@extends('layout.app')

@section('title', 'Detail Kostum')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Detail Kostum: {{ $kostum->nama_kostum }}
        </h1>
    </div>

    <div class="row justify-content-center">

        <!-- Kolom Gambar -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Gambar Kostum</h6>
                </div>
                <div class="card-body text-center">
                    @if ($kostum->image_kostum)
                        <img src="{{ asset('uploads/kostum/' . $kostum->image_kostum) }}"
                             class="img-fluid img-thumbnail"
                             style="max-height: 600px;"
                             loading="lazy">
                    @else
                        <small class="text-muted">Tidak ada gambar</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Detail -->
        <div class="col-lg-6">
            <div class="card shadow border-left-primary mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Informasi Kostum</h6>
                </div>
                <div class="card-body">

                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Nama Kostum</th>
                            <td>{{ $kostum->nama_kostum }}</td>
                        </tr>

                        <tr>
                            <th>Kategori</th>
                            <td>{{ $kostum->kategori }}</td>
                        </tr>

                        <tr>
                            <th>Harga Sewa</th>
                            <td>Rp {{ number_format($kostum->harga, 0, ',', '.') }}</td>
                        </tr>

                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $kostum->catatan ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td>
                                @if ($kostum->status)
                                    <span class="badge badge-danger">Sedang Digunakan</span>
                                @else
                                    <span class="badge badge-success">Tersedia</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <!-- Tombol -->
                    <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>

                    {{-- OPTIONAL: langsung sewa --}}
                    @if (!$kostum->status)
                        <a href="{{ route('penyewaan.create') }}?kostum_id[]={{ $kostum->id }}" 
                           class="btn btn-primary mt-3">
                            <i class="fas fa-shopping-cart"></i> Sewa Kostum
                        </a>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>
@endsection