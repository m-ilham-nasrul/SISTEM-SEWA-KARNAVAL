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

        if (Auth::user()->role === 'penyewa') {
            if ($sewa->penyewa_id !== Auth::user()->penyewa->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        if ($sewa->status_bayar == 1) {
            return response()->json([
                'status' => false,
                'message' => 'Sudah dibayar'
            ]);
        }

        if ($sewa->status != 2) {
            return response()->json([
                'status' => false,
                'message' => 'Belum diverifikasi'
            ]);
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        if ($sewa->midtrans_order_id) {
            $orderId = $sewa->midtrans_order_id;
        } else {
            $orderId = 'SEWA-' . $sewa->id . '-' . time();
        }

        $sewa->update([
            'midtrans_order_id' => $orderId
        ]);

        $item_details = [];
        $subtotal = 0;

        $subtotal = 0;

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
            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'status' => true,
                'snap_token' => $snapToken
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
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

        $transactionStatus = $notif->transaction_status;
        $paymentType       = $notif->payment_type;
        $orderId           = $notif->order_id;
        $fraudStatus       = $notif->fraud_status ?? null;

        // Ambil data sewa berdasarkan order_id
        $sewa = Sewa::where('midtrans_order_id', $orderId)->first();

        if (!$sewa) {
            Log::warning('Order tidak ditemukan: ' . $orderId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // ===============================
        // HANDLE STATUS PEMBAYARAN
        // ===============================

        // ✅ SUCCESS
        if (
            $transactionStatus == 'capture' && $fraudStatus == 'accept' ||
            $transactionStatus == 'settlement'
        ) {
            $sewa->update([
                'status_bayar' => 1 // LUNAS
            ]);
        }

        // ⏳ PENDING
        else if ($transactionStatus == 'pending') {
            $sewa->update([
                'status_bayar' => 0 // BELUM BAYAR
            ]);
        }

        // ❌ GAGAL / EXPIRE / CANCEL
        else if (
            $transactionStatus == 'deny' ||
            $transactionStatus == 'expire' ||
            $transactionStatus == 'cancel'
        ) {
            $sewa->update([
                'status_bayar' => 0
            ]);
        }

        // Logging untuk debugging
        Log::info('Midtrans Notification', [
            'order_id' => $orderId,
            'status' => $transactionStatus,
            'payment_type' => $paymentType
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
