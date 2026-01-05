<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SewaController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Data penyewaan berhasil diambil',
            'data' => Sewa::all()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'penyewa_id'      => 'required|integer',
            'kostum_id'       => 'required|array',
            'tanggal_sewa'    => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_sewa',
            'total_biaya'     => 'required|numeric',
            'status'          => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $sewa = Sewa::create([
            'kode_sewa'       => 'SEWA-' . now()->format('YmdHis'),
            'penyewa_id'      => $request->penyewa_id,
            'kostum_id'       => $request->kostum_id,
            'tanggal_sewa'    => $request->tanggal_sewa,
            'tanggal_kembali' => $request->tanggal_kembali,
            'total_biaya'     => $request->total_biaya,
            'status'          => $request->status,
            'status_bayar'    => false,
            'denda'           => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data penyewaan berhasil disimpan',
            'data'    => $sewa
        ], 201);
    }

    public function show($id)
    {
        $sewa = Sewa::find($id);

        if (!$sewa) {
            return response()->json([
                'success' => false,
                'message' => 'Data penyewaan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $sewa
        ]);
    }

    public function update(Request $request, $id)
    {
        $sewa = Sewa::find($id);

        if (!$sewa) {
            return response()->json([
                'success' => false,
                'message' => 'Data penyewaan tidak ditemukan'
            ], 404);
        }

        $sewa->update($request->only([
            'penyewa_id',
            'kostum_id',
            'tanggal_sewa',
            'tanggal_kembali',
            'total_biaya',
            'status',
            'status_bayar',
            'denda'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Data penyewaan berhasil diperbarui',
            'data' => $sewa
        ]);
    }

    public function destroy($id)
    {
        $sewa = Sewa::find($id);

        if (!$sewa) {
            return response()->json([
                'success' => false,
                'message' => 'Data penyewaan tidak ditemukan'
            ], 404);
        }

        $sewa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data penyewaan berhasil dihapus'
        ]);
    }
}
