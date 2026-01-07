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

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow border-left-primary mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Form Edit Penyewaan</h6>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('penyewaan.update', $sewa->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Penyewa -->
                            <div class="form-group">
                                <label>Nama Penyewa</label>

                                <select name="penyewa_id" class="form-control @error('penyewa_id') is-invalid @enderror">
                                    <option value="">[ Pilih Penyewa ]</option>

                                    @foreach ($penyewas as $penyewa)
                                        <option value="{{ $penyewa->id }}"
                                            {{ old('penyewa_id', $sewa->penyewa_id) == $penyewa->id ? 'selected' : '' }}>
                                            {{ $penyewa->user->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('penyewa_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
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
                                    @foreach ($sewa->kostum_list as $k)
                                        <li>{{ $k->nama_kostum }}</li>
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
                                                @php $selected = $sewa->kostum_list->pluck('id')->toArray(); @endphp

                                                @foreach ($kostums as $item)
                                                    <div class="col-lg-3 col-md-4 col-sm-6">

                                                        <label class="kostum-wrapper w-100">
                                                            <input class="kostum-check" type="checkbox" name="kostum_id[]"
                                                                value="{{ $item->id }}"
                                                                {{ in_array($item->id, $selected) ? 'checked' : '' }}>

                                                            <div
                                                                class="card pilih-item shadow-sm text-center p-3 kostum-card">
                                                                <img src="{{ asset('storage/' . $item->image_kostum) }}"
                                                                    class="card-img-top mb-3"
                                                                    alt="{{ $item->nama_kostum }}"
                                                                    style="height: 260px; width: 100%; object-fit: contain; border-radius: 10px; background:#f8f9fa; padding:4px;">

                                                                <div class="card-body p-0">
                                                                    <div class="nama-kostum fw-bold">
                                                                        {{ $item->nama_kostum }}</div>
                                                                    <div class="harga text-muted">Rp
                                                                        {{ number_format($item->harga) }}</div>
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
                            <!-- Tanggal Sewa -->
                            <div class="form-group">
                                <label for="tanggal_sewa">Tanggal Sewa</label>
                                <input type="date" name="tanggal_sewa" id="tanggal_sewa"
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
                                    value="{{ old('tanggal_kembali', $sewa->tanggal_kembali->format('Y-m-d')) }}">
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

                            <!-- Status -->
                            <div class="form-group">
                                <label for="status">Status Penyewaan</label>
                                <select name="status" id="status"
                                    class="form-control @error('status') is-invalid @enderror">
                                    <option value="0" {{ old('status', $sewa->status) == 0 ? 'selected' : '' }}>
                                        Masa Sewa / Belum Kembali
                                    </option>
                                    <option value="1" {{ old('status', $sewa->status) == 1 ? 'selected' : '' }}>
                                        Sudah Kembali
                                    </option>
                                </select>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
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
@endsection
