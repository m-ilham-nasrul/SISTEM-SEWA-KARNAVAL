@extends('layout.app')

@section('title', 'Nota Pembayaran')

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4 no-print">
            <h1 class="h3 mb-0 text-gray-800">Nota Pembayaran</h1>

            <button onclick="window.print()" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-print"></i> Cetak Nota
            </button>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-5">

                <div class="card shadow" id="nota">

                    <!-- HEADER -->
                    <div class="card-header bg-primary text-white text-center">
                        <h5 class="mb-0 font-weight-bold">NOTA SEWA</h5>
                        <small>Sewa Karnaval</small>
                    </div>

                    <!-- BODY -->
                    <div class="card-body">

                        <table class="table table-borderless table-sm mb-2">
                            <tr>
                                <td width="40%">Kode</td>
                                <td>{{ $sewa->kode_sewa ?? 'SEWA-' . str_pad($sewa->id, 4, '0', STR_PAD_LEFT) }}</td>
                            </tr>

                            <tr>
                                <td>Pelanggan</td>
                                <td>{{ optional(optional($sewa->penyewa)->user)->name ?? '-' }}</td>
                            </tr>

                            <tr>
                                <td>Tanggal Sewa</td>
                                <td>{{ date('d M Y', strtotime($sewa->tanggal_sewa)) }}</td>
                            </tr>

                            <tr>
                                <td>Tanggal Kembali</td>
                                <td>{{ date('d M Y', strtotime($sewa->tanggal_kembali)) }}</td>
                            </tr>

                            <tr>
                                <td>Kondisi</td>
                                <td>
                                    @if ($sewa->kondisi == 'baik')
                                        <span class="badge badge-success">Baik</span>
                                    @elseif($sewa->kondisi == 'rusak')
                                        <span class="badge badge-danger">Rusak</span>
                                    @else
                                        <span class="badge badge-secondary">Belum Dicek</span>
                                    @endif
                                </td>
                            </tr>

                            @if ($sewa->catatan)
                                <tr>
                                    <td>Catatan</td>
                                    <td class="text-danger">{{ $sewa->catatan }}</td>
                                </tr>
                            @endif

                            <tr>
                                <td>Metode Pembayaran</td>
                                <td>{{ $sewa->payment_type ? ucfirst($sewa->payment_type) : '-' }}</td>
                            </tr>

                            <tr>
                                <td>Order ID</td>
                                <td>{{ $sewa->midtrans_order_id ?? '-' }}</td>
                            </tr>

                            <tr>
                                <td>Status</td>
                                <td>
                                    @if ($sewa->status_bayar)
                                        <span class="badge badge-success">Terbayar</span>
                                    @else
                                        <span class="badge badge-danger">Belum Bayar</span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <!-- TOTAL -->
                        <table class="table table-sm">
                            <tr>
                                <td>Biaya Sewa</td>
                                <td class="text-right">
                                    Rp {{ number_format($sewa->total_biaya) }}
                                </td>
                            </tr>

                            @if ($sewa->denda > 0)
                                <tr>
                                    <td class="text-danger">Denda</td>
                                    <td class="text-right text-danger">
                                        Rp {{ number_format($sewa->denda) }}
                                    </td>
                                </tr>
                            @endif

                            <tr class="font-weight-bold">
                                <td>Total Bayar</td>
                                <td class="text-right">
                                    Rp {{ number_format($sewa->total_biaya + $sewa->denda) }}
                                </td>
                            </tr>
                        </table>

                        <div class="text-center mt-4">
                            <small>
                                {{ date('d F Y') }} <br>
                                @php
                                    $user = Auth::user();
                                    $roleLabel = $user->role === 'admin' ? 'Admin' : 'Penyewa';
                                @endphp
                                {{ $user->name }} ({{ $roleLabel }})
                            </small>
                        </div>

                    </div>
                </div>

                <div class="text-center mt-3 no-print">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('addon-style')
    <style>
        @media print {
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                width: 58mm;
                /* ganti 80mm untuk printer lebih lebar */
                font-size: 11px;
                font-family: monospace;
            }

            body * {
                visibility: hidden;
            }

            #nota,
            #nota * {
                visibility: visible;
            }

            #nota {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none !important;
                border: none !important;
            }

            /* Reset header biru jadi polos */
            .card-header {
                background: white !important;
                color: black !important;
                border-bottom: 1px dashed black !important;
            }

            /* Garis pemisah dashed */
            .nota-divider {
                border: none;
                border-top: 1px dashed black;
                margin: 6px 0;
            }

            table td {
                padding: 2px 0;
                font-size: 11px;
            }

            .no-print {
                display: none !important;
            }

            .badge {
                border: 1px solid black;
                background: none !important;
                color: black !important;
            }
        }
    </style>
@endpush
