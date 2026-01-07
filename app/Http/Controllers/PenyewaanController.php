<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\Penyewa;
use App\Models\Kostum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenyewaanController extends Controller
{
    /**
     * INDEX
     * - AJAX → DataTables (JSON)
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
                    'status' => $sewa->status,
                    'penyewa' => $sewa->penyewa ? ['user' => ['name' => optional($sewa->penyewa->user)->name]] : null,
                    'kostum_list' => $kostums,
                    'tanggal_sewa' => $sewa->tanggal_sewa,
                    'tanggal_kembali' => $sewa->tanggal_kembali,
                ];
            });

            return response()->json(['data' => $data]);
        }

        return view('pages.penyewaan.index');
    }

    /**
     * FORM PILIH KOSTUM
     */
    public function select()
    {
        $kostums = Kostum::all();
        return view('pages.penyewaan.select', compact('kostums'));
    }

    /**
     * FORM CREATE
     */
    public function create(Request $request)
    {
        if (!$request->has('kostum_id')) {
            return redirect()->route('penyewaan.select')
                ->with('error', 'Silakan pilih minimal satu kostum!');
        }

        $kostumIds = $request->kostum_id;
        $user = Auth::user();

        return view('pages.penyewaan.create', [
            'kostums' => Kostum::whereIn('id', $kostumIds)->get(),
            'penyewa' => $user->role === 'penyewa' ? $user->penyewa : null,
            'penyewas' => $user->role === 'admin' ? Penyewa::all() : null,
        ]);
    }

    /**
     * STORE PENYEWAAN
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'kostum_id'        => 'required|array',
            'kostum_id.*'      => 'exists:kostums,id',
            'tanggal_sewa'     => 'required|date',
            'tanggal_kembali'  => 'required|date|after_or_equal:tanggal_sewa',
            'catatan'          => 'nullable|string',
            'status'           => 'required|boolean',
        ]);

        // Tentukan penyewa ID secara aman
        if ($user->role === 'penyewa') {
            $penyewaId = $user->penyewa->id;
        } else {
            $request->validate([
                'penyewa_id' => 'required|exists:penyewas,id'
            ]);
            $penyewaId = $request->penyewa_id;
        }

        $kostumList = Kostum::whereIn('id', $request->kostum_id)->get();
        $total = $kostumList->sum('harga');

        $sewa = Sewa::create([
            'kode_sewa'       => 'SEWA-' . now()->format('YmdHis'),
            'penyewa_id'      => $penyewaId,
            'kostum_id'       => json_encode($request->kostum_id),
            'tanggal_sewa'    => $request->tanggal_sewa,
            'tanggal_kembali' => $request->tanggal_kembali,
            'total_biaya'     => $total,
            'catatan'         => $request->catatan,
            'status'          => $request->status,
            'status_bayar'    => false,
            'denda'           => 0,
        ]);

        foreach ($kostumList as $kostum) {
            $kostum->update(['status' => 1]);
        }

        return redirect()->route('pembayaran.index')
            ->with('success', 'Penyewaan berhasil ditambahkan!');
    }

    /**
     * DETAIL
     */
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

    /**
     * EDIT
     */
    public function edit($id)
    {
        $sewa = Sewa::findOrFail($id);
        $user = Auth::user();

        // Penyewa hanya bisa edit jika status = 0
        if ($sewa->status == 1 && $user->role !== '') {
            return redirect()->route('pembayaran.index');
        }

        $currentIds = json_decode($sewa->kostum_id);

        return view('pages.penyewaan.edit', [
            'sewa'      => $sewa,
            'kostums'   => Kostum::all(),
            'selected'  => $currentIds,
            'penyewas'  => Penyewa::all(),
        ]);
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $sewa = Sewa::findOrFail($id);
        $user = Auth::user();

        // Penyewa hanya bisa update jika status = 0
        if ($sewa->status == 1 && $user->role !== 'admin') {
            return redirect()->route('pembayaran.index')
                ->with('error', 'Penyewaan sudah dikembalikan dan tidak bisa diperbarui');
        }

        $request->validate([
            'penyewa_id'       => 'required|exists:penyewas,id',
            'kostum_id'        => 'required|array',
            'kostum_id.*'      => 'exists:kostums,id',
            'tanggal_sewa'     => 'required|date',
            'tanggal_kembali'  => 'required|date|after_or_equal:tanggal_sewa',
            'catatan'          => 'nullable|string',
            'denda'            => 'nullable|integer|min:0',
        ]);

        $oldIds = json_decode($sewa->kostum_id, true) ?? [];
        foreach (Kostum::whereIn('id', $oldIds)->get() as $kostum) {
            $kostum->status = 0;
            $kostum->save();
        }

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

        foreach ($newKostumList as $kostum) {
            $kostum->status = 1;
            $kostum->save();
        }

        return redirect()->route('pembayaran.index')
            ->with('success', 'Penyewaan berhasil diperbarui!');
    }

    /**
     * DESTROY (AJAX)
     */
    public function destroy($id)
    {
        $sewa = Sewa::findOrFail($id);
        $user = Auth::user();

        // Penyewa hanya bisa hapus jika status = 0
        if ($sewa->status == 1 && $user->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Penyewaan sudah dikembalikan dan tidak bisa dibatalkan'
            ], 403);
        }

        try {
            // Kembalikan status kostum → TERSEDIA
            if ($sewa->kostum_id) {
                $ids = json_decode($sewa->kostum_id, true);

                Kostum::whereIn('id', $ids)->update([
                    'status' => 0
                ]);
            }

            // Hapus data sewa
            $sewa->delete();

            return response()->json([
                'status' => true,
                'message' => 'Penyewaan berhasil dibatalkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Penyewaan gagal dibatalkan'
            ], 500);
        }
    }
}
