@extends('layout.app')
@section('title', 'Pilih Kostum')

@push('addon-style')
    <link rel="stylesheet" href="{{ asset('css/select.css') }}">
@endpush

@section('content')
    <div class="container mt-4">
        <h3 class="mb-4 fw-bold">Pilih Kostum untuk Disewa</h3>

        <div class="mb-4 d-flex justify-content-start">
            <div class="btn-group" role="group">
                <button class="btn btn-dark filter-btn" data-kategori="all">All</button>
                <button class="btn btn-danger filter-btn" data-kategori="ogoh_ogoh">Ogoh-ogoh</button>
                <button class="btn btn-primary filter-btn" data-kategori="full_body">Full Body</button>
                <button class="btn btn-success filter-btn" data-kategori="kostum">Kostum</button>
            </div>
        </div>

        <form action="{{ route('penyewaan.create') }}" method="GET">
            <div class="row g-4">

                @foreach ($kostums as $item)
                    <div class="col-lg-3 col-md-4 col-sm-6 kostum-item"
                        data-kategori="{{ Str::slug($item->kategori, '_') }}">
                        <label class="kostum-wrapper w-100">
                            <input class="kostum-check" type="checkbox" name="kostum_id[]" value="{{ $item->id }}">

                            <div class="card pilih-item shadow-sm text-center p-3 kostum-card">
                                <img src="{{ asset('storage/' . $item->image_kostum) }}" class="card-img-top mb-3"
                                    alt="{{ $item->nama_kostum }}"
                                    style="height:260px; width:100%; object-fit:contain; border-radius:10px; background:#f8f9fa; padding:4px;">
                                <div class="card-body p-0">
                                    <div class="nama-kostum fw-bold">{{ $item->nama_kostum }}</div>
                                    <div class="harga text-muted">Rp {{ number_format($item->harga) }}</div>
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

@push('addon-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.filter-btn');
            const items = document.querySelectorAll('.kostum-item');

            buttons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const kategori = this.dataset.kategori;

                    buttons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    items.forEach(item => {
                        item.style.display = (kategori === 'all' || item.dataset
                            .kategori === kategori) ? '' : 'none';
                    });
                });
            });
        });
    </script>
@endpush
