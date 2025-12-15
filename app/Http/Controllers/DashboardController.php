<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Penyewa;
use App\Models\Kostum;
use App\Models\Sewa;

class DashboardController extends Controller
{
    public function index()
    {
        // total penyewa (customer)
        $penyewa = Penyewa::count();

        // total kostum
        $kostum = Kostum::count();

        // total penyewaan yang sedang berlangsung (status = 0 => masa sewa / belum kembali)
        $sewa = Sewa::where('status', 0)->count();

        // total transaksi (jumlah record sewa)
        $total_transaksi = Sewa::count();

        // total pendapatan: jumlah total_biaya + denda untuk sewa yang sudah dibayar
        $paid = Sewa::where('status_bayar', 1);
        $total_pendapatan = (int) $paid->sum('total_biaya') + (int) $paid->sum('denda');

        // kirim ke view 'dashboard' (sesuaikan nama view jika berbeda: e.g. 'pages.dashboard')
        return view('dashboard', compact(
            'penyewa',
            'kostum',
            'sewa',
            'total_transaksi',
            'total_pendapatan'
        ));
    }
}

