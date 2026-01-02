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
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json([
                'data' => Penyewa::with('user')->latest()->get()
            ]);
        }

        return view('pages.penyewa.index');
    }

    /**
     * FORM TAMBAH
     */
    public function create()
    {
        $users = User::where('role', 'penyewa')->orderBy('name')->get();
        return view('pages.penyewa.create', compact('users'));
    }

    /**
     * SIMPAN DATA
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'no_telp' => 'required|string|max:20',
            'alamat'  => 'required|string',
        ]);

        $userId = Auth::user()->role === 'admin'
            ? $request->user_id
            : Auth::id();

        if (Penyewa::where('user_id', $userId)->exists()) {
            return back()->with('error', 'User sudah terdaftar sebagai penyewa');
        }

        Penyewa::create([
            'user_id' => $userId,
            'no_telp' => $request->no_telp,
            'alamat'  => $request->alamat,
        ]);

        return redirect()->route('penyewaan.select')
            ->with('success', 'Data penyewa berhasil disimpan');
    }

    /**
     * FORM EDIT
     */
    public function edit($id)
    {
        $penyewa = Penyewa::with('user')->findOrFail($id);

        // ADMIN → boleh pilih user
        $users = Auth::user()->role === 'admin'
            ? User::where('role', 'penyewa')->orderBy('name')->get()
            : null;

        return view('pages.penyewa.edit', compact('penyewa', 'users'));
    }

    /**
     * UPDATE DATA
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'no_telp' => 'required|string|max:20',
            'alamat'  => 'required|string',
        ]);

        $penyewa = Penyewa::findOrFail($id);

        // ===== OTORISASI =====
        if (Auth::user()->role !== 'admin') {
            if ($penyewa->user_id !== Auth::id()) {
                abort(403, 'Anda tidak memiliki akses');
            }
        }

        // ===== UPDATE =====
        $penyewa->update([
            'user_id' => Auth::user()->role === 'admin'
                ? $request->user_id
                : $penyewa->user_id,
            'no_telp' => $request->no_telp,
            'alamat'  => $request->alamat,
        ]);

        return redirect()->route('penyewa.index')
            ->with('success', 'Data penyewa berhasil diperbarui');
    }

    /**
     * HAPUS
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
            ->with('success', 'Penyewa berhasil dihapus');
    }
}
