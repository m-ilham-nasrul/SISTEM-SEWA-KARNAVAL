<?php

namespace App\Http\Controllers;

use App\Models\Penyewa;
use App\Models\Kostum;
use App\Models\Sewa;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $penyewa = null;
        $kostum = null;
        $total_pendapatan = null;
        $sewa = 0;
        $total_transaksi = 0;
        $riwayatSewa = collect();

        if ($user->role === 'admin') {

            $penyewa = Penyewa::count();
            $kostum = Kostum::count();
            $sewa = Sewa::where('status', 0)->count();
            $total_transaksi = Sewa::count();

            $total_pendapatan = Sewa::where('status_bayar', 1)
                ->selectRaw('SUM(total_biaya + denda) as total')
                ->value('total') ?? 0;

            $riwayatSewa = Sewa::latest()->limit(5)->get();
        }

        else {
            if ($user->penyewa) {

                $sewa = Sewa::where('penyewa_id', $user->penyewa->id)
                    ->where('status', 0)
                    ->count();

                $total_transaksi = Sewa::where('penyewa_id', $user->penyewa->id)->count();

                $riwayatSewa = Sewa::where('penyewa_id', $user->penyewa->id)
                    ->latest()
                    ->limit(5)
                    ->get();
            }

        }

        return view('dashboard', compact(
            'penyewa',
            'kostum',
            'sewa',
            'total_transaksi',
            'total_pendapatan',
            'riwayatSewa'
        ));
    }

    public function ajaxData()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return response()->json([
                'penyewa' => Penyewa::count(),
                'kostum' => Kostum::count(),
                'sewa' => Sewa::where('status', 0)->count(),
                'total_transaksi' => Sewa::count(),
                'total_pendapatan' => Sewa::where('status_bayar', 1)
                    ->selectRaw('SUM(total_biaya + denda) as total')
                    ->value('total') ?? 0,
            ]);
        }

        if ($user->penyewa) {
            return response()->json([
                'sewa' => Sewa::where('penyewa_id', $user->penyewa->id)
                    ->where('status', 0)
                    ->count(),
                'total_transaksi' => Sewa::where('penyewa_id', $user->penyewa->id)->count(),
            ]);
        }

        return response()->json([
            'sewa' => 0,
            'total_transaksi' => 0,
        ]);
    }
}
