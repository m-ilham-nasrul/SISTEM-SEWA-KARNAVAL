@extends('layout.app')

@section('title', 'Pembayaran')

@section('content')
    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                Pembayaran : {{ optional($sewa->penyewa->user)->name ?? '-' }}
            </h1>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card shadow mb-4 border-left-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Form Pembayaran Kostum</h6>
                    </div>

                    <div class="card-body">

                        {{-- HITUNG TOTAL --}}
                        @php
                            $subtotal = $sewa->total_biaya;
                            $denda = $sewa->denda ?? 0;
                            $totalBayar = $subtotal + $denda;
                        @endphp

                        {{-- DETAIL --}}
                        <div class="row mb-2">
                            <div class="col-md-4 font-weight-bold">Nama Penyewa</div>
                            <div class="col-md-8">
                                : {{ optional($sewa->penyewa->user)->name ?? 'Penyewa dihapus' }}
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4 font-weight-bold">Jumlah Kostum</div>
                            <div class="col-md-8">
                                : {{ $sewa->kostum_list->count() }}
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4 font-weight-bold">Tanggal Sewa</div>
                            <div class="col-md-8">
                                : {{ date('d F Y', strtotime($sewa->tanggal_sewa)) }}
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4 font-weight-bold">Tanggal Kembali</div>
                            <div class="col-md-8">
                                : {{ date('d F Y', strtotime($sewa->tanggal_kembali)) }}
                            </div>
                        </div>

                        {{-- STATUS KONDISI --}}
                        <div class="row mb-2">
                            <div class="col-md-4 font-weight-bold">Status Kondisi</div>
                            <div class="col-md-8">
                                :
                                @if ($sewa->kondisi === 'rusak')
                                    <span class="badge badge-danger">Rusak</span>
                                @else
                                    <span class="badge badge-success">Baik</span>
                                @endif
                            </div>
                        </div>

                        {{-- CATATAN KERUSAKAN --}}
                        @if ($sewa->kondisi === 'rusak' && $sewa->catatan)
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Catatan Kerusakan</div>
                                <div class="col-md-8">
                                    : {{ $sewa->catatan }}
                                </div>
                            </div>
                        @endif

                        {{-- FOTO --}}
                        <div class="mb-3">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalFoto">
                                <i class="fas fa-images"></i> Lihat Foto Kostum
                            </button>
                        </div>

                        {{-- DETAIL --}}
                        <button class="btn btn-success mb-2" data-toggle="modal" data-target="#modalDetail">
                            <i class="fas fa-receipt"></i> Detail Kostum & Total Pembayaran
                        </button>

                        {{-- TOTAL --}}
                        <div class="text-success font-weight-bold mb-3">
                            Total Bayar : Rp {{ number_format($totalBayar) }}
                        </div>

                        {{-- BUTTON --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>

                            @if (!$sewa->status_bayar)
                                <button id="btn-bayar" class="btn btn-success">
                                    <i class="fas fa-money-bill-wave"></i> Bayar Sekarang
                                </button>
                            @else
                                <button class="btn btn-success" disabled>
                                    Sudah Dibayar
                                </button>
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= MODAL FOTO ================= --}}
    <div class="modal fade" id="modalFoto">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Detail Gambar Kostum</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body" style="max-height: 520px; overflow-y: auto;">
                    <div class="row g-3">
                        @foreach ($sewa->kostum_list as $k)
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card shadow-sm text-center p-3">
                                    <img src="{{ $k->image_kostum ? asset('uploads/kostum/' . $k->image_kostum) : asset('images/no-image.png') }}"
                                        class="card-img-top"
                                        style="height: 260px; width: 100%; object-fit: contain; background:#f8f9fa; padding:6px;">
                                    <div class="fw-bold mt-2">{{ $k->nama_kostum }}</div>
                                    <div class="text-muted small mt-1">Rp {{ number_format($k->harga) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= MODAL DETAIL ================= --}}
    <div class="modal fade" id="modalDetail">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5>Detail Pembayaran</h5>
                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <ul class="list-group mb-3">
                        @foreach ($sewa->kostum_list as $k)
                            <li class="list-group-item d-flex justify-content-between">
                                {{ $k->nama_kostum }}
                                <span>Rp {{ number_format($k->harga) }}</span>
                            </li>
                        @endforeach

                        @if ($sewa->kondisi === 'rusak')
                            <li class="list-group-item d-flex justify-content-between text-danger">
                                <strong>Denda</strong>
                                <strong>Rp {{ number_format($denda) }}</strong>
                            </li>
                        @endif

                        @if ($sewa->kondisi === 'rusak' && $sewa->catatan)
                            <li class="list-group-item">
                                <strong>Catatan:</strong><br>
                                {{ $sewa->catatan }}
                            </li>
                        @endif
                    </ul>

                    <div class="alert alert-success text-center font-weight-bold">
                        Total : Rp {{ number_format($totalBayar) }}
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- ================= LOADING ================= --}}
    <div id="loading"
        style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;
        background:rgba(255,255,255,0.8);z-index:9999;justify-content:center;align-items:center">
        <div class="text-center">
            <div class="spinner-border text-primary mb-2"></div>
            <p>Memproses...</p>
        </div>
    </div>

@endsection

@push('addon-script')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>

    <script>
        $(function() {
            $('#btn-bayar').click(function(e) {
                e.preventDefault();
                let id = {{ $sewa->id }};
                $('#loading').fadeIn();

                // AJAX GET snap token
                $.ajax({
                    url: `/pembayaran/${id}/snap-token`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $('#loading').fadeOut();

                        if (!res.status) {
                            Swal.fire('Error', res.message, 'error');
                            return;
                        }

                        // Panggil Midtrans Snap
                        snap.pay(res.snap_token, {
                            onSuccess: function(result) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pembayaran Berhasil',
                                    text: 'Terima kasih, pembayaran sudah diterima!',
                                    confirmButtonText: 'OK',
                                    timer: 5000,
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('pembayaran.index') }}";
                                });
                            },
                            onPending: function(result) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Menunggu Pembayaran',
                                    text: 'Silakan selesaikan pembayaran.'
                                });
                            },
                            onError: function(result) {
                                Swal.fire('Error',
                                    'Pembayaran gagal. Silakan coba lagi',
                                    'error');
                            },
                            onClose: function() {
                                console.log('Payment popup ditutup');
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        $('#loading').fadeOut();
                        Swal.fire('Error', 'Gagal koneksi server. Silakan refresh halaman',
                            'error');
                    }
                });
            });
        });
    </script>
@endpush
