<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penyewa;
use App\Models\Kostum;
use App\Models\Sewa;

class DashboardController extends Controller
{
    public function index()
    {
        $penyewa = Penyewa::count();
        $kostum = Kostum::count();
        $sewa = Sewa::where('status', 0)->count();
        $total_transaksi = Sewa::count();

        // Versi collection
        $total_pendapatan = Sewa::where('status_bayar', 1)->get()
            ->sum(function ($s) {
                return $s->total_biaya + $s->denda;
            });

        return view('dashboard', compact(
            'penyewa',
            'kostum',
            'sewa',
            'total_transaksi',
            'total_pendapatan'
        ));
    }

    public function ajaxData()
    {
        $penyewa = Penyewa::count();
        $kostum = Kostum::count();
        $sewa = Sewa::where('status', 0)->count();
        $total_transaksi = Sewa::count();

        $total_pendapatan = Sewa::where('status_bayar', 1)->get()
            ->sum(function ($s) {
                return $s->total_biaya + $s->denda;
            });

        return response()->json([
            'penyewa' => $penyewa,
            'kostum' => $kostum,
            'sewa' => $sewa,
            'total_transaksi' => $total_transaksi,
            'total_pendapatan' => $total_pendapatan,
        ]);
    }
}
