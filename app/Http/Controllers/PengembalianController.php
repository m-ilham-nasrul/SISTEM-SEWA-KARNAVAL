<?php
namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\Kostum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengembalianController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $query = Sewa::with(['penyewa.user'])
                ->orderBy('created_at', 'desc');
            if ($user->role === 'penyewa') {
                $query->where('penyewa_id', $user->penyewa->id);
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
                    'kode_sewa' =>
                        $sewa->kode_sewa ??
                        'SEWA-' . str_pad($sewa->id, 4, '0', STR_PAD_LEFT),

                    'penyewa' => [
                        'user' => [
                            'name' => $sewa->penyewa->user->name ?? null
                        ]
                    ],
                    'kostum_list' => $kostums,
                    'tanggal_sewa' => $sewa->tanggal_sewa,
                    'tanggal_kembali' => $sewa->tanggal_kembali,
                    'total_biaya' => $sewa->total_biaya,
                    'denda' => $sewa->denda,
                    'status' => $sewa->status,
                    'status_bayar' => $sewa->status_bayar
                ];
            });
            return response()->json([
                'data' => $data
            ]);
        }
        return view('pages.pengembalian.index');
    }
    /* =============================
       PENYEWA AJUKAN PENGEMBALIAN
    ============================= */
    public function request($id)
    {
    $sewa = Sewa::findOrFail($id);
    if(Auth::user()->role === 'penyewa'){
        if($sewa->penyewa_id != Auth::user()->penyewa->id){
            abort(403);
        }
    }
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
        if(Auth::user()->role !== 'admin'){
        abort(403);
    }
        $sewa = Sewa::findOrFail($id);
        if ($sewa->kostum_id) {
            $ids = json_decode($sewa->kostum_id, true);
            Kostum::whereIn('id', $ids)->update([
                'status' => 0
            ]);
        }
        $sewa->update([
            'status' => 2,
            'denda' => $request->denda ?? 0
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Pengembalian berhasil diverifikasi'
        ]);
    }

    /* =============================
       HAPUS DATA
    ============================= */
    public function hapus($id)
    {
        $sewa = Sewa::findOrFail($id);
        if ($sewa->kostum_id) {
            $ids = json_decode($sewa->kostum_id, true);
            Kostum::whereIn('id', $ids)->update([
                'status' => 0
            ]);
        }
        $sewa->delete();
        return response()->json([
            'status' => true,
            'message' => 'Data pengembalian berhasil dihapus'
        ]);
    }
}