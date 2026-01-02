@extends('layout.app')
@section('title', 'Edit Penyewa')

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                Edit Penyewa: {{ $penyewa->user->name }}
            </h1>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow border-left-primary mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Form Edit Penyewa</h6>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('penyewa.update', $penyewa->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- User -->
                            @if (Auth::user()->role === 'admin')
                                <div class="mb-3">
                                    <label class="form-label">User</label>
                                    <select name="user_id" class="form-control" required>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('user_id', $penyewa->user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                            @endif

                            <!-- No Telp -->
                            <div class="mb-3">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="text" name="no_telp"
                                    class="form-control @error('no_telp') is-invalid @enderror"
                                    value="{{ old('no_telp', $penyewa->no_telp) }}" required>
                                @error('no_telp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Alamat -->
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat', $penyewa->alamat) }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <a href="{{ route('penyewa.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
