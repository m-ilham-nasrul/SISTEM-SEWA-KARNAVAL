<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
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
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = public_path('uploads/profile');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        if ($user->photo && File::exists($path . '/' . $user->photo)) {
            File::delete($path . '/' . $user->photo);
        }

        $file = $request->file('photo');
        $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->extension();

        $file->move($path, $filename);

        $user->update(['photo' => $filename]);

        return response()->json([
            'status'  => true,
            'message' => 'Foto profil berhasil diperbarui',
            'photo'   => asset('uploads/profile/' . $filename),
        ]);
    }



    public function deletePhoto()
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $path = public_path('uploads/profile');

        if ($user->photo && File::exists($path . '/' . $user->photo)) {
            File::delete($path . '/' . $user->photo);
        }

        $user->update(['photo' => null]);

        return response()->json([
            'status'  => true,
            'message' => 'Foto profil berhasil dihapus'
        ]);
    }
}
