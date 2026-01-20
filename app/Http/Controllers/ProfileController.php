<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        $user = User::with('penyewa')->find(Auth::id());

        return view('pages.profile.index', [
            'user' => $user
        ]);
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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

    public function password(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 404);
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
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json([
            'status'  => true,
            'message' => 'Password berhasil diubah'
        ]);
    }

    public function photo(Request $request)
    {
        $user = User::find(Auth::id());
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        if ($user->photo && Storage::disk('public')->exists('profile/' . $user->photo)) {
            Storage::disk('public')->delete('profile/' . $user->photo);
        }

        $file = $request->file('photo');
        $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

        Storage::disk('public')->putFileAs('profile', $file, $filename);
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
        if ($user->photo && Storage::disk('public')->exists('profile/' . $user->photo)) {
            Storage::disk('public')->delete('profile/' . $user->photo);
        }

        $user->photo = null;
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Foto profil berhasil dihapus'
        ]);
    }
}
