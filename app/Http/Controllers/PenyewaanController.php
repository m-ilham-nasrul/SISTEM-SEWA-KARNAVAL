<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\Penyewa;
use App\Models\Kostum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenyewaanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $user = Auth::user();

            $query = Sewa::with(['penyewa.user', 'details.kostum'])
                ->orderBy('created_at', 'desc');

            // FILTER BERDASARKAN ROLE
            if ($user->role === 'penyewa') {

                if (!$user->penyewa) {
                    return response()->json(['data' => []]);
                }

                $query->where('penyewa_id', $user->penyewa->id);
            }

            $sewas = $query->get();

            $data = $sewas->map(function ($sewa) {
                return [
                    'id' => $sewa->id,
                    'status' => $sewa->status,
                    'status_bayar' => $sewa->status_bayar,
                    'penyewa' => [
                        'user' => [
                            'name' => optional($sewa->penyewa->user)->name
                        ]
                    ],
                    'kostum_list' => $sewa->details->map(function ($d) {
                        return [
                            'id' => $d->kostum->id,
                            'nama_kostum' => $d->kostum->nama_kostum
                        ];
                    }),
                    'tanggal_sewa' => $sewa->tanggal_sewa,
                    'tanggal_kembali' => $sewa->tanggal_kembali,
                ];
            });

            return response()->json(['data' => $data]);
        }

        return view('pages.penyewaan.index');
    }

    public function select()
    {
        $kostums = Kostum::all();
        return view('pages.penyewaan.select', compact('kostums'));
    }

    public function create(Request $request)
    {
        if (!$request->has('kostum_id')) {
            return redirect()->route('penyewaan.select');
        }

        $kostumIds = $request->kostum_id;
        $user = Auth::user();

        return view('pages.penyewaan.create', [
            'kostums' => Kostum::whereIn('id', $kostumIds)->get(),
            'penyewa' => $user->role === 'penyewa' ? $user->penyewa : null,
            'penyewas' => $user->role === 'admin' ? Penyewa::all() : null,
        ]);
    }

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
            'tanggal_sewa'    => $request->tanggal_sewa,
            'tanggal_kembali' => $request->tanggal_kembali,
            'total_biaya'     => $total,
            'catatan'         => $request->catatan,
            'status'          => $request->status,
            'status_bayar'    => false,
            'denda'           => 0,
        ]);

        foreach ($kostumList as $kostum) {
            $sewa->details()->create([
                'kostum_id' => $kostum->id,
                'harga'     => $kostum->harga,
                'qty'       => 1,
                'subtotal'  => $kostum->harga,
            ]);

            $kostum->update(['status' => 1]);
        }

        return redirect()->route('pengembalian.index')
            ->with('success', 'Penyewaan berhasil ditambahkan!');
    }

    public function show($id)
    {
        $sewa = Sewa::findOrFail($id);
        $kostums = $sewa->details->map(function ($d) {
            return $d->kostum;
        });
        $hargaPaket = $kostums->sum('harga');
        $denda = $sewa->denda ?? 0;
        $total = $hargaPaket + $denda;

        return view('pages.penyewaan.show', compact('sewa', 'kostums', 'hargaPaket', 'denda', 'total'));
    }

    public function showkostum($id)
    {
        $kostum = Kostum::findOrFail($id);
        return view('pages.penyewaan.show-kostum', compact('kostum'));
    }

    public function edit($id)
    {
        $sewa = Sewa::findOrFail($id);
        $user = Auth::user();

        // Penyewa hanya bisa edit jika status = 0
        if ($sewa->status == 1 && $user->role !== 'admin') {
            return redirect()->route('pembayaran.index');
        }

        $currentIds = $sewa->details->pluck('kostum_id')->toArray();

        return view('pages.penyewaan.edit', [
            'sewa'      => $sewa,
            'kostums'   => Kostum::all(),
            'selected'  => $currentIds,
            'penyewas'  => Penyewa::all(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $sewa = Sewa::with('details')->findOrFail($id);
        $user = Auth::user();

        // penyewa tidak boleh update jika sudah dikembalikan
        if ($sewa->status == 1 && $user->role !== 'admin') {
            return redirect()->route('pengembalian.index')
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
            'status'           => 'required|in:0,1,2,3',
        ]);

        // =========================
        // 1. Ambil kostum lama dari detail
        // =========================
        $oldKostumIds = $sewa->details->pluck('kostum_id')->toArray();

        // =========================
        // 2. Kembalikan status kostum lama
        // =========================
        Kostum::whereIn('id', $oldKostumIds)->update([
            'status' => 0
        ]);

        // =========================
        // 3. Hapus detail lama
        // =========================
        $sewa->details()->delete();

        // =========================
        // 4. Ambil kostum baru
        // =========================
        $newKostums = Kostum::whereIn('id', $request->kostum_id)->get();

        // =========================
        // 5. Hitung total
        // =========================
        $total = $newKostums->sum('harga') + ($request->denda ?? 0);

        // =========================
        // 6. Update sewa utama
        // =========================
        $sewa->update([
            'penyewa_id'      => $request->penyewa_id,
            'tanggal_sewa'    => $request->tanggal_sewa,
            'tanggal_kembali' => $request->tanggal_kembali,
            'catatan'         => $request->catatan,
            'status'          => $request->status,
            'denda'           => $request->denda ?? 0,
            'total_biaya'     => $total,
        ]);

        // =========================
        // 7. Insert detail baru
        // =========================
        foreach ($newKostums as $kostum) {
            $sewa->details()->create([
                'kostum_id' => $kostum->id,
                'harga'     => $kostum->harga,
                'qty'       => 1,
                'subtotal'  => $kostum->harga,
            ]);

            // ubah status jadi dipakai
            $kostum->update(['status' => 1]);
        }

        return redirect()->route('pengembalian.index')
            ->with('success', 'Penyewaan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $sewa = Sewa::findOrFail($id);
        $user = Auth::user();

        // Penyewa hanya bisa hapus jika status = 0
        if ($sewa->status == 1 && $user->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Penyewaan sudah dikembalikan dan tidak bisa dibatalkan'
            ], 404);
        }

        try {
            $ids = $sewa->details->pluck('kostum_id');

            // kembalikan status kostum
            Kostum::whereIn('id', $ids)->update([
                'status' => 0
            ]);

            // hapus detail
            $sewa->details()->delete();

            // hapus sewa
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
