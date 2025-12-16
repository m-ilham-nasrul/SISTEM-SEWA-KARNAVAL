<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * INDEX
     * - AJAX → DataTables (JSON)
     * - Normal → Blade
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::latest()->get();

            return response()->json([
                'data' => $users
            ]);
        }

        return view('pages.user.index');
    }

    /**
     * FORM TAMBAH USER
     */
    public function create()
    {
        return view('pages.user.create');
    }

    /**
     * SIMPAN USER BARU
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,penyewa',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * FORM EDIT USER
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('pages.user.edit', compact('user'));
    }

    /**
     * UPDATE USER
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $id,
            'role'     => 'required|in:admin,penyewa',
            'password' => 'nullable|min:6',
        ]);

        $data = $request->only(['name', 'email', 'role']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'Data user berhasil diperbarui!');
    }

    /**
     * HAPUS USER (AJAX)
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        // Jika request AJAX, kirim JSON
        if (request()->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'User berhasil dihapus'
            ]);
        }

        return redirect()->route('user.index')->with('success', 'User berhasil dihapus!');
    }
}
