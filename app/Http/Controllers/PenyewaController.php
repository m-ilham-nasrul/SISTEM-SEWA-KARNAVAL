<?php

namespace App\Http\Controllers;

use App\Models\Penyewa;
use App\Models\User;
use Illuminate\Http\Request;

class PenyewaController extends Controller
{
    public function index()
    {
        $penyewas = Penyewa::orderBy('id', 'desc')->get();
        return view('pages.penyewa.index', compact('penyewas'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('pages.penyewa.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'      => 'nullable|integer',
            'nama_penyewa' => 'required|string|max:255',
            'no_telp'      => 'required|string|max:20', // disamakan
            'alamat'       => 'required|string',
        ]);

        Penyewa::create([
            'user_id'      => $request->user_id,
            'nama_penyewa' => $request->nama_penyewa,
            'no_telp'      => $request->no_telp, // tidak mapping lagi, sudah sama
            'alamat'       => $request->alamat,
        ]);

        return redirect()->route('penyewa.index')
            ->with('success', 'Penyewa berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $penyewa = Penyewa::findOrFail($id);
        $users   = User::orderBy('name')->get();
        return view('pages.penyewa.edit', compact('penyewa', 'users'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id'      => 'nullable|integer',
            'nama_penyewa' => 'required|string|max:255',
            'no_telp'      => 'required|string|max:20', // disamakan
            'alamat'       => 'required|string',
        ]);

        $penyewa = Penyewa::findOrFail($id);
        $penyewa->update([
            'user_id'      => $request->user_id,
            'nama_penyewa' => $request->nama_penyewa,
            'no_telp'      => $request->no_telp,
            'alamat'       => $request->alamat,
        ]);

        return redirect()->route('penyewa.index')
            ->with('success', 'Data penyewa berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $penyewa = Penyewa::findOrFail($id);
        $penyewa->delete();

        return redirect()->route('penyewa.index')
            ->with('success', 'Penyewa berhasil dihapus!');
    }
}
