<?php

namespace App\Http\Controllers;

use App\Models\Kostum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KostumController extends Controller
{

    public function index()
    {
        // Ambil semua data kostum dengan urutan terbaru
        $kostumList = Kostum::orderBy('created_at', 'desc')->get();

        // Kirim ke halaman index
        return view('pages.kostum.index', [
            'kostums' => $kostumList
        ]);
    }

    /**
     * Menampilkan halaman form tambah data kostum baru.
     */
    public function create()
    {
        return view('pages.kostum.create');
    }

    public function store(Request $request)
    {
        // 1. VALIDASI INPUT
        $validatedData = $request->validate([
            'nama_kostum'  => 'required|string|max:255',
            'kategori'     => 'required|string|max:255',
            'harga'        => 'required|integer',
            'catatan'      => 'nullable|string',
            'image_kostum' => 'nullable|image|mimes:jpg,jpeg,png|max:6048',
        ]);

        // 2. TAMBAHKAN STATUS DEFAULT
        $validatedData['status'] = $request->input('status') ?? 0;

        // 3. PROSES UPLOAD GAMBAR
        if ($request->hasFile('image_kostum')) {

            $file = $request->file('image_kostum');              // ambil file
            $folder = 'kostum';                                  // tentukan folder
            $disk = 'public';                                    // tentukan disk penyimpanan

            // simpan file ke storage/app/public/kostum
            $uploadedPath = $file->store($folder, $disk);

            // masukkan nama file ke database
            $validatedData['image_kostum'] = $uploadedPath;
        }

        // 4. SIMPAN KE DATABASE
        Kostum::create($validatedData);

        // 5. KEMBALIKAN NOTIFIKASI
        return redirect()
            ->route('kostum.index')
            ->with('success', 'Kostum berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail data kostum tertentu.
     */
    public function show($id)
    {
        // Ambil data berdasarkan ID
        $kostum = Kostum::findOrFail($id);

        // Tampilkan detailnya
        return view('pages.kostum.show', [
            'kostum' => $kostum
        ]);
    }

    /**
     * Menampilkan halaman form edit data kostum.
     */
    public function edit($id)
    {
        // Ambil data lama dari database
        $kostum = Kostum::findOrFail($id);

        return view('pages.kostum.edit', [
            'kostum' => $kostum
        ]);
    }

    public function update(Request $request, $id)
    {
        // 1. Ambil data lama
        $kostum = Kostum::findOrFail($id);

        // 2. VALIDASI
        $validatedData = $request->validate([
            'nama_kostum'  => 'required|string|max:255',
            'kategori'     => 'required|string|max:255',
            'harga'        => 'required|integer',
            'catatan'      => 'nullable|string',
            'status'       => 'required|in:1,0',
            'image_kostum' => 'nullable|image|mimes:jpg,jpeg,png|max:6048',
        ]);

        // 3. PROSES UPLOAD GAMBAR BARU (jika ada)
        if ($request->hasFile('image_kostum')) {

            $oldImage = $kostum->image_kostum;

            // Hapus file lama jika ada dan masih tersimpan
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            // Upload file baru
            $fileBaru = $request->file('image_kostum');
            $folder = 'kostum';
            $disk = 'public';

            $pathBaru = $fileBaru->store($folder, $disk);

            // Simpan nama file baru ke validated data
            $validatedData['image_kostum'] = $pathBaru;
        }

        // 4. UPDATE DATABASE
        $kostum->update($validatedData);

        return redirect()
            ->route('kostum.index')
            ->with('success', 'Data kostum berhasil diperbarui!');
    }


    public function destroy($id)
    {
        // Ambil data
        $kostum = Kostum::findOrFail($id);

        // HAPUS FILE GAMBAR JIKA ADA
        $gambar = $kostum->image_kostum;

        if ($gambar && Storage::disk('public')->exists($gambar)) {
            Storage::disk('public')->delete($gambar);
        }

        // HAPUS DATA KOSTUM
        $kostum->delete();

        return redirect()
            ->route('kostum.index')
            ->with('success', 'Kostum berhasil dihapus!');
    }
}
