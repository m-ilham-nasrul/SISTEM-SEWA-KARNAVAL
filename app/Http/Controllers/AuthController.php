<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required',
            ],
            [
                'required' => ':attribute wajib diisi.',
                'email' => ':attribute harus berupa email yang valid.',
            ],
            [
                'email' => 'Email',
                'password' => 'Password',
            ]
        );

        $credentials = $request->only('email', 'password');
        $checkLogin = Auth::attempt($credentials);

        if ($checkLogin) {

            $role = Auth::user()->role;

            if ($role === 'admin') {
                return redirect()->route('dashboard')->with('success', 'Selamat datang Admin!');
            }

            if ($role === 'penyewa') {
                return redirect()->route('dashboard')->with('success', 'Login berhasil!');
            }

            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }
        return back()->with('error', 'Email atau password salah.');
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function processRegister(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:8',
                'role' => 'required|in:admin,penyewa',
            ],
            [
                'required' => ':attribute wajib diisi.',
                'email' => ':attribute harus berupa email yang valid.',
                'unique' => ':attribute sudah terdaftar.',
                'confirmed' => 'Konfirmasi :attribute tidak sesuai.',
                'min' => ':attribute minimal :min karakter.',
            ],
            [
                'name' => 'Nama',
                'email' => 'Email',
                'password' => 'Password',
                'role' => 'Role',
            ]
        );

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
