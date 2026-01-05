<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Halaman profil
     */
    public function index()
    {
        $user = User::with('penyewa')->find(Auth::id());

        return view('pages.profile.index', [
            'user' => $user
        ]);
    }


    /**
     * Update profil (AJAX)
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Pastikan relasi penyewa ter-load
        $user->load('penyewa');

        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_telp' => 'nullable|string|max:20',
            'alamat'  => 'nullable|string',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->penyewa) {
            $user->penyewa->update([
                'no_telp' => $validated['no_telp'] ?? $user->penyewa->no_telp,
                'alamat'  => $validated['alamat']  ?? $user->penyewa->alamat,
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Profil berhasil diperbarui'
        ]);
    }

    /**
     * Update password (AJAX)
     */
    public function password(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'password_lama' => 'required',
            'password'      => 'required|string|min:6|confirmed',
        ]);

        if (! Hash::check($request->password_lama, $user->password)) {
            return response()->json([
                'errors' => [
                    'password_lama' => ['Password lama tidak sesuai']
                ]
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Password berhasil diubah'
        ]);
    }

    /**
     * Update foto profil (AJAX)
     */
    public function photo(Request $request)
    {
        // Ambil user dari database (bukan dari Auth saja)
        $user = User::find(Auth::id());

        // Jika belum login
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validasi file
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Hapus foto lama jika ada
        if ($user->photo && Storage::disk('public')->exists('profile/' . $user->photo)) {
            Storage::disk('public')->delete('profile/' . $user->photo);
        }

        // Simpan foto baru
        $file = $request->file('photo');
        $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

        Storage::disk('public')->putFileAs('profile', $file, $filename);

        // Simpan nama file ke database
        $user->photo = $filename;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Foto profil berhasil diperbarui',
            'photo' => asset('storage/profile/' . $filename),
        ]);
    }

    public function deletePhoto()
    {
        $user = User::find(Auth::id());

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Hapus file foto jika ada
        if ($user->photo && Storage::disk('public')->exists('profile/' . $user->photo)) {
            Storage::disk('public')->delete('profile/' . $user->photo);
        }

        // Kosongkan kolom photo di database
        $user->photo = null;
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Foto profil berhasil dihapus'
        ]);
    }
}
