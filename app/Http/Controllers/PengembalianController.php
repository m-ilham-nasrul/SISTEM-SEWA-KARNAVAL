<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\Kostum;
use Illuminate\Http\Request;

class PengembalianController extends Controller
{
    /**
     * INDEX
     * - AJAX → DataTables
     * - Normal → Blade
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $sewas = Sewa::with(['penyewa.user'])
                ->orderBy('created_at', 'desc')
                ->get();


            $data = $sewas->map(function ($sewa) {

                $kostums = [];
                if ($sewa->kostum_id) {
                    $ids = json_decode($sewa->kostum_id, true);
                    $kostums = Kostum::whereIn('id', $ids)->get()->map(function ($k) {
                        return [
                            'id' => $k->id,
                            'nama_kostum' => $k->nama_kostum
                        ];
                    });
                }

                return [
                    'id' => $sewa->id,
                    'kode_sewa' => $sewa->kode_sewa ?? 'SEWA-' . str_pad($sewa->id, 4, '0', STR_PAD_LEFT),
                    'penyewa' => ['user' => ['name' => $sewa->penyewa->user->name ?? null]],
                    'kostum_list' => $kostums,
                    'tanggal_sewa' => $sewa->tanggal_sewa,
                    'tanggal_kembali' => $sewa->tanggal_kembali,
                    'total_biaya' => $sewa->total_biaya,
                    'denda' => $sewa->denda,
                    'status' => $sewa->status,
                    'status_bayar' => $sewa->status_bayar,
                ];
            });

            return response()->json(['data' => $data]);
        }

        return view('pages.pengembalian.index');
    }

    /**
     * FORM PEMBAYARAN
     */
    public function edit($id)
    {
        $pengembalian = Sewa::with('penyewa')->findOrFail($id);
        return view('pages.pembayaran.bayar', compact('pengembalian'));
    }

    /**
     * PROSES PEMBAYARAN
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'total_biaya' => 'required|numeric',
            'denda' => 'nullable|numeric',
            'metode_pembayaran' => 'required|in:tunai,ewallet,transfer',

            'nama_ewallet' => 'required_if:metode_pembayaran,ewallet|string|nullable',
            'nomor_ewallet' => 'required_if:metode_pembayaran,ewallet|string|nullable',

            'nama_bank' => 'required_if:metode_pembayaran,transfer|string|nullable',
            'no_rekening' => 'required_if:metode_pembayaran,transfer|string|nullable',
        ], [
            'nama_ewallet.required_if' => 'Nama E-Wallet wajib diisi jika metode E-Wallet dipilih.',
            'nomor_ewallet.required_if' => 'Nomor E-Wallet wajib diisi jika metode E-Wallet dipilih.',
            'nama_bank.required_if' => 'Nama Bank wajib diisi jika metode Transfer dipilih.',
            'no_rekening.required_if' => 'Nomor Rekening wajib diisi jika metode Transfer dipilih.',
        ]);

        $sewa = Sewa::findOrFail($id);

        $sewa->update([
            'total_biaya' => $validated['total_biaya'],
            'denda' => $validated['denda'] ?? 0,
            'metode_pembayaran' => $validated['metode_pembayaran'],

            'nama_ewallet' => $validated['metode_pembayaran'] === 'ewallet' ? $validated['nama_ewallet'] : null,
            'nomor_ewallet' => $validated['metode_pembayaran'] === 'ewallet' ? $validated['nomor_ewallet'] : null,

            'nama_bank' => $validated['metode_pembayaran'] === 'transfer' ? $validated['nama_bank'] : null,
            'no_rekening' => $validated['metode_pembayaran'] === 'transfer' ? $validated['no_rekening'] : null,

            'status_bayar' => 1
        ]);

        return redirect()
            ->route('pengembalian.index')
            ->with('success', 'Pembayaran berhasil diproses');
    }
    /**
     * PROSES PENGEMBALIAN KOSTUM
     */
    public function destroy($id)
    {
        try {
            $sewa = Sewa::findOrFail($id);

            //SUDAH DIKEMBALIKAN
            if ($sewa->status == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Sewa sudah dikembalikan'
                ], 400);
            }

            //BELUM BAYAR
            if (!$sewa->status_bayar) {
                return response()->json([
                    'status' => false,
                    'message' => 'Sewa belum dibayar. Silakan lakukan pembayaran terlebih dahulu.'
                ], 404);
            }

            // KEMBALIKAN STATUS KOSTUM
            if ($sewa->kostum_id) {
                $ids = json_decode($sewa->kostum_id, true);
                Kostum::whereIn('id', $ids)->update([
                    'status' => 0
                ]);
            }

            // UPDATE STATUS SEWA
            $sewa->update([
                'status' => 1
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Kostum berhasil dikembalikan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengembalikan kostum'
            ], 500);
        }
    }


    public function hapus($id)
    {
        try {
            $sewa = Sewa::findOrFail($id);

            // pastikan kostum kembali tersedia
            if ($sewa->kostum_id) {
                $ids = json_decode($sewa->kostum_id, true);
                Kostum::whereIn('id', $ids)->update([
                    'status' => 0
                ]);
            }

            $sewa->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data pengembalian berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal dihapus'
            ], 404);
        }
    }
}
