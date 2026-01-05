@extends('layout.app')

@section('title', 'Pembayaran')

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                Pembayaran: {{ optional($pengembalian->penyewa->user)->name ?? '-' }}
            </h1>
        </div>

        <div class="row justify-content-center">

            {{-- ========================== --}}
            {{-- KOLOM FORM PEMBAYARAN --}}
            {{-- ========================== --}}
            <div class="col-lg-6">
                <div class="card shadow border-left-primary mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Form Pembayaran Kostum</h6>
                    </div>
                    <div class="card-body">

                        {{-- DETAIL PENYEWA --}}
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3 d-flex justify-content-between fw-bold">
                                <span>Nama Penyewa</span><span>:</span>
                            </div>
                            <div class="col-md-9">
                                {{ optional($pengembalian->penyewa->user)->name ?? 'Penyewa sudah dihapus' }}
                            </div>
                        </div>

                        {{-- DETAIL KOSTUM --}}
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3 d-flex justify-content-between fw-bold">
                                <span>Nama Kostum</span><span>:</span>
                            </div>
                            <div class="col-md-9">
                                @forelse($pengembalian->kostum_list as $kostum)
                                    {{ $kostum->nama_kostum }}@if (!$loop->last)
                                        ,
                                    @endif
                                    @empty
                                        <small>Kostum sudah dihapus</small>
                                    @endforelse
                                </div>
                            </div>

                            {{-- TANGGAL --}}
                            <div class="row mb-2 align-items-center">
                                <div class="col-md-3 d-flex justify-content-between fw-bold">
                                    <span>Tanggal Sewa</span><span>:</span>
                                </div>
                                <div class="col-md-9">
                                    {{ date('d F Y', strtotime($pengembalian->tanggal_sewa)) }}
                                </div>
                            </div>

                            <div class="row mb-2 align-items-center">
                                <div class="col-md-3 d-flex justify-content-between fw-bold">
                                    <span>Tanggal Kembali</span><span>:</span>
                                </div>
                                <div class="col-md-9">
                                    {{ date('d F Y', strtotime($pengembalian->tanggal_kembali)) }}
                                </div>
                            </div>

                            {{-- ========================== --}}
                            {{-- FORM PEMBAYARAN (TIDAK DIUBAH) --}}
                            {{-- ========================== --}}

                            <form action="{{ route('pengembalian.update', $pengembalian->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="denda" class="form-label">Denda (Jika Ada)</label>
                                    <input type="number" id="denda" name="denda" class="form-control"
                                        value="{{ old('denda', $pengembalian->denda) }}" placeholder="Masukkan denda">
                                </div>

                                <div class="mb-3">
                                    <label for="total_biaya" class="form-label">Total Biaya Sewa</label>
                                    <input type="number" id="total_biaya" name="total_biaya" class="form-control"
                                        value="{{ old('total_biaya', $pengembalian->total_biaya) }}">
                                </div>

                                {{-- Metode Pembayaran --}}
                                <div class="mb-3">
                                    <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                                    <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                                        <option value="">-- Pilih Metode --</option>
                                        <option value="tunai"
                                            {{ old('metode_pembayaran', $pengembalian->metode_pembayaran) == 'tunai' ? 'selected' : '' }}>
                                            Tunai</option>
                                        <option value="ewallet"
                                            {{ old('metode_pembayaran', $pengembalian->metode_pembayaran) == 'ewallet' ? 'selected' : '' }}>
                                            E-Wallet</option>
                                        <option value="transfer"
                                            {{ old('metode_pembayaran', $pengembalian->metode_pembayaran) == 'transfer' ? 'selected' : '' }}>
                                            Transfer Bank</option>
                                    </select>
                                </div>

                                {{-- Input E-Wallet --}}
                                <div class="mb-3" id="input_ewallet_nama" style="display:none;">
                                    <label class="form-label">Nama E-Wallet</label>
                                    <input type="text" name="nama_ewallet" class="form-control"
                                        value="{{ old('nama_ewallet', $pengembalian->nama_ewallet) }}"
                                        placeholder="DANA / OVO / Gopay">
                                </div>

                                <div class="mb-3" id="input_ewallet_nomor" style="display:none;">
                                    <label class="form-label">Nomor E-Wallet</label>
                                    <input type="text" name="nomor_ewallet" class="form-control"
                                        value="{{ old('nomor_ewallet', $pengembalian->nomor_ewallet) }}"
                                        placeholder="08xxxxxxxxxx">
                                </div>

                                {{-- Input Bank --}}
                                <div class="mb-3" id="input_bank" style="display:none;">
                                    <label class="form-label">Nama Bank</label>
                                    <input type="text" name="nama_bank" class="form-control"
                                        value="{{ old('nama_bank', $pengembalian->nama_bank) }}"
                                        placeholder="BCA / BRI / Mandiri">

                                    <label class="form-label mt-2">No Rekening</label>
                                    <input type="text" name="no_rekening" class="form-control"
                                        value="{{ old('no_rekening', $pengembalian->no_rekening) }}" placeholder="1234567890">
                                </div>

                                <script>
                                    const metode = document.getElementById('metode_pembayaran');
                                    const ewalletNama = document.getElementById('input_ewallet_nama');
                                    const ewalletNomor = document.getElementById('input_ewallet_nomor');
                                    const inputBank = document.getElementById('input_bank');

                                    function tampilkanInput() {
                                        if (metode.value === 'ewallet') {
                                            ewalletNama.style.display = 'block';
                                            ewalletNomor.style.display = 'block';
                                            inputBank.style.display = 'none';
                                        } else if (metode.value === 'transfer') {
                                            inputBank.style.display = 'block';
                                            ewalletNama.style.display = 'none';
                                            ewalletNomor.style.display = 'none';
                                        } else {
                                            inputBank.style.display = 'none';
                                            ewalletNama.style.display = 'none';
                                            ewalletNomor.style.display = 'none';
                                        }
                                    }
                                    tampilkanInput();
                                    metode.addEventListener('change', tampilkanInput);
                                </script>

                                <div class="d-flex justify-content-between mt-3">
                                    <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan Pembayaran
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                {{-- ========================== --}}
                {{-- KOLOM FOTO --}}
                {{-- ========================== --}}
                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-primary text-white">
                            <h6 class="m-0 font-weight-bold">Gambar Kostum</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">

                                @php
                                    $totalFoto = $pengembalian->kostum_list->count();
                                @endphp

                                @foreach ($pengembalian->kostum_list->take(4) as $k)
                                    <div class="col-6 mb-3">
                                        <div class="border rounded p-2 bg-white shadow-sm text-center">
                                            <img src="{{ $k->image_kostum ? asset('storage/' . $k->image_kostum) : asset('images/no-image.png') }}"
                                                class="img-fluid rounded">

                                            <h6 class="mt-2">{{ $k->nama_kostum }}</h6>
                                        </div>
                                    </div>
                                @endforeach

                                @if ($totalFoto > 4)
                                    <div class="col-12 text-center mt-3">
                                        <button class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#modalSemuaFoto">
                                            Lihat Semua Foto ({{ $totalFoto }})
                                        </button>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

            </div> {{-- END ROW --}}
        </div> {{-- END CONTAINER-FLUID --}}

        {{-- ========================== --}}
        {{-- MODAL SEMUA FOTO (dipindah keluar) --}}
        {{-- ========================== --}}
        <div class="modal fade" id="modalSemuaFoto" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Semua Foto Kostum</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body" style="max-height: 70vh; overflow-y:auto;">
                        <div class="row g-3">
                            @foreach ($pengembalian->kostum_list as $k)
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                    <div class="card shadow-sm text-center p-3">
                                        <img src="{{ $k->image_kostum ? asset('storage/' . $k->image_kostum) : asset('images/no-image.png') }}"
                                            class="card-img-top"
                                            style="height: 260px; object-fit: contain; background:#f8f9fa; padding:6px;">
                                        <div class="card-body p-0 fw-bold">{{ $k->nama_kostum }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    <script>
    @if ($errors->any())
        let pesanError = '';
        @foreach ($errors->all() as $error)
            pesanError += '{{ $error }}<br>';
        @endforeach
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            html: pesanError,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    @endif
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    @endif
</script>
@endsection
