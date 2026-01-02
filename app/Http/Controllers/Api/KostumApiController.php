<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kostum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KostumApiController extends Controller
{
    /**
     * GET /api/kostum
     */
    public function index()
    {
        $data = Kostum::all()->map(function ($kostum) {
            return [
                'id'            => $kostum->id,
                'nama_kostum'   => $kostum->nama_kostum,
                'kategori'      => $kostum->kategori,
                'harga'         => $kostum->harga,
                'catatan'       => $kostum->catatan,
                'status'        => $kostum->status,
                'image_kostum'  => $kostum->image_kostum,
                'sedang_dipakai'=> $kostum->sedangDipakai(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data kostum berhasil diambil',
            'data'    => $data
        ]);
    }

    /**
     * POST /api/kostum
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kostum'  => 'required|string|max:255',
            'kategori'     => 'required|string|max:100',
            'harga'        => 'required|numeric',
            'status'       => 'required|boolean',
            'catatan'      => 'nullable|string',
            'image_kostum' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $kostum = Kostum::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kostum berhasil ditambahkan',
            'data'    => $kostum
        ], 201);
    }

    /**
     * GET /api/kostum/{id}
     */
    public function show($id)
    {
        $kostum = Kostum::find($id);

        if (!$kostum) {
            return response()->json([
                'success' => false,
                'message' => 'Kostum tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id'             => $kostum->id,
                'nama_kostum'    => $kostum->nama_kostum,
                'kategori'       => $kostum->kategori,
                'harga'          => $kostum->harga,
                'catatan'        => $kostum->catatan,
                'status'         => $kostum->status,
                'image_kostum'   => $kostum->image_kostum,
                'sedang_dipakai' => $kostum->sedangDipakai(),
            ]
        ]);
    }

    /**
     * PUT /api/kostum/{id}
     */
    public function update(Request $request, $id)
    {
        $kostum = Kostum::find($id);

        if (!$kostum) {
            return response()->json([
                'success' => false,
                'message' => 'Kostum tidak ditemukan'
            ], 404);
        }

        $kostum->update($request->only([
            'nama_kostum',
            'kategori',
            'harga',
            'status',
            'catatan',
            'image_kostum'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Kostum berhasil diperbarui',
            'data'    => $kostum
        ]);
    }

    /**
     * DELETE /api/kostum/{id}
     */
    public function destroy($id)
    {
        $kostum = Kostum::find($id);

        if (!$kostum) {
            return response()->json([
                'success' => false,
                'message' => 'Kostum tidak ditemukan'
            ], 404);
        }

        if ($kostum->sedangDipakai()) {
            return response()->json([
                'success' => false,
                'message' => 'Kostum sedang disewa dan tidak dapat dihapus'
            ], 400);
        }

        $kostum->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kostum berhasil dihapus'
        ]);
    }
}
