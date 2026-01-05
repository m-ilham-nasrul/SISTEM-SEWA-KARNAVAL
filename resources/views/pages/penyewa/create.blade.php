@extends('layout.app')
@section('title', 'Tambah Penyewa')

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Tambah Penyewa Baru</h1>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Oops!</strong> Ada beberapa kesalahan:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow border-left-primary mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Form Tambah Penyewa</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('penyewa.store') }}" method="POST">
                            @csrf

                            <!-- User -->
                            <div class="mb-3">
                                <label for="user_id" class="form-label">User</label>

                                @if (Auth::user()->role === 'admin')
                                    <!-- ADMIN BOLEH PILIH USER -->
                                    <select name="user_id" id="user_id"
                                        class="form-control @error('user_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih User --</option>

                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @else
                                    <!-- PENYEWA LOGIN (AUTO) -->
                                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>

                                    <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                                @endif
                            </div>

                            <!-- Nomor Telepon -->
                            <div class="mb-3">
                                <label for="no_telp" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control @error('no_telp') is-invalid @enderror"
                                    id="no_telp" name="no_telp" value="{{ old('no_telp') }}" required>
                                @error('no_telp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Alamat -->
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror" name="alamat" id="alamat" rows="3"
                                    required>{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tombol -->
                            <a href="{{ route('penyewa.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Penyewa
                            </button>

                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
