<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPenyewa
{
    public function handle(Request $request, Closure $next)
    {
        // Asumsi: CheckAuth sudah dijalankan dulu
        $user = Auth::user();

        if (
            $user->role === 'penyewa' &&
            !$user->penyewa
        ) {
            return redirect()->route('penyewa.create')
                ->with('warning', 'Lengkapi data penyewa terlebih dahulu');
        }

        return $next($request);
    }
}
