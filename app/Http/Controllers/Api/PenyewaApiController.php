<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Penyewa;
use Illuminate\Http\Request;

class PenyewaApiController extends Controller
{
    /**
     * GET /api/penyewa
     * Ambil semua penyewa
     */
    public function index()
    {
        $penyewas = Penyewa::with('user')->latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Data Penyewa berhasil diambil',
            'data' => $penyewas
        ]);
    }

    /**
     * POST /api/penyewa
     * Simpan penyewa baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'alamat'  => 'required|string|max:255',
            'no_telp' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Optional: cegah satu user punya lebih dari satu penyewa
        if (Penyewa::where('user_id', $request->user_id)->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'User sudah terdaftar sebagai penyewa'
            ], 409);
        }

        $penyewa = Penyewa::create([
            'user_id' => $request->user_id,
            'alamat'  => $request->alamat,
            'no_telp' => $request->no_telp,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Penyewa berhasil ditambahkan',
            'data'    => $penyewa
        ], 201);
    }

    /**
     * GET /api/penyewa/{id}
     * Detail penyewa
     */
    public function show($id)
    {
        $penyewa = Penyewa::with(['user', 'sewas'])->find($id);

        if (!$penyewa) {
            return response()->json([
                'status' => false,
                'message' => 'Penyewa tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $penyewa
        ]);
    }

    /**
     * PUT /api/penyewa/{id}
     * Update penyewa
     */
    public function update(Request $request, $id)
    {
        $penyewa = Penyewa::find($id);

        if (!$penyewa) {
            return response()->json([
                'status'  => false,
                'message' => 'Penyewa tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'alamat'  => 'sometimes|required|string|max:255',
            'no_telp' => 'sometimes|required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $penyewa->update($request->only(['alamat', 'no_telp']));

        return response()->json([
            'status'  => true,
            'message' => 'Penyewa berhasil diperbarui',
            'data'    => $penyewa
        ]);
    }

    /**
     * DELETE /api/penyewa/{id}
     * Hapus penyewa
     */
    public function destroy($id)
    {
        $penyewa = Penyewa::find($id);

        if (!$penyewa) {
            return response()->json([
                'status' => false,
                'message' => 'Penyewa tidak ditemukan'
            ], 404);
        }

        $penyewa->delete();

        return response()->json([
            'status' => true,
            'message' => 'Penyewa berhasil dihapus'
        ]);
    }
}
