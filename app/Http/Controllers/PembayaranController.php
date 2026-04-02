<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;

/* MIDTRANS */
use Midtrans\Config;
use Midtrans\Snap;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status_bayar');
        $user = Auth::user();

        $pendapatan_hari = 0;
        $pendapatan_bulan = 0;

        /* ================= PENDAPATAN ADMIN ================= */
        if ($user->role === 'admin') {

            $pendapatan_hari = Sewa::where('status_bayar', 1)
                ->whereDate('updated_at', Carbon::today())
                ->sum(DB::raw('total_biaya + denda'));

            $pendapatan_bulan = Sewa::where('status_bayar', 1)
                ->whereYear('updated_at', now()->year)
                ->whereMonth('updated_at', now()->month)
                ->sum(DB::raw('total_biaya + denda'));
        }

        /* ================= AJAX ================= */
        if ($request->ajax()) {

            $query = Sewa::with(['penyewa.user', 'details.kostum'])
                ->orderBy('updated_at', 'desc');

            if ($user->role === 'penyewa') {
                $query->where('penyewa_id', $user->penyewa->id);
            }

            if ($status === '1') {
                $query->where('status_bayar', 1);
            } elseif ($status === '0') {
                $query->where('status_bayar', 0);
            }

            $sewas = $query->get();

            $data = $sewas->map(function ($sewa) {
                return [
                    'id' => $sewa->id,
                    'kode_sewa' => $sewa->kode_sewa,
                    'penyewa' => [
                        'user' => [
                            'name' => optional($sewa->penyewa->user)->name
                        ]
                    ],
                    'kostum_list' => $sewa->details->map(function ($d) {
                        return [
                            'id' => $d->kostum->id,
                            'nama_kostum' => $d->kostum->nama_kostum
                        ];
                    }),
                    'tanggal_sewa' => $sewa->tanggal_sewa,
                    'tanggal_kembali' => $sewa->tanggal_kembali,
                    'denda' => $sewa->denda ?? 0,
                    'total_biaya' => $sewa->total_biaya,
                    'status' => $sewa->status,
                    'status_bayar' => $sewa->status_bayar,
                ];
            });

            return response()->json(['data' => $data]);
        }

        $statusTitle = $status === '1'
            ? 'Terbayar'
            : ($status === '0' ? 'Menunggu Pembayaran' : '');

        return view('pages.pembayaran.index', compact(
            'statusTitle',
            'pendapatan_hari',
            'pendapatan_bulan'
        ));
    }

    /* ================= HALAMAN BAYAR ================= */
    public function bayar($id)
    {
        $sewa = Sewa::with(['penyewa.user', 'details.kostum'])->findOrFail($id);

        if (Auth::user()->role == 'penyewa') {
            if ($sewa->penyewa_id != Auth::user()->penyewa->id) {
                abort(403);
            }
        }

        if ($sewa->status != 2) {
            return redirect()->back()
                ->with('error', 'Pengembalian belum diverifikasi admin.');
        }

        if ($sewa->status_bayar == 1) {
            return redirect()->route('pembayaran.index')
                ->with('success', 'Transaksi sudah dibayar.');
        }

        $sewa->kostum_list = $sewa->details->map(function ($d) {
            return $d->kostum;
        });

        return view('pages.pembayaran.bayar', compact('sewa'));
    }

    /* ================= MIDTRANS ================= */
    public function snapToken($id)
    {
        $sewa = Sewa::with(['penyewa.user', 'details.kostum'])->findOrFail($id);

        // ================= AUTH CHECK =================
        if (Auth::user()->role === 'penyewa') {
            if ($sewa->penyewa_id !== Auth::user()->penyewa->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        // ================= CEK STATUS BAYAR =================
        if ($sewa->status_bayar == 1) {
            return response()->json([
                'status' => false,
                'message' => 'Sudah dibayar'
            ]);
        }

        // ================= CEK VERIFIKASI SEWA =================
        if ($sewa->status != 2) {
            return response()->json([
                'status' => false,
                'message' => 'Belum diverifikasi'
            ]);
        }

        // ================= CEK TOKEN LAMA =================
        // Token hanya valid jika dibuat < 24 jam yang lalu
        if ($sewa->snap_token && $sewa->snap_token_created_at) {
            $hours = $sewa->snap_token_created_at->diffInHours(now());
            if ($hours < 24) {
                return response()->json([
                    'status' => true,
                    'snap_token' => $sewa->snap_token
                ]);
            }
        }

        // ================= MIDTRANS CONFIG =================
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // ================= ORDER ID =================
        $orderId = $sewa->midtrans_order_id ?? 'SEWA-' . $sewa->id . '-' . time();
        $sewa->update([
            'midtrans_order_id' => $orderId
        ]);

        // ================= ITEM DETAILS =================
        $item_details = [];
        $subtotal = (int) $sewa->total_biaya;

        foreach ($sewa->details as $d) {
            $item_details[] = [
                'id' => $d->kostum->id,
                'price' => (int) $d->harga,
                'quantity' => (int) $d->qty,
                'name' => $d->kostum->nama_kostum
            ];
        }

        $denda = (int) ($sewa->denda ?? 0);
        if ($denda > 0) {
            $item_details[] = [
                'id' => 'DENDA',
                'price' => $denda,
                'quantity' => 1,
                'name' => 'Denda/Kerusakan'
            ];
        }

        // ================= CUSTOMER DETAILS =================
        $phone = preg_replace('/[^0-9]/', '', optional($sewa->penyewa)->no_telp ?? '');
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $subtotal + $denda
            ],
            'customer_details' => [
                'first_name' => optional($sewa->penyewa->user)->name,
                'email' => optional($sewa->penyewa->user)->email ?? 'customer@email.com',
                'phone' => $phone,
                'billing_address' => [
                    'address' => optional($sewa->penyewa)->alamat ?? '-'
                ],
                'shipping_address' => [
                    'address' => optional($sewa->penyewa)->alamat ?? '-'
                ]
            ],
            'item_details' => $item_details,
            'callbacks' => [
                'finish' => url('/pembayaran?status=success')
            ]
        ];

        try {
            // ================= CEK TOKEN LAMA =================
            // Token hanya valid jika dibuat < 24 jam yang lalu
            if ($sewa->snap_token && $sewa->snap_token_created_at) {
                $hours = $sewa->snap_token_created_at->diffInHours(now());
                if ($hours < 24) {
                    // Gunakan token lama
                    return response()->json([
                        'status' => true,
                        'snap_token' => $sewa->snap_token
                    ]);
                }
            }

            // Hanya generate token baru kalau token lama expired
            $snapToken = Snap::getSnapToken($params);

            // Update database dengan token baru
            $sewa->update([
                'snap_token' => $snapToken,
                'snap_token_created_at' => now()
            ]);

            return response()->json([
                'status' => true,
                'snap_token' => $snapToken
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal generate Snap Token: ' . $e->getMessage()
            ], 500);
        }
    }


    /* ================= NOTA ================= */
    public function nota($id)
    {
        $sewa = Sewa::with('details.kostum')->findOrFail($id);

        $kostums = $sewa->details->map(function ($d) {
            return $d->kostum;
        });

        return view('pages.pembayaran.nota', compact('sewa', 'kostums'));
    }

    /* ================= DELETE ================= */
    public function destroy($id)
    {
        $sewa = Sewa::with('details.kostum')->findOrFail($id);

        foreach ($sewa->details as $detail) {
            $detail->kostum->update([
                'status' => 0
            ]);
        }

        $sewa->details()->delete();
        $sewa->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data pembayaran berhasil dihapus'
        ]);
    }

    public function notification(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);

        try {

            $notif = new Notification();

            $orderId           = $notif->order_id;
            $statusCode        = $notif->status_code;
            $grossAmount       = $notif->gross_amount;
            $transactionStatus = $notif->transaction_status;
            $fraudStatus       = $notif->fraud_status ?? null;

            // =============================
            // VALIDASI SIGNATURE (WAJIB)
            // =============================
            $signatureKey = hash(
                'sha512',
                $orderId . $statusCode . $grossAmount . config('midtrans.server_key')
            );

            if ($signatureKey !== $notif->signature_key) {
                Log::warning('Invalid signature dari Midtrans');
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            // =============================
            // CARI DATA SEWA
            // =============================
            $sewa = Sewa::where('midtrans_order_id', $orderId)->first();

            if (!$sewa) {
                Log::warning('Order tidak ditemukan: ' . $orderId);
                return response()->json(['message' => 'Order not found'], 404);
            }

            // =============================
            // HANDLE STATUS
            // =============================

            if (
                ($transactionStatus == 'capture' && $fraudStatus == 'accept') ||
                $transactionStatus == 'settlement'
            ) {
                $sewa->update([
                    'status_bayar' => 1,
                    'transaction_status' => $transactionStatus,
                    'payment_type' => $notif->payment_type,
                    'snap_token' => null
                ]);
            } elseif ($transactionStatus == 'pending') {
                $sewa->update([
                    'status_bayar' => 0
                ]);
            } elseif (
                $transactionStatus == 'deny' ||
                $transactionStatus == 'expire' ||
                $transactionStatus == 'cancel'
            ) {
                $sewa->update([
                    'status_bayar' => 0
                ]);
            }

            Log::info('Midtrans masuk', [
                'order_id' => $orderId,
                'status' => $transactionStatus
            ]);

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
