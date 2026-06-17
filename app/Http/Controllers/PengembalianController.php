<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengembalianController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $query = Sewa::with(['penyewa.user', 'details.kostum'])
                ->orderBy('created_at', 'desc');

            // filter role
            if ($user->role === 'penyewa') {
                if ($user->penyewa) {
                    $query->where(
                        'penyewa_id',
                        $user->penyewa->id
                    );
                } else {
                    return response()->json([
                        'data' => []
                    ]);
                }
            }

            $sewas = $query->get();
            $data = $sewas->map(function ($sewa) {
                return [
                    'id' => $sewa->id,
                    'kode_sewa' => $sewa->kode_sewa
                        ?? 'SEWA-' . str_pad($sewa->id, 4, '0', STR_PAD_LEFT),

                    'penyewa' => [
                        'user' => [
                            'name' => optional($sewa->penyewa->user)->name
                        ]
                    ],

                    // ambil dari detail_sewas
                    'kostum_list' => $sewa->details->map(function ($d) {
                        return [
                            'id' => $d->kostum->id,
                            'nama_kostum' => $d->kostum->nama_kostum
                        ];
                    }),

                    'tanggal_sewa' => $sewa->tanggal_sewa,
                    'tanggal_kembali' => $sewa->tanggal_kembali,
                    'total_biaya' => $sewa->total_biaya,
                    'denda' => $sewa->denda,
                    'kondisi' => $sewa->kondisi,   // TAMBAHAN
                    'catatan' => $sewa->catatan,
                    'status' => $sewa->status,
                    'status_bayar' => $sewa->status_bayar
                ];
            });

            return response()->json(['data' => $data]);
        }

        return view('pages.pengembalian.index');
    }

    /* =============================
       PENYEWA AJUKAN PENGEMBALIAN
    ============================= */
    public function request($id)
    {
        $sewa = Sewa::findOrFail($id);

        if (Auth::user()->role === 'penyewa') {
            if ($sewa->penyewa_id != Auth::user()->penyewa->id) {
                abort(403);
            }
        }

        // status: 1 = diajukan pengembalian
        $sewa->update([
            'status' => 1
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    /* =============================
       ADMIN VERIFIKASI
    ============================= */
    public function verifikasi(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        $request->validate([
            'kondisi' => 'required|in:baik,rusak',
            'denda' => 'required_if:kondisi,rusak|numeric|min:0',
            'catatan' => 'required_if:kondisi,rusak|nullable|string'
        ]);
        $sewa = Sewa::with('details.kostum')->findOrFail($id);
        // kembalikan semua kostum
        foreach ($sewa->details as $detail) {
            $detail->kostum->update([
                'status' => 0
            ]);
        }

        $tanggalKembali = Carbon::parse($sewa->tanggal_kembali);
        $tanggalSekarang = Carbon::now();

        $hariTerlambat = max(
            0,
            $tanggalKembali->diffInDays($tanggalSekarang, false)
        );

        $dendaTerlambat = $hariTerlambat * 10000; // Rp10.000 per hari

        $dendaKerusakan = $request->kondisi === 'rusak'
            ? ($request->denda ?? 0)
            : 0;

        $totalDenda = $dendaTerlambat + $dendaKerusakan;
        $sewa->update([
            'status' => 2,
            'kondisi' => $request->kondisi,
            'denda' => $totalDenda,
            'catatan' => $request->kondisi === 'rusak'
                ? $request->catatan
                : null,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Pengembalian berhasil diverifikasi',
            'hari_terlambat' => $hariTerlambat,
            'denda_terlambat' => $dendaTerlambat,
            'total_denda' => $totalDenda,
        ]);
    }

    /* =============================
       HAPUS DATA
    ============================= */
    public function hapus($id)
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
            'message' => 'Data pengembalian berhasil dihapus'
        ]);
    }
}
