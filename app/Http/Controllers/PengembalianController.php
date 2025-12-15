<?php

namespace App\Http\Controllers;

use App\Models\Kostum;
use App\Models\Sewa;
use Illuminate\Http\Request;

class PengembalianController extends Controller
{
    /**
     * Tampilkan semua data sewa untuk pengembalian
     */
    public function index()
    {
        // Ambil semua data sewa beserta penyewa
        $sewas = Sewa::with('penyewa')->get(); // kostum dipanggil via accessor
        return view('pages.pengembalian.index', compact('sewas'));
    }

    /**
     * Halaman bayar / edit pembayaran
     */
    public function edit(Sewa $pengembalian)
    {
        return view('pages.pembayaran.bayar', compact('pengembalian'));
    }

    /**
     * Proses pembayaran total biaya & denda
     */
    public function update(Request $request, Sewa $pengembalian)
    {
        // Validasi input
        $validated = $request->validate([
            'total_biaya' => 'required|numeric',
            'denda' => 'nullable|numeric',
            'metode_pembayaran' => 'required',
            'no_rekening' => 'nullable|string',
            'nama_bank' => 'nullable|string',
            'nama_ewallet' => 'nullable|string',
            'nomor_ewallet' => 'nullable|string',
        ]);

        // Update data pembayaran
        $pengembalian->update([
            'total_biaya' => $validated['total_biaya'],
            'denda' => $validated['denda'] ?? 0,
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'no_rekening' => $validated['no_rekening'] ?? null,
            'nama_bank' => $validated['nama_bank'] ?? null,
            'nama_ewallet' => $validated['nama_ewallet'] ?? null,
            'nomor_ewallet' => $validated['nomor_ewallet'] ?? null,
            'status_bayar' => 1
        ]);

        return redirect()->route('pengembalian.index')
            ->with('success', 'Pembayaran berhasil diperbarui');
    }

    /**
     * Proses pengembalian kostum (ubah status kostum menjadi tersedia)
     */
    public function destroy(Sewa $pengembalian)
    {
        // Update semua kostum terkait menjadi tersedia (status = 0)
        foreach ($pengembalian->kostum_list as $kostum) {
            $kostum->update(['status' => 0]);
        }

        // Tandai sewa sudah dikembalikan
        $pengembalian->update(['status' => 1]);

        return redirect()->route('pengembalian.index')
            ->with('success', 'Kostum berhasil dikembalikan');
    }

    /**
     * Hapus data pembayaran & sewa
     */
    public function hapus($id)
    {
        $sewa = Sewa::findOrFail($id);

        // Kembalikan status semua kostum terkait
        foreach ($sewa->kostum_list as $kostum) {
            $kostum->update(['status' => 1]);
        }

        $sewa->delete();

        return redirect()->back()->with('success', 'Pembayaran berhasil dihapus!');
    }
}
