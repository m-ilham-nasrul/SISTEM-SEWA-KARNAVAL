@extends('layout.app')
@section('title', 'Pembayaran')
@section('content')

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Pembayaran Sewa</h1>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">

            <!-- Pendapatan Hari Ini -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Pendapatan Hari Ini
                                </div>
                                <div class="h5 mb-0 font-weight-bold">Rp. {{ $pendapatan_hari }}</div>
                            </div>
                            <div class="d-flex align-items-center justify-content-center bg-success"
                                style="width: 50px; height: 50px; border-radius: 10px; margin-left: 10px;">
                                <i class="fas fa-dollar-sign text-white" style="font-size: 22px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pendapatan Bulan Ini -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Pendapatan Bulan Ini</div>
                                <div class="h5 mb-0 font-weight-bold">Rp. {{ $pendapatan_bulan }}</div>
                            </div>
                            <div class="d-flex align-items-center justify-content-center bg-primary"
                                style="width: 50px; height: 50px; border-radius: 10px; margin-left: 10px;">
                                <i class="fas fa-chart-line text-white" style="font-size: 22px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    Data Pembayaran <span class="badge badge-pill badge-secondary">{{ $statusTitle }}</span>
                </h6>
            </div>

            <div class="card-body">
                <!-- Filter -->
                <div class="mx-auto mb-3">
                    <div class="btn-group" role="group">
                        <a href="{{ route('pembayaran.index') }}" class="btn btn-dark">All</a>
                        <a href="{{ route('pembayaran.index', 'status_bayar=0') }}" class="btn btn-secondary">Menunggu
                            Pembayaran</a>
                        <a href="{{ route('pembayaran.index', 'status_bayar=1') }}" class="btn btn-secondary">Telah
                            Terbayar</a>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">
                    <table class="table table-bordered" id="dataTable" width="100%">
                        <thead class="text-center">
                            <tr>
                                <th>No</th>
                                <th>Kode Sewa</th>
                                <th>Nama Penyewa</th>
                                <th>Nama Kostum</th>
                                <th>Tanggal Sewa</th>
                                <th>Tanggal Kembali</th>
                                <th>Denda</th>
                                <th>Total Bayar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($sewas as $sewa)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $sewa->kode_sewa ?? 'SEWA-' . str_pad($sewa->id, 4, '0', STR_PAD_LEFT) }}</td>

                                    <!-- Nama Penyewa -->
                                    <td>
                                        {!! $sewa->penyewa->nama_penyewa ?? '<small>Data penyewa telah dihapus!</small>' !!}
                                    </td>

                                    <!-- Nama Kostum -->
                                    <td>
                                        @if ($sewa->kostum_list->isNotEmpty())
                                            @foreach ($sewa->kostum_list as $kostum)
                                                <div>{{ $kostum->nama_kostum ?? 'Kostum telah dihapus' }}</div>
                                            @endforeach
                                        @else
                                            <small>Data kostum telah dihapus!</small>
                                        @endif
                                    </td>

                                    <!-- Tanggal -->
                                    <td>{{ $sewa->tanggal_sewa->format('d-F-Y') }}</td>
                                    <td>{{ $sewa->tanggal_kembali->format('d-F-Y') }}</td>

                                    <!-- Denda -->
                                    <td>Rp. {{ number_format($sewa->denda) }}</td>

                                    <!-- Total Bayar -->
                                    <td>Rp. {{ number_format($sewa->total_biaya) }}</td>

                                    <!-- Status -->
                                    <td>
                                        @if (!$sewa->status)
                                            <span class="badge badge-secondary">Masa Sewa</span>
                                        @else
                                            <span class="badge badge-success">Kembali</span>
                                        @endif
                                        <br>
                                        @if ($sewa->status_bayar)
                                            <span class="badge badge-success mt-1">Telah Terbayar</span>
                                        @else
                                            <span class="badge badge-danger mt-1">Belum Membayar</span>
                                        @endif
                                    </td>

                                    <!-- Aksi -->
                                    <td>
                                        <!-- Tombol Tiga Titik -->
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button"
                                                id="dropdownMenu{{ $sewa->id }}" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false"
                                                style="border-radius: 50%; width: 32px; height: 32px; padding: 0;">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>

                                            <!-- Popup Menu -->
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenu{{ $sewa->id }}">

                                                <!-- Detail -->
                                                <a class="dropdown-item" href="{{ route('penyewaan.show', $sewa->id) }}">
                                                    <i class="fas fa-eye mr-2"></i> Detail
                                                </a>

                                                

                                                <!-- Edit (hanya admin) -->
                                                @if (Auth::user()->role === 'admin')
                                                    <a class="dropdown-item"
                                                        href="{{ route('penyewaan.edit', $sewa->id) }}">
                                                        <i class="fas fa-edit mr-2"></i> Edit
                                                    </a>
                                                @endif

                                                <!-- Hapus (trigger modal, hanya admin) -->
                                                @if (Auth::user()->role === 'admin')
                                                    <button class="dropdown-item text-danger" data-toggle="modal"
                                                        data-target="#deleteModal{{ $sewa->id }}">
                                                        <i class="fas fa-trash mr-2"></i> Hapus
                                                    </button>
                                                @endif
                                            </div>

                                            <!-- Bayar (hanya jika belum bayar) -->
                                                @if (!$sewa->status_bayar)
                                                    <a class="dropdown-item"
                                                        href="{{ route('pengembalian.edit', $sewa->id) }}">
                                                        <i class="fas fa-money-bill-wave mr-2"></i> Bayar
                                                    </a>
                                                @endif
                                        </div>

                                        <!-- Modal Hapus -->
                                        @if (Auth::user()->role === 'admin')
                                            <div class="modal fade" id="deleteModal{{ $sewa->id }}" tabindex="-1"
                                                role="dialog" aria-labelledby="deleteModalLabel{{ $sewa->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">

                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title"
                                                                id="deleteModalLabel{{ $sewa->id }}">
                                                                Konfirmasi Hapus
                                                            </h5>
                                                            <button type="button" class="close text-white"
                                                                data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body">
                                                            Apakah Anda yakin ingin menghapus Data Pembayaran
                                                            <strong>{{ $sewa->kode_sewa ?? 'SEWA-' . str_pad($sewa->id, 4, '0', STR_PAD_LEFT) }}</strong>?
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Batal</button>

                                                            <form action="{{ route('pengembalian.hapus', $sewa->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">
                                                                    Hapus
                                                                </button>
                                                            </form>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

@endsection

@push('addon-style')
    <link href="{{ asset('sbadmin2/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@push('addon-script')
    <!-- Datatables -->
    <script src="{{ asset('sbadmin2/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Datatables Demo -->
    <script src="{{ asset('sbadmin2/js/demo/datatables-demo.js') }}"></script>

    <!-- SweetAlert -->
    @if (session('success'))
        Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: "{{ session('success') }}",
        timer: 1500,
        showConfirmButton: false
        });
    @elseif (session('error'))
        Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: "{{ session('error') }}",
        });
    @endif
@endpush
