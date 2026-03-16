<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\Kostum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        /* ================= AJAX DATATABLE ================= */

        if ($request->ajax()) {

            $query = Sewa::with('penyewa.user')
                ->orderBy('updated_at', 'desc');

            /* hanya data milik penyewa */
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

                $kostums = [];

                if ($sewa->kostum_id) {

                    $ids = json_decode($sewa->kostum_id, true);

                    $kostums = Kostum::whereIn('id', $ids)
                        ->get()
                        ->map(function ($k) {
                            return [
                                'id' => $k->id,
                                'nama_kostum' => $k->nama_kostum
                            ];
                        });
                }

                return [
                    'id' => $sewa->id,
                    'kode_sewa' => $sewa->kode_sewa,
                    'penyewa' => [
                        'user' => [
                            'name' => optional($sewa->penyewa->user)->name
                        ]
                    ],
                    'kostum_list' => $kostums,
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
    $pengembalian = Sewa::with(['penyewa.user'])->findOrFail($id);

    if(Auth::user()->role == 'penyewa'){
        if($pengembalian->penyewa_id != Auth::user()->penyewa->id){
            abort(403);
        }
    }
    /* VALIDASI */
    if ($pengembalian->status != 2) {
        return redirect()->back()
            ->with('error', 'Pengembalian belum diverifikasi admin.');
    }
    if ($pengembalian->status_bayar == 1) {
        return redirect()->route('pembayaran.index')
            ->with('success', 'Transaksi sudah dibayar.');
    }
    if ($pengembalian->kostum_id) {
        $ids = json_decode($pengembalian->kostum_id, true);
        $pengembalian->kostum_list = Kostum::whereIn('id', $ids)->get();
    } else {
        $pengembalian->kostum_list = collect();
    }
    return view('pages.pembayaran.bayar', compact('pengembalian'));
    }


    /* ================= MIDTRANS SNAP TOKEN ================= */
    public function snapToken($id)
{
    $sewa = Sewa::with(['penyewa.user'])->findOrFail($id);

    /* ===== VALIDASI KEPEMILIKAN (PENYEWA) ===== */
    if (Auth::user()->role === 'penyewa') {
        if ($sewa->penyewa_id !== Auth::user()->penyewa->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
    }

    /* ===== CEK SUDAH DIBAYAR ===== */
    if ($sewa->status_bayar == 1) {
        return response()->json([
            'status' => false,
            'message' => 'Transaksi sudah dibayar'
        ]);
    }

    /* ===== MIDTRANS CONFIG ===== */
    Config::$serverKey    = config('midtrans.server_key');
    Config::$isProduction = config('midtrans.is_production', false);
    Config::$isSanitized  = true;
    Config::$is3ds        = true;

    /* ===== GENERATE ORDER ID ===== */
    $orderId = 'SEWA-' . $sewa->id . '-' . time();

    $sewa->update([
        'midtrans_order_id' => $orderId
    ]);

    /* ===== AMBIL DATA KOSTUM ===== */
    $kostumIds = json_decode($sewa->kostum_id, true) ?? [];
    $kostums   = Kostum::whereIn('id', $kostumIds)->get();

    $item_details = [];
    $subtotal = 0;

    foreach ($kostums as $k) {

        $item_details[] = [
            'id'       => $k->id,
            'price'    => (int) $k->harga,
            'quantity' => 1,
            'name'     => $k->nama_kostum
        ];

        $subtotal += $k->harga;
    }

    /* ===== TAMBAH DENDA JIKA ADA ===== */
    $denda = (int) ($sewa->denda ?? 0);

    if ($denda > 0) {
        $item_details[] = [
            'id'       => 'DENDA',
            'price'    => $denda,
            'quantity' => 1,
            'name'     => 'Denda Keterlambatan / Kerusakan'
        ];
    }

    $grossAmount = $subtotal + $denda;

    /* ===== PARAMETER MIDTRANS ===== */
    $params = [
        'transaction_details' => [
            'order_id'     => $orderId,
            'gross_amount' => $grossAmount
        ],

        'customer_details' => [
            'first_name' => optional($sewa->penyewa->user)->name,
            'email'      => optional($sewa->penyewa->user)->email ?? 'customer@email.com'
        ],

        'item_details' => $item_details
    ];

    /* ===== SNAP TOKEN ===== */
    try {

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'status'     => true,
            'snap_token' => $snapToken
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'status'  => false,
            'message' => 'Gagal membuat transaksi Midtrans',
            'error'   => $e->getMessage()
        ], 500);
    }
}

/* ================= Update ================= */
    public function updateStatus(Request $request)
{
    $sewa = Sewa::findOrFail($request->id);

    $sewa->update([
        'status_bayar' => 1
    ]);

    return response()->json([
        'status' => true
    ]);
}

    /* ================= NOTA ================= */
    public function nota($id)
    {
        $sewa = Sewa::with('penyewa')->findOrFail($id);
        $kostumIds = json_decode($sewa->kostum_id, true) ?? [];
        $kostums = Kostum::whereIn('id', $kostumIds)->get();
        return view('pages.pembayaran.nota', compact('sewa', 'kostums'));
    }

    /* ================= DELETE ================= */
    public function destroy($id)
    {
        $sewa = Sewa::findOrFail($id);
        if ($sewa->kostum_id) {
            $ids = json_decode($sewa->kostum_id, true);
            Kostum::whereIn('id', $ids)
                ->update(['status' => 0]);
        }
        $sewa->delete();
        return response()->json([
            'status' => true,
            'message' => 'Data pembayaran berhasil dihapus'
        ]);
    }
}