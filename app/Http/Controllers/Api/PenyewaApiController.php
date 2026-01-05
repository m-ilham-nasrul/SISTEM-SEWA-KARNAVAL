<?php

namespace App\Http\Controllers\Api;

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
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'alamat'  => 'required|string',
            'no_telp' => 'required|string|max:20',
        ]);

        $penyewa = Penyewa::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Penyewa berhasil ditambahkan',
            'data' => $penyewa
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
                'status' => false,
                'message' => 'Penyewa tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'alamat'  => 'required|string',
            'no_telp' => 'required|string|max:20',
        ]);

        $penyewa->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Penyewa berhasil diperbarui',
            'data' => $penyewa
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
