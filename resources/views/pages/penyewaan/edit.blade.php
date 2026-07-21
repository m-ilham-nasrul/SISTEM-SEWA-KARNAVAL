@extends('layout.app')
@section('title', 'Edit Penyewaan')

@push('addon-style')
    <link rel="stylesheet" href="{{ asset('css/select.css') }}">
@endpush

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Penyewaan</h1>
        </div>
        <!-- START FORM -->
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-11">

                <div class="card shadow mb-4 border-left-primary">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="m-0 font-weight-bold">Edit Penyewaan</h6>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('penyewaan.update', $sewa->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Penyewa -->
                            <div class="form-group">
                                <label>Nama Penyewa</label>

                                @if (Auth::user()->role === 'admin')
                                    <select name="penyewa_id" class="form-control @error('penyewa_id') is-invalid @enderror"
                                        required>

                                        <option value="">[ Pilih Penyewa ]</option>

                                        @foreach ($penyewas as $p)
                                            <option value="{{ $p->id }}"
                                                {{ old('penyewa_id', $sewa->penyewa_id) == $p->id ? 'selected' : '' }}>
                                                {{ $p->user->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('penyewa_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @else
                                    <!-- Penyewa login otomatis -->
                                    <input type="text" class="form-control" value="{{ $sewa->penyewa->user->name }}"
                                        readonly>

                                    <input type="hidden" name="penyewa_id" value="{{ $sewa->penyewa_id }}">
                                @endif
                            </div>

                            <!-- NO TELEPON  -->
                            <div class="form-group">
                                <label>No Telepon Penyewa</label>
                                <input type="text" class="form-control" value="{{ $sewa->penyewa->no_telp ?? '-' }}"
                                    readonly>
                            </div>

                            <!-- Tombol Kostum -->
                            <div class="form-group">
                                <label>Pilih Kostum</label><br>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#modalKostum">
                                    Pilih Kostum
                                </button>
                                <ul class="mt-2">
                                    @foreach ($sewa->details as $d)
                                        <li>{{ $d->kostum->nama_kostum }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Modal Kostum -->
                            <div class="modal fade" id="modalKostum">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">

                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Pilih Kostum</h5>
                                            <button class="close text-white" data-dismiss="modal">&times;</button>
                                        </div>

                                        <!-- Tambahkan scroll disini -->
                                        <div class="modal-body" style="max-height: 520px; overflow-y: auto;">
                                            <div class="row g-3">
                                                @php $selected = $sewa->details->pluck('kostum_id')->toArray(); @endphp

                                                @foreach ($kostums as $item)
                                                    @php
                                                        $checked = in_array($item->id, $selected);

                                                        $disabled = $item->sedangDipakai($sewa->id);
                                                    @endphp

                                                    <div class="col-lg-3 col-md-4 col-sm-6">

                                                        <label class="kostum-wrapper w-100">

                                                            <input class="kostum-check" type="checkbox" name="kostum_id[]"
                                                                value="{{ $item->id }}"
                                                                data-harga="{{ $item->harga }}"
                                                                data-nama="{{ $item->nama_kostum }}"
                                                                {{ $checked ? 'checked' : '' }}
                                                                {{ $disabled ? 'disabled' : '' }}>

                                                            <div
                                                                class="card pilih-item shadow-sm text-center p-3 kostum-card
                                                                {{ $disabled ? 'kostum-disabled' : '' }}">

                                                                <img src="{{ asset('uploads/kostum/' . $item->image_kostum) }}"
                                                                    class="card-img-top mb-3"
                                                                    style="height:260px;width:100%;object-fit:contain;background:#f8f9fa;padding:4px;">

                                                                <div class="card-body p-0">

                                                                    <div class="nama-kostum fw-bold">
                                                                        {{ $item->nama_kostum }}
                                                                    </div>

                                                                    <div class="harga text-muted">
                                                                        Rp {{ number_format($item->harga) }}
                                                                    </div>

                                                                    @if ($disabled)
                                                                        <div class="badge badge-danger d-block w-100">
                                                                            Sedang Digunakan
                                                                        </div>
                                                                    @else
                                                                        <div class="badge badge-success d-block w-100">
                                                                            Tersedia
                                                                        </div>
                                                                    @endif

                                                                </div>

                                                            </div>

                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" data-dismiss="modal">Selesai</button>
                                        </div>

                                    </div>
                                </div>
                            </div>



                            <div class="modal fade" id="modalDetailPembayaran">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">

                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">
                                                Detail Kostum & Pembayaran
                                            </h5>

                                            <button class="close text-white" data-dismiss="modal">
                                                &times;
                                            </button>
                                        </div>

                                        <div class="modal-body">

                                            <ul class="list-group mb-3" id="listKostum">

                                            </ul>

                                            <div class="alert alert-success text-center font-weight-bold">

                                                Total :

                                                <span id="modalTotalBayar">

                                                    Rp {{ number_format($totalBayar) }}

                                                </span>

                                            </div>

                                            <div id="alertBayar" class="alert alert-info text-center font-weight-bold">

                                                {{ $metode == 'dp' ? 'DP (50%)' : 'Bayar Sekarang' }}

                                                :
                                                Rp {{ number_format($metode == 'dp' ? $dp : $totalBayar) }}

                                            </div>

                                            <div id="alertSisa" class="alert alert-warning text-center font-weight-bold">

                                                Sisa Pembayaran :
                                                Rp {{ number_format($metode == 'dp' ? $sisaBayar : 0) }}

                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">

                                <button type="button" class="btn btn-success" data-toggle="modal"
                                    data-target="#modalDetailPembayaran">
                                    <i class="fas fa-receipt"></i>
                                    Detail Kostum & Total Pembayaran
                                </button>

                                <div class="mt-3 font-weight-bold text-success">
                                    Total Bayar :
                                    <span id="totalBayar">
                                        Rp {{ number_format($totalBayar) }}
                                    </span>
                                </div>

                                <div class="mt-2 font-weight-bold text-primary">
                                    <span id="labelBayar">
                                        {{ $metode == 'dp' ? 'DP (50%)' : 'Bayar Sekarang' }}
                                    </span> :
                                    <span id="jumlahBayar">
                                        Rp {{ number_format($metode == 'dp' ? $dp : $totalBayar) }}
                                    </span>
                                </div>

                                <div class="mt-2 font-weight-bold text-warning">
                                    Sisa Pembayaran :
                                    <span id="sisaBayar">
                                        Rp {{ number_format($metode == 'dp' ? $sisaBayar : 0) }}
                                    </span>
                                </div>

                            </div>
                            <!-- Tanggal Sewa -->
                            <div class="form-group">
                                <label for="tanggal_sewa">Tanggal Sewa</label>
                                <input type="date" name="tanggal_sewa" id="tanggal_sewa" min="{{ date('Y-m-d') }}"
                                    class="form-control @error('tanggal_sewa') is-invalid @enderror"
                                    value="{{ old('tanggal_sewa', $sewa->tanggal_sewa->format('Y-m-d')) }}">

                                @error('tanggal_sewa')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Tanggal Kembali -->
                            <div class="form-group">
                                <label for="tanggal_kembali">Tanggal Kembali</label>

                                <input type="date" name="tanggal_kembali" id="tanggal_kembali"
                                    class="form-control @error('tanggal_kembali') is-invalid @enderror"
                                    value="{{ old('tanggal_kembali', $sewa->tanggal_kembali->format('Y-m-d')) }}"
                                    readonly>

                                <small class="text-muted">
                                    Tanggal kembali otomatis 7 hari setelah tanggal sewa.
                                </small>

                                @error('tanggal_kembali')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Catatan -->
                            <div class="form-group">
                                <label for="catatan">Catatan</label>
                                <textarea name="catatan" id="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror">{{ old('catatan', $sewa->catatan) }}</textarea>
                                @error('catatan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Metode Pembayaran -->
                            <div class="form-group">

                                <label>Metode Pembayaran</label>

                                <select name="metode_pembayaran" id="metode_pembayaran" class="form-control">

                                    <option value="dp" {{ $metode == 'dp' ? 'selected' : '' }}>
                                        Bayar DP (50%)
                                    </option>

                                    <option value="lunas" {{ $metode == 'lunas' ? 'selected' : '' }}>
                                        Bayar Lunas
                                    </option>

                                </select>

                            </div>

                            <!-- Status -->
                            <div class="form-group">
                                <label>Status Penyewaan</label>

                                <input type="text" class="form-control"
                                    value="{{ $sewa->status == 0 ? 'Masa Sewa' : 'Sudah Kembali' }}" readonly>

                                <input type="hidden" name="status" value="{{ $sewa->status }}">
                            </div>

                            <!-- Tombol -->
                            <a href="{{ route('penyewaan.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.getElementById('tanggal_sewa').addEventListener('change', function() {

                if (!this.value) return;

                let tanggal = new Date(this.value);

                tanggal.setDate(tanggal.getDate() + 7);

                let tahun = tanggal.getFullYear();
                let bulan = String(tanggal.getMonth() + 1).padStart(2, '0');
                let hari = String(tanggal.getDate()).padStart(2, '0');

                document.getElementById('tanggal_kembali').value =
                    `${tahun}-${bulan}-${hari}`;
            });

            const metode = document.getElementById('metode_pembayaran');
            const checks = document.querySelectorAll('.kostum-check');

            function rupiah(x) {
                return 'Rp ' + x.toLocaleString('id-ID');
            }

            function hitung() {

                let total = 0;

                let html = '';

                checks.forEach(function(item) {

                    if (item.checked) {

                        let harga = parseInt(item.dataset.harga);

                        total += harga;

                        html += `
                            <li class="list-group-item d-flex justify-content-between">
                                ${item.dataset.nama}
                                <span>${rupiah(harga)}</span>
                            </li>
                        `;
                    }

                });

                document.getElementById('listKostum').innerHTML = html;

                let dp = total * 0.5;

                let sisa = total - dp;

                document.getElementById('totalBayar').innerHTML =
                    rupiah(total);

                document.getElementById('modalTotalBayar').innerHTML =
                    rupiah(total);

                if (metode.value == 'dp') {

                    document.getElementById('labelBayar').innerHTML = 'DP (50%)';

                    document.getElementById('jumlahBayar').innerHTML = rupiah(dp);

                    document.getElementById('sisaBayar').innerHTML = rupiah(sisa);

                    document.getElementById('alertBayar').innerHTML =
                        'DP (50%) : ' + rupiah(dp);

                    document.getElementById('alertSisa').innerHTML =
                        'Sisa Pembayaran : ' + rupiah(sisa);

                } else {

                    document.getElementById('labelBayar').innerHTML = 'Bayar Sekarang';

                    document.getElementById('jumlahBayar').innerHTML = rupiah(total);

                    document.getElementById('sisaBayar').innerHTML = rupiah(0);

                    document.getElementById('alertBayar').innerHTML =
                        'Bayar Sekarang : ' + rupiah(total);

                    document.getElementById('alertSisa').innerHTML =
                        'Sisa Pembayaran : ' + rupiah(0);

                }

            }

            checks.forEach(function(item) {

                item.addEventListener('change', hitung);

            });

            metode.addEventListener('change', hitung);

            hitung();
        </script>
    @endpush
@endsection
