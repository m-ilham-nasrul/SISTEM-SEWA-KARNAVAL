<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan form login
     */
    public function loginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $checkLogin = Auth::attempt($credentials);

        if ($checkLogin) {

            // Ambil role user
            $role = Auth::user()->role;

            // Redirect berdasarkan role
            if ($role === 'admin') {
                return redirect()->route('dashboard')->with('success', 'Selamat datang Admin!');
            }

            if ($role === 'penyewa') {
                return redirect()->route('dashboard')->with('success', 'Login berhasil!');
            }

            // default jika role tidak terdeteksi
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }
        return back()->with('error', 'Email atau password salah.');
    }

    /**
     * Tampilkan form registrasi
     */
    public function registerForm()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi
     */
    public function register()
    {
        return view('auth.register');
    }

    public function processRegister(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:5',
            'role' => 'required|in:admin,penyewa',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }
    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Hapus session biar benar-benar keluar
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Arahkan ke landing page
        return redirect('/'); // atau route('landing') jika punya route name
    }
}
