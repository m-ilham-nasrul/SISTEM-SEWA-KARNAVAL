<?php

namespace App\Http\Controllers;

use App\Enums\StatusBayar;
use App\Models\Penyewa;
use App\Models\Kostum;
use App\Models\Sewa;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $penyewa = 0;
        $kostum = 0;
        $total_pendapatan = 0;
        $sewa = 0;
        $total_sewa = 0;
        $total_transaksi = 0;
        $riwayatSewa = collect();

        if ($user->role === 'admin') {

            $penyewa = Penyewa::count();
            $kostum = Kostum::count();

            $sewa = Sewa::where('status', 0)
                ->whereIn('status_bayar', [
                    StatusBayar::DP_PAID,
                    StatusBayar::PAID,
                ])
                ->count();
            $total_sewa = Sewa::count();

            $total_transaksi = Sewa::whereIn('status_bayar', [
                StatusBayar::DP_PAID,
                StatusBayar::PAID
            ])->count();

            $total_pendapatan = Sewa::where('status_bayar', StatusBayar::PAID)
                ->selectRaw('SUM(total_biaya + denda) as total')
                ->value('total') ?? 0;

            $riwayatSewa = Sewa::with('details.kostum')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($sewa) {
                    $sewa->kostum_list = $sewa->details->map(function ($d) {
                        return $d->kostum;
                    });
                    return $sewa;
                });
        } else {

            if ($user->penyewa) {

                $sewa = Sewa::where('penyewa_id', $user->penyewa->id)
                    ->where('status', 0)
                    ->whereIn('status_bayar', [
                        StatusBayar::DP_PAID,
                        StatusBayar::PAID,
                    ])
                    ->count();

                $total_transaksi = Sewa::where('penyewa_id', $user->penyewa->id)
                    ->whereIn('status_bayar', [
                        StatusBayar::DP_PAID,
                        StatusBayar::PAID
                    ])->count();

                $riwayatSewa = Sewa::with('details.kostum')
                    ->where('penyewa_id', $user->penyewa->id)
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($sewa) {
                        $sewa->kostum_list = $sewa->details->map(function ($d) {
                            return $d->kostum;
                        });
                        return $sewa;
                    });
            }
        }

        return view('dashboard', compact(
            'penyewa',
            'kostum',
            'sewa',
            'total_sewa',
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
                'sewa' => Sewa::where('status', 0)
                    ->whereIn('status_bayar', [
                        StatusBayar::DP_PAID,
                        StatusBayar::PAID,
                    ])
                    ->count(),

                'total_sewa' => Sewa::count(),

                'total_transaksi' => Sewa::whereIn('status_bayar', [
                    StatusBayar::DP_PAID,
                    StatusBayar::PAID
                ])->count(),

                'total_pendapatan' => Sewa::where(
                    'status_bayar',
                    StatusBayar::PAID
                )
                    ->selectRaw('SUM(total_biaya + denda) as total')
                    ->value('total') ?? 0,
            ]);
        }

        if ($user->penyewa) {
            return response()->json([
                'sewa' => Sewa::where('penyewa_id', $user->penyewa->id)
                    ->where('status', 0)
                    ->whereIn('status_bayar', [
                        StatusBayar::DP_PAID,
                        StatusBayar::PAID,
                    ])
                    ->count(),
                'total_transaksi' => Sewa::where('penyewa_id', $user->penyewa->id)
                    ->whereIn('status_bayar', [
                        StatusBayar::DP_PAID,
                        StatusBayar::PAID
                    ])->count(),
            ]);
        }

        return response()->json([
            'sewa' => 0,
            'total_transaksi' => 0,
        ]);
    }
}
