@extends('layout.app')

@section('title', 'Tambah Penyewaan')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Form Penyewaan</h1>
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

        <!-- START FORM -->
        <div class="row justify-content-center">
            <div class="col-lg-6">

                <div class="card shadow mb-4 border-left-primary">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="m-0 font-weight-bold">Form Penyewaan</h6>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('penyewaan.store') }}" method="POST">
                            @csrf

                            <!-- FOTO KOSTUM (banyak) -->
                            <div class="text-center mb-3">
                                @foreach ($kostums as $k)
                                    <img src="{{ asset('storage/' . $k->image_kostum) }}" class="img-fluid rounded m-1"
                                        style="max-height:130px;">
                                @endforeach
                            </div>

                            <!-- Penyewa -->
                            <div class="form-group">
                                <label>Nama Penyewa</label>

                                @if (Auth::user()->role === 'admin')
                                    <!-- ADMIN BOLEH PILIH -->
                                    <select name="penyewa_id" class="form-control @error('penyewa_id') is-invalid @enderror"
                                        required>
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

                            <!-- Detail Kostum Dipilih -->
                            <h6 class="mb-2">Kostum yang Dipilih:</h6>
                            <ul class="list-group mb-3">
                                @foreach ($kostums as $k)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $k->nama_kostum }}
                                        <span>Rp {{ number_format($k->harga) }}</span>

                                        <!-- Kirim semua kostum_id -->
                                        <input type="hidden" name="kostum_id[]" value="{{ $k->id }}">
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Tanggal Sewa -->
                            <div class="form-group">
                                <label for="tanggal_sewa">Tanggal Sewa</label>
                                <input type="date" name="tanggal_sewa" id="tanggal_sewa"
                                    class="form-control @error('tanggal_sewa') is-invalid @enderror"
                                    value="{{ old('tanggal_sewa') }}" required>
                                @error('tanggal_sewa')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Tanggal Kembali -->
                            <div class="form-group">
                                <label for="tanggal_kembali">Tanggal Kembali</label>
                                <input type="date" name="tanggal_kembali" id="tanggal_kembali"
                                    class="form-control @error('tanggal_kembali') is-invalid @enderror"
                                    value="{{ old('tanggal_kembali') }}" required>
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

                            <!-- Status -->
                            <div class="form-group">
                                <label>Status Sewa</label>
                                <select name="status" class="form-control">
                                    <option value="0" {{ old('status', 0) == 0 ? 'selected' : '' }}>Masa Sewa</option>

                                    @if (Auth::user()->role === 'admin')
                                        <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Sudah Kembali
                                        </option>
                                    @endif
                                </select>
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
@endsection
