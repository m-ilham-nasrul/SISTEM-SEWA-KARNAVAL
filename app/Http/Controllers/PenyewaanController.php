<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\Penyewa;
use App\Models\Kostum;
use Illuminate\Http\Request;

class PenyewaanController extends Controller
{
    /** ------------------------------------------
     *  Tampilkan Semua Data Penyewaan
     * -------------------------------------------*/
    public function index()
    {
        $sewas = Sewa::orderBy('id', 'desc')->get();
        return view('pages.penyewaan.index', compact('sewas'));
    }

    /** ------------------------------------------
     *  HALAMAN PILIH KOSTUM (multi select)
     * -------------------------------------------*/
    public function select()
    {
        $kostums = Kostum::all();
        return view('pages.penyewaan.select', compact('kostums'));
    }

    /** ------------------------------------------
     *  HALAMAN CREATE -> setelah pilih banyak kostum
     * -------------------------------------------*/
    public function create(Request $request)
    {
        if (!$request->has('kostum_id')) {
            return redirect()->route('penyewaan.select')
                ->with('error', 'Silakan pilih minimal satu kostum!');
        }

        $kostumIds = $request->kostum_id;

        return view('pages.penyewaan.create', [
            'penyewas'  => Penyewa::all(),
            'kostums'   => Kostum::whereIn('id', $kostumIds)->get(),
        ]);
    }

    /** ------------------------------------------
     *  Store Penyewaan Baru (multi kostum)
     *  - Otomatis update status kostum menjadi "Sedang Digunakan"
     * -------------------------------------------*/
    public function store(Request $request)
    {
        $request->validate([
            'penyewa_id' => 'required|exists:penyewas,id',
            'kostum_id'  => 'required|array',
            'kostum_id.*' => 'exists:kostums,id',
            'tanggal_sewa' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_sewa',
            'catatan' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $kostumList = Kostum::whereIn('id', $request->kostum_id)->get();
        $total = $kostumList->sum('harga');

        $sewa = Sewa::create([
            'kode_sewa'       => 'SEWA-' . now()->format('YmdHis'),
            'penyewa_id'      => $request->penyewa_id,
            'kostum_id'       => json_encode($request->kostum_id),
            'tanggal_sewa'    => $request->tanggal_sewa,
            'tanggal_kembali' => $request->tanggal_kembali,
            'total_biaya'     => $total,
            'catatan'         => $request->catatan,
            'status'          => $request->status,
            'status_bayar'    => false,
            'denda'           => 0,
        ]);

        // --- Update status kostum menjadi "Sedang Digunakan"
        foreach ($kostumList as $kostum) {
            $kostum->status = 1;
            $kostum->save();
        }

        return redirect()->route('pembayaran.index')
            ->with('success', 'Penyewaan berhasil ditambahkan!');
    }

    /** ------------------------------------------
     *  Tampilkan Detail Penyewaan
     * -------------------------------------------*/
    public function show($id)
    {
        $sewa = Sewa::findOrFail($id);
        $kostumIds = $sewa->kostum_id ? json_decode($sewa->kostum_id, true) : [];
        $kostums = Kostum::whereIn('id', $kostumIds)->get();
        $hargaPaket = $kostums->sum('harga');
        $denda = $sewa->denda ?? 0;
        $total = $hargaPaket + $denda;

        return view('pages.penyewaan.show', compact('sewa', 'kostums', 'hargaPaket', 'denda', 'total'));
    }

    /** ------------------------------------------
     *  Edit Penyewaan
     * -------------------------------------------*/
    public function edit($id)
    {
        $sewa = Sewa::findOrFail($id);
        $currentIds = json_decode($sewa->kostum_id);

        return view('pages.penyewaan.edit', [
            'sewa'      => $sewa,
            'kostums'   => Kostum::all(),
            'selected'  => $currentIds,
            'penyewas'  => Penyewa::all(),
        ]);
    }

    /** ------------------------------------------
     *  Update Penyewaan
     *  - Otomatis update status kostum sesuai sewa
     * -------------------------------------------*/
    public function update(Request $request, $id)
    {
        $sewa = Sewa::findOrFail($id);

        $request->validate([
            'penyewa_id'       => 'required|exists:penyewas,id',
            'kostum_id'        => 'required|array',
            'kostum_id.*'      => 'exists:kostums,id',
            'tanggal_sewa'     => 'required|date',
            'tanggal_kembali'  => 'required|date|after_or_equal:tanggal_sewa',
            'catatan'          => 'nullable|string',
            'denda'            => 'nullable|integer|min:0',
        ]);

        // --- Reset status kostum lama
        $oldIds = json_decode($sewa->kostum_id, true) ?? [];
        foreach (Kostum::whereIn('id', $oldIds)->get() as $kostum) {
            $kostum->status = 0;
            $kostum->save();
        }

        // --- Hitung total biaya baru
        $newKostumList = Kostum::whereIn('id', $request->kostum_id)->get();
        $total = $newKostumList->sum('harga') + ($request->denda ?? 0);

        $sewa->update([
            'penyewa_id'      => $request->penyewa_id,
            'kostum_id'       => json_encode($request->kostum_id),
            'tanggal_sewa'    => $request->tanggal_sewa,
            'tanggal_kembali' => $request->tanggal_kembali,
            'catatan'         => $request->catatan,
            'status'          => $request->status,
            'denda'           => $request->denda ?? 0,
            'total_biaya'     => $total,
        ]);

        // --- Set status kostum baru
        foreach ($newKostumList as $kostum) {
            $kostum->status = 1;
            $kostum->save();
        }

        return redirect()->route('penyewaan.index')
            ->with('success', 'Penyewaan berhasil diperbarui!');
    }

    /** ------------------------------------------
     *  Hapus / Batalkan Penyewaan
     *  - Otomatis update status kostum jadi tersedia
     * -------------------------------------------*/
    public function destroy($id)
    {
        $sewa = Sewa::findOrFail($id);

        // --- Reset status kostum menjadi tersedia
        foreach ($sewa->kostum_list as $kostum) {
            $kostum->status = 0;
            $kostum->save();
        }

        $sewa->delete();

        return redirect()->route('penyewaan.index')
            ->with('success', 'Penyewaan berhasil dibatalkan!');
    }
}
