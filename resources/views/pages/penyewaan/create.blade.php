@extends('layout.app')
@section('title', 'Tambah Penyewaan')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Form Penyewaan</h1>
        </div>
        <!-- START FORM -->
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-11">

                <div class="card shadow mb-4 border-left-primary">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="m-0 font-weight-bold">Form Penyewaan</h6>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('penyewaan.store') }}" method="POST" novalidate>
                            @csrf

                            <!-- Penyewa -->
                            <div class="form-group">
                                <label>Nama Penyewa</label>

                                @if (Auth::user()->role === 'admin')
                                    <!-- ADMIN BOLEH PILIH -->
                                    <select name="penyewa_id"
                                        class="form-control @error('penyewa_id') is-invalid @enderror">
                                        <option value="">[ Pilih Penyewa ]</option>

                                        @forelse($penyewas as $penyewa)
                                            <option value="{{ $penyewa->id }}">
                                                {{ $penyewa->user->name }}
                                            </option>
                                        @empty
                                            <option disabled>Data penyewa kosong</option>
                                        @endforelse
                                    </select>

                                    @error('penyewa_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @else
                                    <!-- PENYEWA LOGIN (AUTO) -->
                                    <input type="text" class="form-control" value="{{ $penyewa->user->name }}" readonly>

                                    <!-- kirim ID secara tersembunyi -->
                                    <input type="hidden" name="penyewa_id" value="{{ $penyewa->id }}">
                                @endif
                            </div>

                            <!-- FOTO KOSTUM (banyak) -->
                            <div class="mb-3 text-left">
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#modalSemuaFoto">
                                    <i class="fas fa-images"></i> Lihat Foto Kostum
                                </button>
                            </div>

                            <!-- MODAL SEMUA FOTO -->
                            <div class="modal fade" id="modalSemuaFoto" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Detail Gambar Kostum</h5>
                                            <button type="button" class="close text-white"
                                                data-dismiss="modal">&times;</button>
                                        </div>

                                        <div class="modal-body" style="max-height: 520px; overflow-y: auto;">
                                            <div class="row g-3">
                                                @foreach ($kostums as $k)
                                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                                        <div class="card shadow-sm text-center p-3">
                                                            <img src="{{ $k->image_kostum ? asset('uploads/kostum/' . $k->image_kostum) : asset('images/no-image.png') }}"
                                                                class="card-img-top"
                                                                style="height: 260px; width: 100%; object-fit: contain; background:#f8f9fa; padding:6px;">
                                                            <div class="fw-bold mt-2">{{ $k->nama_kostum }}</div>
                                                            <div class="text-muted small mt-1">Rp
                                                                {{ number_format($k->harga) }}</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Detail Kostum Dipilih -->
                            <div class="mb-3 text-left">
                                <button type="button" class="btn btn-success" data-toggle="modal"
                                    data-target="#modalDetailSewa">
                                    <i class="fas fa-receipt"></i> Detail Kostum & Total Pembayaran
                                </button>
                                @php
                                    $totalBayar = $kostums->sum('harga');
                                @endphp
                                @php
                                    $dp = $totalBayar * 0.5;
                                    $sisaBayar = $totalBayar - $dp;
                                @endphp
                                <div class="mt-2 font-weight-bold text-success">
                                    Total Biaya :
                                    <span id="totalBayar">Rp {{ number_format($totalBayar) }}</span>
                                </div>

                                <div class="mt-2 font-weight-bold text-primary">
                                    <span id="labelBayar">DP (50%)</span> :
                                    <span id="nominalBayar">Rp {{ number_format($dp) }}</span>
                                </div>

                                <div class="mt-2 font-weight-bold text-warning">
                                    Sisa Pembayaran :
                                    <span id="sisaBayar">Rp {{ number_format($sisaBayar) }}</span>
                                </div>
                            </div>

                            <!-- MODAL DETAIL SEWA -->
                            <div class="modal fade" id="modalDetailSewa" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                    <div class="modal-content">

                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Detail Kostum & Total Pembayaran</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal">
                                                &times;</button>
                                        </div>

                                        <div class="modal-body">
                                            <ul class="list-group mb-3">
                                                @foreach ($kostums as $k)
                                                    <li class="list-group-item d-flex justify-content-between">
                                                        {{ $k->nama_kostum }}
                                                        <span>Rp {{ number_format($k->harga) }}</span>
                                                        <input type="hidden" name="kostum_id[]"
                                                            value="{{ $k->id }}">
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <div class="alert alert-info text-center font-weight-bold">
                                                <span id="modalLabelBayar">DP (50%)</span> :
                                                <span id="modalNominalBayar">Rp {{ number_format($dp) }}</span>
                                            </div>

                                            <div class="alert alert-warning text-center font-weight-bold">
                                                Sisa Pembayaran :
                                                <span id="modalSisaBayar">Rp {{ number_format($sisaBayar) }}</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Tanggal Sewa -->
                            <div class="form-group">
                                <label for="tanggal_sewa">Tanggal Sewa</label>
                                <input type="date" name="tanggal_sewa" id="tanggal_sewa" min="{{ date('Y-m-d') }}"
                                    class="form-control @error('tanggal_sewa') is-invalid @enderror"
                                    value="{{ old('tanggal_sewa') }}">
                                @error('tanggal_sewa')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Tanggal Kembali -->
                            <div class="form-group">
                                <label for="tanggal_kembali">Tanggal Kembali</label>
                                <input type="date" name="tanggal_kembali" id="tanggal_kembali"
                                    class="form-control @error('tanggal_kembali') is-invalid @enderror"
                                    value="{{ old('tanggal_kembali') }}" readonly>
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
                                <textarea name="catatan" id="catatan" class="form-control @error('catatan') is-invalid @enderror">{{ old('catatan') }}</textarea>
                                @error('catatan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Metode Pembayaran -->
                            <div class="form-group">
                                <label>Metode Pembayaran</label>

                                <select name="metode_pembayaran"
                                    class="form-control @error('metode_pembayaran') is-invalid @enderror">

                                    <option value="dp" {{ old('metode_pembayaran') == 'dp' ? 'selected' : '' }}>
                                        Bayar DP (50%)
                                    </option>

                                    <option value="lunas" {{ old('metode_pembayaran') == 'lunas' ? 'selected' : '' }}>
                                        Bayar Lunas
                                    </option>
                                </select>

                                @error('metode_pembayaran')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="form-group">
                                <label>Status Sewa</label>
                                <input type="text" class="form-control" value="Masa Sewa" readonly>
                                <input type="hidden" name="status" value="0">
                            </div>

                            <a href="{{ route('penyewaan.select') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Penyewaan
                            </button>

                        </form>
                    </div>

                </div>

            </div>
        </div>
        <!-- END FORM -->
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

            const metode = document.querySelector('[name="metode_pembayaran"]');

            const total = {{ $totalBayar }};
            const dp = total * 0.5;
            const sisa = total - dp;

            function formatRupiah(angka) {
                return 'Rp ' + angka.toLocaleString('id-ID');
            }

            function updatePembayaran() {

                if (metode.value === 'dp') {

                    document.getElementById('labelBayar').innerText = 'DP (50%)';
                    document.getElementById('nominalBayar').innerText = formatRupiah(dp);
                    document.getElementById('sisaBayar').innerText = formatRupiah(sisa);

                    document.getElementById('modalLabelBayar').innerText = 'DP (50%)';
                    document.getElementById('modalNominalBayar').innerText = formatRupiah(dp);
                    document.getElementById('modalSisaBayar').innerText = formatRupiah(sisa);

                } else {

                    document.getElementById('labelBayar').innerText = 'Bayar Sekarang';
                    document.getElementById('nominalBayar').innerText = formatRupiah(total);
                    document.getElementById('sisaBayar').innerText = formatRupiah(0);

                    document.getElementById('modalLabelBayar').innerText = 'Bayar Sekarang';
                    document.getElementById('modalNominalBayar').innerText = formatRupiah(total);
                    document.getElementById('modalSisaBayar').innerText = formatRupiah(0);

                }
            }

            metode.addEventListener('change', updatePembayaran);

            updatePembayaran();
        </script>
    @endpush
@endsection
