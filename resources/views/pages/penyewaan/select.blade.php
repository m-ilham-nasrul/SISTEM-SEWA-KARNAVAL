@extends('layout.app')
@section('title', 'Pilih Kostum')

@push('addon-style')
    <link rel="stylesheet" href="{{ asset('css/select.css') }}">
@endpush

@section('content')
    <div class="container mt-4">
        <h3 class="mb-4 fw-bold">Pilih Kostum untuk Disewa</h3>
        <form action="{{ route('penyewaan.create') }}" method="GET">
                <div class="row g-4">

                    @foreach ($kostums as $item)
                        <div class="col-lg-3 col-md-4 col-sm-6">

                            <!-- WRAPPER SELECTABLE CARD -->
                            <label class="kostum-wrapper">

                                <!-- HIDDEN CHECKBOX -->
                                <input class="kostum-check" type="checkbox" name="kostum_id[]" value="{{ $item->id }}">

                                <!-- CARD -->
                                <div class="card pilih-item shadow-sm text-center p-3 kostum-card">

                                    <img src="{{ asset('storage/' . $item->image_kostum) }}" class="card-img-top mb-3"
                                        alt="{{ $item->nama_kostum }}"
                                        style="height: 260px; width: 100%; object-fit: cover; border-radius: 10px;">

                                    <div class="card-body p-0">
                                        <div class="nama-kostum">{{ $item->nama_kostum }}</div>
                                        <div class="harga">Rp {{ number_format($item->harga) }}</div>
                                    </div>
                                </div>

                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="btn-action-group mt-3">
                    <a href="{{ route('penyewaan.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>

                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-cart"></i> Sewa Sekarang
                    </button>
                </div>
        </form>
    </div>
@endsection
