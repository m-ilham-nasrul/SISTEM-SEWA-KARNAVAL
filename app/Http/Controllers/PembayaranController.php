<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    // Menampilkan daftar pembayaran
    public function index(Request $request)
    {
        $status = $request->input('status_bayar');

        if ($status === '1') {
            $statusTitle = 'Terbayar';
            $sewas = Sewa::with('penyewa')->where('status_bayar', 1)->get();
        } elseif ($status === '0') {
            $statusTitle = 'Menunggu Pembayaran';
            $sewas = Sewa::with('penyewa')->where('status_bayar', 0)->get();
        } else {
            $statusTitle = '';
            $sewas = Sewa::with('penyewa')->get();
        }

        // Pendapatan hari ini
        $pendapatan_hari =
            Sewa::whereDate('tanggal_sewa', date('Y-m-d'))->where('status_bayar', 1)->sum('total_biaya')
            +
            Sewa::whereDate('tanggal_sewa', date('Y-m-d'))->sum('denda');

        // Pendapatan bulan ini
        $pendapatan_bulan =
            Sewa::whereMonth('tanggal_sewa', date('m'))->where('status_bayar', 1)->sum('total_biaya')
            +
            Sewa::whereMonth('tanggal_sewa', date('m'))->sum('denda');

        return view('pages.pembayaran.index', compact(
            'sewas',
            'statusTitle',
            'pendapatan_hari',
            'pendapatan_bulan'
        ));
    }

    // Form pembayaran
    public function bayar($id)
    {
        $pengembalian = Sewa::with('penyewa')->findOrFail($id);
        return view('pages.pembayaran.bayar', compact('pengembalian'));
    }

    // Proses pembayaran
    public function update(Request $request, $id)
    {
        $request->validate([
            'denda' => 'nullable|numeric',
            'total_biaya' => 'required|numeric',
            'metode_pembayaran' => 'required',
            'no_rekening' => 'nullable|string'
        ]);

        $sewa = Sewa::findOrFail($id);

        $sewa->denda = $request->denda ?? 0;
        $sewa->total_biaya = $request->total_biaya;
        $sewa->metode_pembayaran = $request->metode_pembayaran;
        $sewa->no_rekening = $request->no_rekening;
        $sewa->status_bayar = 1;

        $sewa->save();

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil diproses.');
    }

    // Cetak nota
    public function nota($id)
    {
        $sewa = Sewa::with('penyewa')->findOrFail($id);

        return view('pages.pembayaran.nota', compact('sewa'));
    }

    // Hapus pembayaran
    public function hapus($id)
    {
        $sewa = Sewa::findOrFail($id);

        // Jika mau update status kostum menjadi tersedia, lakukan perulangan
        if ($sewa->kostum_id) {
            foreach ($sewa->kostum_list as $kostum) {
                $kostum->status = 1; // Tersedia
                $kostum->save();
            }
        }

        $sewa->delete();

        return redirect()->back()->with('success', 'Pembayaran berhasil dihapus!');
    }
}
