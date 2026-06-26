@extends('layout.app')
@section('title', 'Detail Penyewaan')

@section('content')
    <div class="container-fluid">

        <h1 class="h3 mb-4 text-gray-800">Detail Penyewaan</h1>

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Informasi Penyewaan</h6>
            </div>

            <div class="card-body">
                <div class="row">

                    <!-- FOTO KOSTUM GRID -->
                    <div class="col-md-4 mb-4">
                        <div class="row">
                            @php
                                $totalFoto = $kostums->count();
                            @endphp

                            @foreach ($kostums->take(4) as $k)
                                <div class="col-6 mb-3">
                                    <div class="border rounded p-2 bg-white shadow-sm text-center">
                                        <img src="{{ $k->image_kostum ? asset('uploads/kostum/' . $k->image_kostum) : asset('images/no-image.png') }}"
                                            class="img-fluid rounded">
                                        <h6 class="mt-2 font-weight-bold text-dark">{{ $k->nama_kostum }}</h6>
                                    </div>
                                </div>
                            @endforeach

                            @if ($totalFoto > 4)
                                <div class="col-12 text-center mt-3">
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                        data-target="#modalSemuaFoto">
                                        Lihat Semua Foto ({{ $totalFoto }})
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- MODAL SEMUA FOTO -->
                    <div class="modal fade" id="modalSemuaFoto" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Detail Gambar Kostum</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                </div>

                                <div class="modal-body" style="max-height: 520px; overflow-y: auto;">
                                    <div class="row g-3">
                                        @foreach ($kostums as $k)
                                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                                <div class="card shadow-sm text-center p-3">
                                                    <img src="{{ $k->image_kostum ? asset('uploads/kostum/' . $k->image_kostum) : asset('images/no-image.png') }}"
                                                        class="card-img-top"
                                                        style="height: 260px; width: 100%; object-fit: contain; background:#f8f9fa; padding:6px;">
                                                    <div class="card-body p-0 fw-bold">{{ $k->nama_kostum }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- DETAIL SEWA -->
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Kode Sewa</th>
                                <td>{{ $sewa->kode_sewa }}</td>
                            </tr>
                            <tr>
                                <th>Penyewa</th>
                                <td>
                                    {{ optional($sewa->penyewa->user)->name ?? 'Penyewa dihapus' }}
                                </td>
                            </tr>
                            <tr>
                                <th>No Telepon</th>
                                <td>
                                    {{ $sewa->penyewa->no_telp ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Daftar Kostum</th>
                                <td>
                                    @foreach ($kostums as $k)
                                        <span>{{ $k->nama_kostum }}</span><br>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th>Tanggal Sewa</th>
                                <td>{{ $sewa->tanggal_sewa }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Kembali</th>
                                <td>{{ $sewa->tanggal_kembali }}</td>
                            </tr>
                            <tr>
                                <th>Status Penyewaan</th>
                                <td>
                                    @if ($sewa->status == 0)
                                        <span class="badge badge-secondary px-3 py-2">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Masa Sewa
                                        </span>
                                        <br>
                                        <span class="badge badge-success px-3 py-2 mt-1">
                                            <i class="fas fa-money-check-alt mr-1"></i>
                                            Sudah DP
                                        </span>
                                    @elseif ($sewa->status == 1)
                                        <span class="badge badge-warning px-3 py-2">
                                            <i class="fas fa-user-check"></i> Menunggu Verifikasi Admin
                                        </span>
                                    @elseif ($sewa->status == 2)
                                        <span class="badge badge-info px-3 py-2">
                                            <i class="fas fa-user-check"></i> Pengembalian Diverifikasi
                                        </span>
                                    @elseif ($sewa->status == 3)
                                        <span class="badge badge-success px-3 py-2">
                                            <i class="fas fa-check-circle"></i> Selesai
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Status Pembayaran</th>
                                <td>
                                    @if ($sewa->status_bayar->value == 'pending')
                                        <span class="badge badge-danger px-3 py-2">
                                            <i class="fas fa-clock mr-1"></i> Menunggu DP
                                        </span>
                                    @elseif ($sewa->status_bayar->value == 'dp_paid')
                                        <span class="badge badge-primary px-3 py-2">
                                            <i class="fas fa-money-bill-wave"></i> Menunggu Pelunasan
                                        </span>
                                    @elseif ($sewa->status_bayar->value == 'paid')
                                        <span class="badge badge-success px-3 py-2">
                                            <i class="fas fa-check-circle"></i> Terbayar Lunas
                                        </span>
                                    @else
                                        <span class="badge badge-secondary px-3 py-2">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Harga Paket</th>
                                <td>Rp {{ number_format($hargaPaket, 0, ',', '.') }}</td>
                            </tr>

                            <tr>
                                <th>Denda Kerusakan</th>
                                <td>Rp {{ number_format($denda, 0, ',', '.') }}</td>
                            </tr>

                            <tr class="bg-light font-weight-bold">
                                <th>Total Biaya</th>
                                <td class="text-primary font-weight-bold">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </td>
                            </tr>

                            <tr>
                                <th>Catatan</th>
                                <td>{{ $sewa->catatan ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>Dibuat Pada</th>
                                <td>{{ $sewa->created_at }}</td>
                            </tr>

                            <tr>
                                <th>Terakhir Update</th>
                                <td>{{ $sewa->updated_at }}</td>
                            </tr>

                        </table>

                        <!-- ACTION BUTTONS -->
                        <div class="mt-4 mb-3">

                            {{-- Pelunasan --}}
                            @if ($sewa->status == 2 && $sewa->status_bayar->value === 'dp_paid')
                                <a href="/pembayaran/{{ $sewa->id }}/bayar" class="btn btn-primary btn-sm mr-2">
                                    <i class="fas fa-credit-card"></i>
                                    Lanjutkan Pelunasan
                                </a>
                            @endif

                            {{-- Nota --}}
                            @if ($sewa->status_bayar->value === 'paid')
                                <a href="/pembayaran/{{ $sewa->id }}/nota" class="btn btn-info btn-sm mr-2">
                                    <i class="fas fa-file-invoice"></i>
                                    Lihat Nota
                                </a>
                            @endif

                            {{-- Edit Admin --}}
                            @if (Auth::user()->role === 'admin')
                                <a href="{{ route('penyewaan.edit', $sewa->id) }}" class="btn btn-secondary btn-sm mr-2">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                            @endif

                        </div>

                        <a href="{{ route('penyewaan.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
