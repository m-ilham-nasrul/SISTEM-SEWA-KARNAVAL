@extends('layout.app')
@section('title', 'Pilih Kostum')

@push('addon-style')
    <link rel="stylesheet" href="{{ asset('css/select.css') }}">
@endpush

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">Pilih Kostum untuk Disewa</h3>

            <div class="btn-action-group">
                <a href="{{ route('penyewaan.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>

                <button type="button" class="btn btn-primary" id="btnSewa">
                    <i class="fas fa-shopping-cart"></i> Sewa Sekarang
                </button>
            </div>
        </div>

        <div class="mb-4 d-flex justify-content-start">
            <div class="btn-group" role="group">
                <button class="btn btn-dark filter-btn" data-kategori="all">All</button>
                <button class="btn btn-danger filter-btn" data-kategori="ogoh_ogoh">Ogoh-Ogoh</button>
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
                            <input class="kostum-check" type="checkbox" name="kostum_id[]" value="{{ $item->id }}"
                                {{ $item->status == 1 ? 'disabled' : '' }}>

                            <div
                                class="card pilih-item shadow-sm text-center p-3 kostum-card 
                                        {{ $item->status == 1 ? 'kostum-disabled' : '' }}">
                                <img src="{{ asset('uploads/kostum/' . $item->image_kostum) }}" class="card-img-top mb-3"
                                    alt="{{ $item->nama_kostum }}"
                                    style="height:260px; width:100%; object-fit:contain; border-radius:10px; background:#f8f9fa; padding:4px;">
                                <div class="card-body p-0">
                                    <div class="nama-kostum fw-bold">{{ $item->nama_kostum }}</div>
                                    <div class="harga text-muted">Rp {{ number_format($item->harga) }}</div>
                                    @if ($item->status == 1)
                                    <div class="badge badge-danger d-block w-100 mb-2">
                                        <i class="fas fa-times-circle"></i> Sedang Digunakan
                                    </div>
                                    @if ($item->tanggal_sewa && $item->tanggal_kembali)
                                        <div class="small text-muted mb-2">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ \Carbon\Carbon::parse($item->tanggal_sewa)->translatedFormat('d F Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($item->tanggal_kembali)->translatedFormat('d F Y') }}
                                        </div>
                                    @endif
                                    @else
                                        <div class="badge badge-success d-block w-100">
                                            <i class="fas fa-check-circle"></i> Tersedia
                                        </div>
                                    @endif
                                    <div class="mt-2">
                                        <a href="{{ route('penyewaan.kostum.detail', $item->id) }}"
                                            class="btn btn-sm btn-outline-primary w-100" onclick="event.stopPropagation();">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                @endforeach

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
        document.getElementById('btnSewa').addEventListener('click', function() {

            let selected = [];

            document.querySelectorAll('.kostum-check:checked').forEach(cb => {
                selected.push(cb.value);
            });

            if (selected.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Silahkan Pilih Kostum Dulu!',
                    text: 'Minimal Pilih Satu Kostum',
                    timer: 1500,
                    showConfirmButton: false
                });
                return;
            }

            // Redirect ke route dengan query string
            let url = "{{ route('penyewaan.create') }}?kostum_id[]=" + selected.join('&kostum_id[]=');

            window.location.href = url;
        });
    </script>
@endpush
