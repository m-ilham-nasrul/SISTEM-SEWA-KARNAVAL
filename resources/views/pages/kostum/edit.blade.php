@extends('layout.app')

@section('title', 'Edit Kostum')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Kostum: {{ $kostum->nama_kostum }}</h1>
        </div>

        <!-- Notifikasi Error -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Edit Kostum -->
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow border-left-primary mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Form Edit Kostum</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('kostum.update', $kostum->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Gambar Kostum -->
                            <div class="mb-3">
                                <label for="image_kostum" class="form-label">Gambar Kostum</label>
                                <div class="mb-2">
                                    @if ($kostum->image_kostum)
                                        <img src="{{ asset('storage/' . $kostum->image_kostum) }}" alt="Gambar Kostum"
                                            class="img-thumbnail" style="max-width: 150px;">
                                    @endif
                                </div>
                                <input type="file" class="form-control @error('image_kostum') is-invalid @enderror"
                                    id="image_kostum" name="image_kostum">
                                @error('image_kostum')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nama Kostum -->
                            <div class="mb-3">
                                <label for="nama_kostum" class="form-label">Nama Kostum</label>
                                <input type="text" class="form-control @error('nama_kostum') is-invalid @enderror"
                                    id="nama_kostum" name="nama_kostum"
                                    value="{{ old('nama_kostum', $kostum->nama_kostum) }}">
                                @error('nama_kostum')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <!-- Kategori -->
                            <div class="mb-3">
                                <label for="kategori" class="form-label">Kategori Kostum</label>
                                <select name="kategori" id="kategori"
                                    class="form-control @error('kategori') is-invalid @enderror">
                                    <option value="">[ Pilih Kategori Kostum ]</option>
                                    <option value="Kostum"
                                        {{ old('kategori', $kostum->kategori) === 'Kostum' ? 'selected' : '' }}>Kostum
                                    </option>
                                    <option value="Full Body"
                                        {{ old('kategori', $kostum->kategori) === 'Full Body' ? 'selected' : '' }}>Full Body
                                    </option>
                                    <option value="Ogoh-Ogoh"
                                        {{ old('kategori', $kostum->kategori) === 'Ogoh-Ogoh' ? 'selected' : '' }}>
                                        Ogoh-Ogoh</option>
                                </select>
                                @error('kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Catatan -->
                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea class="form-control @error('catatan') is-invalid @enderror" name="catatan" id="catatan" rows="3">{{ old('catatan', $kostum->catatan) }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Harga -->
                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga Sewa (Rp)</label>
                                <input type="number" class="form-control @error('harga') is-invalid @enderror"
                                    id="harga" name="harga" value="{{ old('harga', $kostum->harga) }}">
                                @error('harga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="mb-3">
                                <label for="status" class="form-label">Status Kostum</label>
                                <select name="status" id="status" class="form-control">
                                    @php
                                        $sewaAktif = $kostum->sewas()->where('status', 1)->exists();
                                    @endphp
                                    <option value="0" {{ !$sewaAktif && $kostum->status == 0 ? 'selected' : '' }}>
                                        Tersedia</option>
                                    <option value="1" {{ $sewaAktif || $kostum->status == 1 ? 'selected' : '' }}>
                                        Sedang Digunakan</option>
                                </select>
                            </div>


                            <!-- Tombol -->
                            <a href="{{ route('kostum.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
