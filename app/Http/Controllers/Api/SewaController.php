<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sewa;
use App\Models\DetailSewa;
use App\Models\Kostum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SewaController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Data penyewaan berhasil diambil',
            'data' => Sewa::with('details.kostum')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'penyewa_id' => 'required|exists:penyewas,id',

            'items' => 'required|array|min:1',
            'items.*.kostum_id' => 'required|exists:kostums,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',

            'tanggal_sewa' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after:tanggal_sewa',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // VALIDASI DUPLIKAT ITEM
            $ids = collect($request->items)->pluck('kostum_id');
            if ($ids->count() !== $ids->unique()->count()) {
                throw new \Exception("Tidak boleh memilih kostum yang sama lebih dari sekali");
            }

            $total = 0;

            // VALIDASI STOK + HITUNG TOTAL
            foreach ($request->items as $item) {

                $kostum = Kostum::find($item['kostum_id']);

                if ($kostum->status == 1) {
                    throw new \Exception("Kostum {$kostum->nama_kostum} sedang tidak tersedia");
                }

                $total += $item['harga'] * $item['qty'];
            }

            // CREATE SEWA
            $sewa = Sewa::create([
                'kode_sewa' => 'SEWA-' . now()->format('YmdHis') . '-' . rand(100, 999),
                'penyewa_id' => $request->penyewa_id,
                'tanggal_sewa' => $request->tanggal_sewa,
                'tanggal_kembali' => $request->tanggal_kembali,
                'total_biaya' => $total,
                'dp' => $total * 0.5,
                'sisa_bayar' => $total * 0.5,
                'denda' => 0,
                'status' => 0,
                'status_bayar' => \App\Enums\StatusBayar::PENDING,
                'transaction_status' => null,
                'payment_type' => null,
                'midtrans_order_id_dp' => null,
                'midtrans_order_id_pelunasan' => null,
                'snap_token' => null,
                'snap_token_created_at' => null,
            ]);

            // SIMPAN DETAIL + UPDATE STATUS KOSTUM
            foreach ($request->items as $item) {

                DetailSewa::create([
                    'sewa_id' => $sewa->id,
                    'kostum_id' => $item['kostum_id'],
                    'qty' => $item['qty'],
                    'harga' => $item['harga']
                ]);

                Kostum::where('id', $item['kostum_id'])
                    ->update(['status' => 1]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data penyewaan berhasil disimpan',
                'data' => $sewa->load('details.kostum')
            ], 201);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $sewa = Sewa::with('details.kostum')->find($id);

        if (!$sewa) {
            return response()->json([
                'success' => false,
                'message' => 'Data penyewaan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $sewa
        ]);
    }

    public function update(Request $request, $id)
    {
        $sewa = Sewa::with('details.kostum')->find($id);

        if (!$sewa) {
            return response()->json([
                'success' => false,
                'message' => 'Data penyewaan tidak ditemukan'
            ], 404);
        }

        // BLOKIR FIELD PEMBAYARAN
        if ($request->hasAny([
            'status_bayar',
            'payment_type',
            'transaction_status',
            'midtrans_order_id_dp',
            'midtrans_order_id_pelunasan'
        ])) {
            return response()->json([
                'success' => false,
                'message' => 'Field pembayaran tidak boleh diubah manual'
            ], 403);
        }
        if ($sewa->status_bayar != \App\Enums\StatusBayar::PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak dapat diubah karena pembayaran DP sudah dilakukan.'
            ], 403);
        }
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'penyewa_id' => 'required|exists:penyewas,id',
                'tanggal_sewa' => 'required|date|after_or_equal:today',
                'tanggal_kembali' => 'required|date|after:tanggal_sewa',

                'items' => 'required|array|min:1',
                'items.*.kostum_id' => 'required|exists:kostums,id',
                'items.*.qty' => 'required|integer|min:1',
                'items.*.harga' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            $data = $request->only([
                'penyewa_id',
                'tanggal_sewa',
                'tanggal_kembali',
                'status',
                'denda'
            ]);

            $sewa->update($data);

            // JIKA ITEMS DIUPDATE
            if ($request->has('items')) {

                // kembalikan status kostum lama
                foreach ($sewa->details as $detail) {
                    $detail->kostum->update(['status' => 0]);
                }

                $sewa->details()->delete();

                $total = 0;

                $ids = collect($request->items)->pluck('kostum_id');

                if ($ids->count() !== $ids->unique()->count()) {
                    throw new \Exception("Tidak boleh memilih kostum yang sama lebih dari sekali");
                }

                foreach ($request->items as $item) {

                    $kostum = Kostum::find($item['kostum_id']);

                    // PERBAIKAN VALIDASI
                    if ($kostum->status == 1 && !$sewa->details->contains('kostum_id', $kostum->id)) {
                        throw new \Exception("Kostum {$kostum->nama_kostum} sedang tidak tersedia");
                    }

                    DetailSewa::create([
                        'sewa_id' => $sewa->id,
                        'kostum_id' => $item['kostum_id'],
                        'qty' => $item['qty'],
                        'harga' => $item['harga']
                    ]);

                    $total += $item['harga'] * $item['qty'];

                    $kostum->update(['status' => 1]);
                }

                $sewa->update([
                    'total_biaya' => $total,
                    'dp' => $total * 0.5,
                    'sisa_bayar' => $total * 0.5,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data penyewaan berhasil diperbarui',
                'data' => $sewa->load('details.kostum')
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $sewa = Sewa::with('details.kostum')->find($id);

        if (!$sewa) {
            return response()->json([
                'success' => false,
                'message' => 'Data penyewaan tidak ditemukan'
            ], 404);
        }

        if ($sewa->status_bayar != \App\Enums\StatusBayar::PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak dapat dihapus karena pembayaran sudah dilakukan.'
            ], 403);
        }

        DB::beginTransaction();

        try {

            // kembalikan status kostum
            foreach ($sewa->details as $detail) {
                $detail->kostum->update(['status' => 0]);
            }

            $sewa->details()->delete();
            $sewa->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data penyewaan berhasil dihapus'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
