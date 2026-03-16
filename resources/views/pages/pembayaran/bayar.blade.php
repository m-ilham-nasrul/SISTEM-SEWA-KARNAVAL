@extends('layout.app')

@section('title','Pembayaran')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Pembayaran : {{ optional($pengembalian->penyewa->user)->name ?? '-' }}
        </h1>
    </div>

    <div class="row justify-content-center">
        {{-- FORM PEMBAYARAN --}}
        <div class="col-lg-8">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Form Pembayaran Kostum</h6>
                </div>

                <div class="card-body">

                    {{-- HITUNG TOTAL --}}
                    @php
                    $subtotal = $pengembalian->total_biaya;
                    $denda = $pengembalian->denda ?? 0;
                    $totalBayar = $subtotal + $denda;
                    @endphp

                    {{-- DETAIL PENYEWA --}}
                    <div class="row mb-2">
                        <div class="col-md-3 font-weight-bold">Nama Penyewa</div>
                        <div class="col-md-9">
                            : {{ optional($pengembalian->penyewa->user)->name ?? 'Penyewa sudah dihapus' }}
                        </div>
                    </div>

                    {{-- JUMLAH KOSTUM --}}
                    <div class="row mb-2">
                        <div class="col-md-3 font-weight-bold">Jumlah Kostum</div>
                        <div class="col-md-9">
                            : {{ $pengembalian->kostum_list->count() }}
                        </div>
                    </div>

                    {{-- TANGGAL SEWA --}}
                    <div class="row mb-2">
                        <div class="col-md-3 font-weight-bold">Tanggal Sewa</div>
                        <div class="col-md-9">
                            : {{ date('d F Y', strtotime($pengembalian->tanggal_sewa)) }}
                        </div>
                    </div>

                    {{-- TANGGAL KEMBALI --}}
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Tanggal Kembali</div>
                        <div class="col-md-9">
                            : {{ date('d F Y', strtotime($pengembalian->tanggal_kembali)) }}
                        </div>
                    </div>

                    {{-- STATUS PEMBAYARAN --}}
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Status Pembayaran</div>
                        <div class="col-md-9">
                            :
                            @if($pengembalian->status_bayar == 1)
                                <span class="badge badge-success">Telah Terbayar</span>
                            @else
                                <span class="badge badge-danger">Belum Membayar</span>
                            @endif
                        </div>
                    </div>

                    {{-- TOMBOL FOTO --}}
                    <div class="mb-3">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#modalSemuaFoto">
                            <i class="fas fa-images"></i> Lihat Foto Kostum
                        </button>
                    </div>

                    {{-- DETAIL KOSTUM & TOTAL PEMBAYARAN --}}
                        <button
                            class="btn btn-success mb-2"
                            type="button"
                            data-toggle="modal"
                            data-target="#modalDetailSewa">
                            <i class="fas fa-receipt"></i>
                            Detail Kostum & Total Pembayaran
                        </button>

                    {{-- TOTAL BAYAR --}}
                        <div class="text-success font-weight-bold mb-3">
                            Total Bayar : Rp {{ number_format($totalBayar) }}
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button id="btn-bayar-midtrans" class="btn btn-success">
                                <i class="fas fa-money-bill-wave"></i> Bayar Sekarang
                            </button>
                        </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL FOTO ================= --}}
<div class="modal fade" id="modalSemuaFoto">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Detail Gambar Kostum</h5>
                <button class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body" style="max-height:520px;overflow-y:auto">
                <div class="row">
                    @foreach($pengembalian->kostum_list as $k)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card text-center p-3 shadow-sm">
                                <img
                                    src="{{ $k->image_kostum ? asset('uploads/kostum/'.$k->image_kostum) : asset('images/no-image.png') }}"
                                    style="height:260px;object-fit:contain;background:#f8f9fa">
                                <div class="fw-bold mt-2">{{ $k->nama_kostum }} </div>
                                <div class="text-muted small mt-1"> Rp {{ number_format($k->harga) }} </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ================= MODAL DETAIL SEWA ================= --}}
<div class="modal fade" id="modalDetailSewa">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    Detail Kostum & Total Pembayaran
                </h5>
                <button class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <ul class="list-group mb-3">
                    @foreach($pengembalian->kostum_list as $k)
                    <li class="list-group-item d-flex justify-content-between">
                        {{ $k->nama_kostum }}
                        <span>Rp {{ number_format($k->harga) }}</span>
                    </li>
                    @endforeach
                    @if($denda > 0)
                    <li class="list-group-item d-flex justify-content-between text-danger">
                        <strong>Denda</strong>
                        <strong>Rp {{ number_format($denda) }}</strong>
                    </li>
                    @endif
                </ul>
                <div class="alert alert-success text-center font-weight-bold">
                    Total Bayar : Rp {{ number_format($totalBayar) }}
                </div>
                <input type="hidden" name="total_biaya" value="{{ $totalBayar }}">

            </div>
        </div>
    </div>
</div>


{{-- ================= LOADING MIDTRANS ================= --}}
<div id="loadingMidtrans"
     style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.8);z-index:9999;justify-content:center;align-items:center;">

    <div class="text-center">
        <div class="spinner-border text-primary mb-3"></div>
        <p class="font-weight-bold">
            Memproses Pembayaran...
        </p>

    </div>
</div>

@push('addon-script')

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>

$(document).ready(function(){
    $('#btn-bayar-midtrans').click(function(e){
        e.preventDefault();
        $('#loadingMidtrans').fadeIn();
        let id = {{ $pengembalian->id }};
        $.ajax({
            url:`/pembayaran/${id}/snap-token`,
            type:"GET",
            success:function(res){
                $('#loadingMidtrans').fadeOut();
                snap.pay(res.snap_token,{
                    onSuccess:function(result){
                            $.ajax({
                            url:"/pembayaran/update-status",
                            type:"POST",
                            data:{
                             id:id,
                                _token:$('meta[name="csrf-token"]').attr('content')
                            },
                            success:function(){
                                Swal.fire({
                                icon:'success',
                                    title:'Pembayaran Berhasil'
                                }).then(()=>{
                                    window.location.href="{{ route('pembayaran.index') }}"
                                })
                         },
                            error:function(){
                                Swal.fire({
                                    icon:'error',
                                    title:'Gagal update status pembayaran'
                                })
                            }
                        })
                     },
                     
                    onPending:function(){
                        Swal.fire({
                            icon:'info',
                            title:'Menunggu Pembayaran'
                        })
                    },

                    onError:function(){
                        Swal.fire({
                            icon:'error',
                            title:'Pembayaran Gagal'
                        })
                    }
                })
            }
        })
    })
})
</script>

@endpush
@endsection