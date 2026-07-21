<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Enums\StatusBayar;
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
                    'kode_sewa' => $sewa->kode_sewa
                        ?? 'SEWA-' . str_pad($sewa->id, 4, '0', STR_PAD_LEFT),
                    'status' => $sewa->status,
                    'status_bayar' => $sewa->status_bayar,
                    'metode_pembayaran' => $sewa->metode_pembayaran,
                    'denda' => $sewa->denda,
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

    public function select()
    {
        $kostums = Kostum::all();
        return view('pages.penyewaan.select', compact('kostums'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate(
            [
                'kostum_id'       => 'required|array',
                'kostum_id.*'     => 'exists:kostums,id',
                'tanggal_sewa' => 'required|date|after_or_equal:today',
                // tanggal_kembali tidak perlu divalidasi dari input
                'catatan'         => 'nullable|string',
                'metode_pembayaran' => 'required|in:dp,lunas',
            ],
            [
                'required' => ':attribute wajib diisi.',
                'date' => ':attribute tidak valid.',
                'after_or_equal' => ':attribute tidak boleh sebelum hari ini.',
                'array' => ':attribute wajib dipilih.',
                'exists' => ':attribute tidak ditemukan.',
            ],
            [
                'kostum_id' => 'Kostum',
                'tanggal_sewa' => 'Tanggal Sewa',
                'tanggal_kembali' => 'Tanggal Kembali',
            ]
        );

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

        if ($request->metode_pembayaran == 'dp') {

            $dp = $total * 0.5;
            $sisaBayar = $total - $dp;
        } else {

            $dp = 0;
            $sisaBayar = 0;
        }

        $statusBayar = StatusBayar::PENDING;

        $sewa = Sewa::create([
            'kode_sewa'       => 'SEWA-' . now()->format('YmdHis'),
            'penyewa_id'      => $penyewaId,
            'tanggal_sewa'    => $request->tanggal_sewa,
            'tanggal_kembali' => Carbon::parse($request->tanggal_sewa)->addDays(7),
            'total_biaya'     => $total,
            'dp'              => $dp,
            'sisa_bayar'      => $sisaBayar,
            'catatan'         => $request->catatan,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status'          => 0,
            'status_bayar' => $statusBayar,

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

        return redirect()->route('penyewaan.index')
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
        $sewa = Sewa::with(['details.kostum', 'penyewa.user'])
            ->findOrFail($id);

        if ($sewa->status_bayar !== StatusBayar::PENDING) {
            return redirect()->route('penyewaan.index')
                ->with('error', 'Penyewaan tidak dapat diubah karena DP sudah dibayar.');
        }

        $currentIds = $sewa->details->pluck('kostum_id')->toArray();

        $totalBayar = $sewa->total_biaya;
        $dp         = $sewa->dp;
        $sisaBayar  = $sewa->sisa_bayar;

        $metode = old(
            'metode_pembayaran',
            $sewa->metode_pembayaran
        );

        return view('pages.penyewaan.edit', [
            'sewa'        => $sewa,
            'kostums'     => Kostum::all(),
            'selected'    => $currentIds,
            'penyewas'    => Penyewa::all(),

            'totalBayar'  => $totalBayar,
            'dp'          => $dp,
            'sisaBayar'   => $sisaBayar,
            'metode'      => $metode,
        ]);
    }

    public function update(Request $request, $id)
    {
        $sewa = Sewa::with(['details.kostum', 'penyewa.user'])->findOrFail($id);
        $user = Auth::user();

        // penyewa tidak boleh update jika sudah dikembalikan
        if ($sewa->status_bayar !== StatusBayar::PENDING) {
            return redirect()->route('penyewaan.index')
                ->with('error', 'Penyewaan tidak dapat diperbarui karena DP sudah dibayar.');
        }

        $request->validate(
            [
                'kostum_id'        => 'required|array',
                'kostum_id.*'      => 'exists:kostums,id',
                'tanggal_sewa' => 'required|date|after_or_equal:today',
                // tanggal_kembali tidak perlu divalidasi dari input
                'catatan'          => 'nullable|string',
                'status'             => 'nullable|integer',
                'metode_pembayaran'  => 'required|in:dp,lunas',
            ],
            [
                'required' => ':attribute wajib diisi.',
                'date' => ':attribute tidak valid.',
                'after_or_equal' => ':attribute tidak boleh sebelum hari ini.',
                'array' => ':attribute wajib dipilih.',
                'exists' => ':attribute tidak ditemukan.',
                'numeric' => ':attribute harus berupa angka.',
                'min' => ':attribute minimal :min.',
            ],
            [
                'kostum_id' => 'Kostum',
                'tanggal_sewa' => 'Tanggal Sewa',
                'tanggal_kembali' => 'Tanggal Kembali',
                'status' => 'Status',
                'dp' => 'DP',
            ]
        );
        if ($user->role === 'penyewa') {
            $penyewaId = $user->penyewa->id;
        } else {
            $request->validate([
                'penyewa_id' => 'required|exists:penyewas,id'
            ]);

            $penyewaId = $request->penyewa_id;
        }

        // =========================
        // 1. Ambil kostum lama dari detail
        // =========================
        $oldKostumIds = $sewa->details->pluck('kostum_id')->toArray();

        // =========================
        // 2. Validasi kostum baru
        // =========================
        foreach ($request->kostum_id as $idKostum) {

            $kostum = Kostum::findOrFail($idKostum);

            if ($kostum->sedangDipakai($sewa->id)) {

                return back()
                    ->withInput()
                    ->with('error', "Kostum {$kostum->nama_kostum} sedang digunakan.");
            }
        }
        // =========================
        // 3. Kembalikan status kostum lama
        // =========================
        Kostum::whereIn('id', $oldKostumIds)->update([
            'status' => 0
        ]);

        // =========================
        // 4. Hapus detail lama
        // =========================
        $sewa->details()->delete();

        // =========================
        // 5. Ambil kostum baru
        // =========================
        $newKostums = Kostum::whereIn('id', $request->kostum_id)->get();

        // =========================
        // 6. Hitung total
        // =========================
        $total = $newKostums->sum('harga');
        if ($request->metode_pembayaran == 'dp') {

            $dp = $total * 0.5;
            $sisaBayar = $total - $dp;
        } else {

            $dp = 0;
            $sisaBayar = 0;
        }
        $statusBayar = StatusBayar::PENDING;

        // =========================
        // 7. Update sewa utama
        // =========================
        $sewa->update([
            'penyewa_id'      => $penyewaId,
            'tanggal_sewa'    => $request->tanggal_sewa,
            'tanggal_kembali' => Carbon::parse($request->tanggal_sewa)->addDays(7),
            'catatan'         => $request->catatan,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status'          => $request->status,
            'denda'           => $request->denda ?? 0,

            'total_biaya'     => $total,
            'dp'              => $dp,
            'sisa_bayar'      => $sisaBayar,
            'status_bayar'    => $statusBayar,
        ]);

        // =========================
        // 8. Insert detail baru
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

        return redirect()->route('penyewaan.index')
            ->with('success', 'Penyewaan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $sewa = Sewa::findOrFail($id);
        $user = Auth::user();

        if ($sewa->status_bayar !== StatusBayar::PENDING) {
            return response()->json([
                'status' => false,
                'message' => 'Penyewaan tidak dapat dibatalkan karena DP sudah dibayar'
            ], 403);
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
