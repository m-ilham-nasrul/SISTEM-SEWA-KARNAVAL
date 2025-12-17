<?php

namespace App\Http\Controllers;

use App\Models\Kostum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KostumController extends Controller
{
    /**
     * INDEX
     * - AJAX → DataTables (JSON)
     * - Normal → Blade
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json([
                'data' => Kostum::orderBy('created_at', 'desc')->get()
            ]);
        }

        return view('pages.kostum.index');
    }

    /**
     * FORM TAMBAH (VIEW)
     */
    public function create()
    {
        return view('pages.kostum.create');
    }

    /**
     * SIMPAN DATA (AJAX / FORM)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kostum'  => 'required|string|max:255',
            'kategori'     => 'required|string|max:255',
            'harga'        => 'required|integer',
            'catatan'      => 'nullable|string',
            'image_kostum' => 'nullable|image|mimes:jpg,jpeg,png|max:6048',
        ]);

        $validated['status'] = 0; // TERSEDIA

        if ($request->hasFile('image_kostum')) {
            $validated['image_kostum'] =
                $request->file('image_kostum')->store('kostum', 'public');
        }

        Kostum::create($validated);

        return redirect()
            ->route('kostum.index')
            ->with('success', 'Kostum berhasil ditambahkan');
    }


    /**
     * DETAIL (VIEW)
     */
    public function show($id)
    {
        $kostum = Kostum::findOrFail($id);
        return view('pages.kostum.show', compact('kostum'));
    }

    /**
     * FORM EDIT (VIEW)
     */
    public function edit($id)
    {
        $kostum = Kostum::findOrFail($id);
        return view('pages.kostum.edit', compact('kostum'));
    }

    /**
     * UPDATE DATA
     */
    public function update(Request $request, $id)
    {
        $kostum = Kostum::findOrFail($id);

        $validated = $request->validate([
            'nama_kostum'  => 'required|string|max:255',
            'kategori'     => 'required|string|max:255',
            'harga'        => 'required|integer',
            'catatan'      => 'nullable|string',
            'status'       => 'required|in:0,1',
            'image_kostum' => 'nullable|image|mimes:jpg,jpeg,png|max:6048',
        ]);

        if ($request->hasFile('image_kostum')) {
            if ($kostum->image_kostum) {
                Storage::disk('public')->delete($kostum->image_kostum);
            }

            $validated['image_kostum'] =
                $request->file('image_kostum')->store('kostum', 'public');
        }

        $kostum->update($validated);

        return redirect()
            ->route('kostum.index')
            ->with('success', 'Data kostum berhasil diperbarui');
    }

    /**
     * HAPUS DATA (AJAX)
     */
    public function destroy($id)
    {
        $kostum = Kostum::findOrFail($id);

        if ($kostum->image_kostum) {
            Storage::disk('public')->delete($kostum->image_kostum);
        }

        $kostum->delete();

        return response()->json([
            'status' => true,
            'message' => 'Kostum berhasil dihapus'
        ]);
    }
}
