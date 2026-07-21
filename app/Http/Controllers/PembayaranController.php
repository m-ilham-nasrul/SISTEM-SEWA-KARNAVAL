<?php

namespace App\Http\Controllers;

use App\Enums\StatusBayar;

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

            $pendapatan_hari = Sewa::where('status_bayar', StatusBayar::PAID)
                ->whereDate('updated_at', Carbon::today())
                ->sum(DB::raw('total_biaya + denda'));

            $pendapatan_bulan = Sewa::where('status_bayar', StatusBayar::PAID)
                ->whereYear('updated_at', now()->year)
                ->whereMonth('updated_at', now()->month)
                ->sum(DB::raw('total_biaya + denda'));
        }

        /* ================= AJAX ================= */
        if ($request->ajax()) {
            $query = Sewa::with(['penyewa.user', 'details.kostum'])
                ->orderBy('updated_at', 'desc');
            if ($user->role === 'penyewa') {
                if ($user->penyewa) {
                    $query->where('penyewa_id', $user->penyewa->id);
                } else {
                    return response()->json([
                        'data' => []
                    ]);
                }
            }
            if ($status === 'paid') {
                $query->where('status_bayar', StatusBayar::PAID);
            } elseif ($status === 'pending') {
                $query->where('status_bayar', StatusBayar::PENDING);
            } elseif ($status === 'dp_paid') {
                $query->where('status_bayar', StatusBayar::DP_PAID);
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
                            'id' => optional($d->kostum)->id,
                            'nama_kostum' => optional($d->kostum)->nama_kostum ?? 'Kostum telah dihapus'
                        ];
                    }),
                    'tanggal_sewa' => $sewa->tanggal_sewa,
                    'tanggal_kembali' => $sewa->tanggal_kembali,
                    'denda' => $sewa->denda ?? 0,
                    'total_biaya' => $sewa->total_biaya,
                    'metode_pembayaran' => $sewa->metode_pembayaran,
                    'status' => $sewa->status,
                    'status_bayar' => $sewa->status_bayar,
                ];
            });

            return response()->json(['data' => $data]);
        }

        $statusTitle = $status === 'paid'
            ? 'Terbayar'
            : ($status === 'pending' ? 'Menunggu DP' : ($status === 'dp_paid' ? 'DP Dibayar' : ''));

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

        if (
            $sewa->status_bayar === StatusBayar::PAID &&
            !($sewa->status == 2 && $sewa->denda > 0)
        ) {
            return redirect()->route('pembayaran.index')
                ->with('success', 'Transaksi sudah lunas.');
        }

        // Jika mau pelunasan harus sudah diverifikasi admin
        if (
            $sewa->status_bayar === StatusBayar::DP_PAID &&
            $sewa->status != 2
        ) {
            return redirect()->back()
                ->with('error', 'Pengembalian belum diverifikasi admin.');
        }

        $sewa->kostum_list = $sewa->details->map(function ($d) {
            return $d->kostum;
        });

        return view('pages.pembayaran.bayar', compact('sewa'));
    }

    /* ================= MIDTRANS DP ================= */
    public function snapTokenDp($id)
    {
        $sewa = Sewa::with(['penyewa.user'])->findOrFail($id);

        // AUTH CHECK
        if (Auth::check() && Auth::user()->role === 'penyewa') {

            $penyewaId = optional(Auth::user()->penyewa)->id;

            if (!$penyewaId || $sewa->penyewa_id != $penyewaId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        // CEK STATUS BAYAR
        if ($sewa->status_bayar !== StatusBayar::PENDING) {
            return response()->json([
                'status' => false,
                'message' => 'DP sudah dibayar',
                'status_bayar' => $sewa->status_bayar
            ]);
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = 'DP-' . $sewa->id . '-' . time();

        $phone = preg_replace(
            '/[^0-9]/',
            '',
            optional($sewa->penyewa)->no_telp ?? ''
        );

        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        $dpAmount = (int) round($sewa->total_biaya * 0.5);

        $sewa->update([
            'dp' => $dpAmount,
            'sisa_bayar' => $sewa->total_biaya - $dpAmount
        ]);

        // Validate DP amount
        if ($dpAmount < 1) {
            return response()->json([
                'status' => false,
                'message' => 'Jumlah DP tidak valid. Minimal harus Rp 1'
            ], 400);
        }

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $dpAmount
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

            'item_details' => [
                [
                    'id' => 'DP',
                    'price' => (int)  $dpAmount,
                    'quantity' => 1,
                    'name' => 'Pembayaran DP Penyewaan'
                ]
            ],

            'callbacks' => [
                'finish' => url('/penyewaan')
            ]
        ];

        try {

            $snapToken = Snap::getSnapToken($params);

            $sewa->update([
                'midtrans_order_id_dp' => $orderId,
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
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /* ================= MIDTRANS LUNAS ================= */
    public function snapTokenLunas($id)
    {
        $sewa = Sewa::with(['penyewa.user'])->findOrFail($id);

        // ================= AUTH =================
        if (Auth::check() && Auth::user()->role === 'penyewa') {

            $penyewaId = optional(Auth::user()->penyewa)->id;

            if (!$penyewaId || $sewa->penyewa_id != $penyewaId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        // ================= VALIDASI =================
        if ($sewa->status_bayar !== StatusBayar::PENDING) {

            return response()->json([
                'status' => false,
                'message' => 'Transaksi sudah dibayar'
            ]);
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = 'LUNAS-' . $sewa->id . '-' . time();

        $phone = preg_replace(
            '/[^0-9]/',
            '',
            optional($sewa->penyewa)->no_telp ?? ''
        );

        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        $totalBayar = (int) $sewa->total_biaya;

        if ($totalBayar < 1) {
            return response()->json([
                'status' => false,
                'message' => 'Jumlah pembayaran tidak valid.'
            ], 400);
        }

        $params = [

            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $totalBayar
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

            'item_details' => [
                [
                    'id' => 'LUNAS',
                    'price' => $totalBayar,
                    'quantity' => 1,
                    'name' => 'Pembayaran Lunas Penyewaan'
                ]
            ],

            'callbacks' => [
                'finish' => url('/penyewaan')
            ]
        ];

        try {

            $snapToken = Snap::getSnapToken($params);

            $sewa->update([
                'midtrans_order_id_lunas' => $orderId,
                'snap_token' => $snapToken,
                'snap_token_created_at' => now(),
                'dp' => 0,
                'sisa_bayar' => 0
            ]);

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

    /* ================= MIDTRANS Pelunasan ================= */
    public function snapTokenPelunasan($id)
    {
        $sewa = Sewa::with(['penyewa.user'])->findOrFail($id);

        // AUTH CHECK
        if (Auth::check() && Auth::user()->role === 'penyewa') {

            $penyewaId = optional(Auth::user()->penyewa)->id;

            if (!$penyewaId || $sewa->penyewa_id != $penyewaId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        // VALIDASI STATUS BAYAR
        if (
            $sewa->status_bayar !== StatusBayar::DP_PAID &&
            $sewa->status_bayar !== StatusBayar::PAID
        ) {

            return response()->json([
                'status' => false,
                'message' => 'Status pembayaran tidak valid'
            ]);
        }

        // HARUS SUDAH DIVERIFIKASI ADMIN
        if ($sewa->status != 2) {
            return response()->json([
                'status' => false,
                'message' => 'Pengembalian belum diverifikasi admin'
            ]);
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = 'PELUNASAN-' . $sewa->id . '-' . time();

        $phone = preg_replace(
            '/[^0-9]/',
            '',
            optional($sewa->penyewa)->no_telp ?? ''
        );

        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        if ($sewa->status_bayar == StatusBayar::DP_PAID) {

            $sisaBayar = max(0, $sewa->total_biaya - $sewa->dp);
        } else {

            // Sudah bayar lunas
            $sisaBayar = 0;
        }

        $sewa->update([
            'sisa_bayar' => $sisaBayar
        ]);

        $totalPelunasan = $sisaBayar + $sewa->denda;
        // Validasi total pelunasan
        if ($totalPelunasan <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada tagihan pelunasan.'
            ]);
        }
        // Validate total amount
        if ($totalPelunasan < 1) {
            return response()->json([
                'status' => false,
                'message' => 'Jumlah pelunasan tidak valid. Minimal harus Rp 1'
            ], 400);
        }

        $itemDetails = [];

        if ($sisaBayar > 0) {

            $itemDetails[] = [
                'id' => 'PELUNASAN',
                'price' => (int)$sisaBayar,
                'quantity' => 1,
                'name' => 'Pelunasan Penyewaan'
            ];
        }

        if ($sewa->denda > 0) {

            $itemDetails[] = [
                'id' => 'DENDA',
                'price' => (int)$sewa->denda,
                'quantity' => 1,
                'name' => 'Denda'
            ];
        }

        $params = [

            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $totalPelunasan
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
            'item_details' => $itemDetails,

            'callbacks' => [
                'finish' => url('/penyewaan')
            ]
        ];

        try {

            $snapToken = Snap::getSnapToken($params);

            $sewa->update([
                'midtrans_order_id_pelunasan' => $orderId,
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
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /* ================= NOTIFICATION ================= */
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
            // VALIDASI SIGNATURE
            // =============================
            $signatureKey = hash(
                'sha512',
                $orderId . $statusCode . $grossAmount . config('midtrans.server_key')
            );
            if ($signatureKey !== $notif->signature_key) {
                Log::warning('Invalid signature Midtrans');
                return response()->json([
                    'message' => 'Invalid signature'
                ], 403);
            }
            // =============================
            // CARI DATA SEWA
            // =============================
            $sewa = Sewa::where('midtrans_order_id_dp', $orderId)
                ->orWhere('midtrans_order_id_lunas', $orderId)
                ->orWhere('midtrans_order_id_pelunasan', $orderId)
                ->first();

            if (!$sewa) {
                Log::warning('Order tidak ditemukan : ' . $orderId);

                return response()->json([
                    'message' => 'Order not found'
                ], 404);
            }
            // =============================
            // PEMBAYARAN BERHASIL
            // =============================
            if (
                ($transactionStatus == 'capture' && $fraudStatus == 'accept') ||
                $transactionStatus == 'settlement'
            ) {

                // ===== PEMBAYARAN DP =====
                if (str_starts_with($orderId, 'DP-')) {

                    $sisaBayar = max(0, $sewa->total_biaya - $sewa->dp);

                    $sewa->update([
                        'status_bayar' => StatusBayar::DP_PAID,
                        'sisa_bayar' => $sisaBayar,
                        'transaction_status' => $transactionStatus,
                        'payment_type' => $notif->payment_type,
                        'snap_token' => null,
                    ]);
                }
                // ===== PEMBAYARAN LUNAS =====
                if (str_starts_with($orderId, 'LUNAS-')) {

                    $sewa->update([
                        'status_bayar'       => StatusBayar::PAID,
                        'status'             => 0, // masih masa sewa
                        'dp'                 => 0,
                        'sisa_bayar'         => 0,
                        'transaction_status' => $transactionStatus,
                        'payment_type'       => $notif->payment_type,
                        'snap_token'         => null
                    ]);
                }
                // ===== PELUNASAN =====
                if (str_starts_with($orderId, 'PELUNASAN-')) {

                    $sewa->update([
                        'status_bayar'      => StatusBayar::PAID,
                        'status'            => 3, // selesai
                        'sisa_bayar'        => 0,
                        'transaction_status' => $transactionStatus,
                        'payment_type'      => $notif->payment_type,
                        'snap_token'        => null
                    ]);
                }
            }
            // =============================
            // MENUNGGU PEMBAYARAN
            // =============================
            elseif ($transactionStatus == 'pending') {

                $sewa->update([
                    'transaction_status' => 'pending'
                ]);
            }
            // =============================
            // GAGAL / EXPIRED / CANCEL
            // =============================
            elseif (
                $transactionStatus == 'deny' ||
                $transactionStatus == 'expire' ||
                $transactionStatus == 'cancel'
            ) {
                $sewa->update([
                    'transaction_status' => $transactionStatus
                ]);
            }
            Log::info('Midtrans Notification', [
                'order_id' => $orderId,
                'status'   => $transactionStatus
            ]);
            return response()->json([
                'status' => 'ok'
            ]);
        } catch (\Exception $e) {

            Log::error('Midtrans Error : ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
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
            if ($detail->kostum) {
                $detail->kostum->update([
                    'status' => 0
                ]);
            }
        }
        $sewa->delete();
        return response()->json([
            'status' => true,
            'message' => 'Data pembayaran berhasil dihapus'
        ]);
    }
}
