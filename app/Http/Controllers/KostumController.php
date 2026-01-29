<?php

namespace App\Http\Controllers;

use App\Models\Kostum;
use Illuminate\Http\Request;

class KostumController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json([
                'data' => Kostum::orderBy('created_at', 'desc')->get()
            ]);
        }

        return view('pages.kostum.index');
    }

    public function create()
    {
        return view('pages.kostum.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'nama_kostum'  => 'required|string|max:255',
                'kategori'     => 'required|string|max:255',
                'harga'        => 'required|integer',
                'catatan'      => 'nullable|string',
                'image_kostum' => 'nullable|image|mimes:jpg,jpeg,png|max:6048',
            ],
            [
                'image_kostum.max' => 'Ukuran gambar maksimal 6 MB.',
            ]
        );

        $validated['status'] = 0; // TERSEDIA

        if ($request->hasFile('image_kostum')) {
            $image = $request->file('image_kostum');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/kostum'), $filename);
            $validated['image_kostum'] = $filename;
        }

        Kostum::create($validated);

        return redirect()
            ->route('kostum.index')
            ->with('success', 'Kostum berhasil ditambahkan');
    }

    public function show($id)
    {
        $kostum = Kostum::findOrFail($id);
        return view('pages.kostum.show', compact('kostum'));
    }

    public function edit($id)
    {
        $kostum = Kostum::findOrFail($id);
        return view('pages.kostum.edit', compact('kostum'));
    }

    public function update(Request $request, $id)
    {
        $kostum = Kostum::findOrFail($id);

        $validated = $request->validate(
            [
                'nama_kostum'  => 'required|string|max:255',
                'kategori'     => 'required|string|max:255',
                'harga'        => 'required|integer',
                'catatan'      => 'nullable|string',
                'status'       => 'required|in:0,1',
                'image_kostum' => 'nullable|image|mimes:jpg,jpeg,png|max:6048',
            ],
            [
                'image_kostum.max' => 'Ukuran gambar maksimal 6 MB.',
            ]
        );

        if ($request->hasFile('image_kostum')) {

            // hapus gambar lama
            if ($kostum->image_kostum && file_exists(public_path('uploads/kostum/' . $kostum->image_kostum))) {
                unlink(public_path('uploads/kostum/' . $kostum->image_kostum));
            }

            $image = $request->file('image_kostum');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/kostum'), $filename);
            $validated['image_kostum'] = $filename;
        }

        $kostum->update($validated);

        return redirect()
            ->route('kostum.index')
            ->with('success', 'Data kostum berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kostum = Kostum::findOrFail($id);

        if ($kostum->image_kostum && file_exists(public_path('uploads/kostum/' . $kostum->image_kostum))) {
            unlink(public_path('uploads/kostum/' . $kostum->image_kostum));
        }

        $kostum->delete();

        return response()->json([
            'status' => true,
            'message' => 'Kostum berhasil dihapus'
        ]);
    }
}
