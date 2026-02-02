<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\Kostum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status_bayar');
        $user = Auth::user();

        $pendapatan_hari = 0;
        $pendapatan_bulan = 0;

        if ($user->role === 'admin') {

            $pendapatan_hari = Sewa::where('status_bayar', 1)
                ->whereBetween('updated_at', [
                    Carbon::today(),
                    Carbon::tomorrow()
                ])
                ->sum(DB::raw('total_biaya + denda'));

            $pendapatan_bulan = Sewa::where('status_bayar', 1)
                ->whereYear('updated_at', now()->year)
                ->whereMonth('updated_at', now()->month)
                ->sum(DB::raw('total_biaya + denda'));
        }

        if ($request->ajax()) {

            $query = Sewa::with('penyewa.user')
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

                $kostums = [];
                if ($sewa->kostum_id) {
                    $ids = json_decode($sewa->kostum_id, true);
                    $kostums = Kostum::whereIn('id', $ids)->get()->map(function ($k) {
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
                    'denda' => $sewa->denda,
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

    public function bayar($id)
    {
        $pengembalian = Sewa::with('penyewa')->findOrFail($id);
        return view('pages.pembayaran.bayar', compact('pengembalian'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'denda' => 'nullable|numeric',
            'total_biaya' => 'required|numeric',
            'metode_pembayaran' => 'required|string',
            'no_rekening' => 'nullable|string'
        ]);

        $sewa = Sewa::findOrFail($id);

        $sewa->update([
            'denda' => $request->denda ?? 0,
            'total_biaya' => $request->total_biaya,
            'metode_pembayaran' => $request->metode_pembayaran,
            'no_rekening' => $request->no_rekening,
            'status_bayar' => 1,
            'updated_at' => now()
        ]);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil diproses.');
    }

    // ================= NOTA =================
    public function nota($id)
    {
        $sewa = Sewa::with('penyewa')->findOrFail($id);

        $kostumIds = json_decode($sewa->kostum_id, true) ?? [];
        $kostums = Kostum::whereIn('id', $kostumIds)->get();

        return view('pages.pembayaran.nota', compact('sewa', 'kostums'));
    }

    public function destroy($id)
    {
        $sewa = Sewa::find($id);

        if (!$sewa) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        if ($sewa->kostum_id) {
            $ids = json_decode($sewa->kostum_id, true);
            Kostum::whereIn('id', $ids)->update(['status' => 0]);
        }

        $sewa->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data pembayaran berhasil dihapus'
        ]);
    }
}
