<?php

namespace App\Http\Controllers;

use App\Models\Penyewa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenyewaController extends Controller
{
    /**
     * INDEX
     * - AJAX → DataTables
     * - Normal → Blade
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $penyewas = Penyewa::with('user')->orderBy('id', 'desc')->get();
            return response()->json(['data' => $penyewas]);
        }

        return view('pages.penyewa.index');
    }

    /**
     * FORM TAMBAH VIEW
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('pages.penyewa.create', compact('users'));
    }

    /**
     * SIMPAN DATA PENYEWA
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_penyewa' => 'required|string|max:255',
            'no_telp'      => 'required|string|max:20',
            'alamat'       => 'required|string',
        ]);

        // Cegah duplikasi
        if (Auth::user()->penyewa) {
            return redirect()->route('penyewaan.select');
        }

        Penyewa::create([
            'user_id'      => Auth::id(),
            'nama_penyewa' => $request->nama_penyewa,
            'no_telp'      => $request->no_telp,
            'alamat'       => $request->alamat,
        ]);

        return redirect()->route('penyewaan.select')
            ->with('success', 'Data penyewa berhasil disimpan');
    }
    /**
     * FORM EDIT VIEW
     */
    public function edit($id)
    {
        $penyewa = Penyewa::findOrFail($id);
        $users   = User::orderBy('name')->get();
        return view('pages.penyewa.edit', compact('penyewa', 'users'));
    }

    /**
     * UPDATE DATA PENYEWA
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id'      => 'nullable|integer',
            'nama_penyewa' => 'required|string|max:255',
            'no_telp'      => 'required|string|max:20',
            'alamat'       => 'required|string',
        ]);

        $penyewa = Penyewa::findOrFail($id);
        $penyewa->update($request->only(['user_id', 'nama_penyewa', 'no_telp', 'alamat']));

        return redirect()->route('penyewa.index')
            ->with('success', 'Data penyewa berhasil diperbarui!');
    }

    /**
     * HAPUS DATA PENYEWA (AJAX)
     */
    public function destroy($id)
    {
        $penyewa = Penyewa::findOrFail($id);
        $penyewa->delete();

        if (request()->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'Penyewa berhasil dihapus'
            ]);
        }

        return redirect()->route('penyewa.index')
            ->with('success', 'Penyewa berhasil dihapus!');
    }
}
