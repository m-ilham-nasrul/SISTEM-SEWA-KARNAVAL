@extends('layout.app')
@section('title', 'Pengembalian')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Pengembalian Kostum</h1>
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

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Data Pengembalian</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Penyewa</th>
                                <th>Nama Kostum</th>
                                <th>Tanggal Sewa</th>
                                <th>Tanggal Kembali</th>
                                <th>Total Bayar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($sewas as $sewa)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    {{-- Nama Penyewa --}}
                                    <td>
                                        {!! $sewa->penyewa->nama_penyewa ?? '<small>Data penyewa telah dihapus!</small>' !!}
                                    </td>

                                    {{-- Nama Kostum (bisa banyak) --}}
                                    <td>
                                        @if ($sewa->kostum_list->isNotEmpty())
                                            @foreach ($sewa->kostum_list as $kostum)
                                                <div>{{ $kostum->nama_kostum ?? 'Kostum telah dihapus' }}</div>
                                            @endforeach
                                        @else
                                            <small>Data kostum telah dihapus!</small>
                                        @endif
                                    </td>

                                    {{-- Tanggal Sewa --}}
                                    <td>{{ date('d-F-Y', strtotime($sewa->tanggal_sewa)) }}</td>

                                    {{-- Tanggal Kembali --}}
                                    <td>{{ date('d-F-Y', strtotime($sewa->tanggal_kembali)) }}</td>

                                    {{-- Total Bayar --}}
                                    <td>Rp. {{ number_format($sewa->total_biaya) }}</td>

                                    {{-- Status --}}
                                    <td>
                                        @if (!$sewa->status)
                                            <span class="badge badge-secondary badge-pill">Masa Sewa
                                                @if (date('Y-m-d') > $sewa->tanggal_kembali)
                                                    - <span class="text-danger">Terlambat</span>
                                                @elseif (date('Y-m-d') === $sewa->tanggal_kembali)
                                                    - <span class="text-warning">Hari terakhir</span>
                                                @endif
                                            </span>
                                        @else
                                            <span class="badge badge-success badge-pill">Kembali</span>
                                        @endif

                                        <br>

                                        {{-- Status Pembayaran --}}
                                        @if ($sewa->status_bayar)
                                            <span class="badge badge-pill badge-success mt-1">Telah Terbayar</span>
                                        @else
                                            <span class="badge badge-pill badge-danger mt-1">Belum Membayar</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button"
                                                id="dropdownMenu{{ $sewa->id }}" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false"
                                                style="border-radius: 50%; width: 32px; height: 32px; padding: 0;">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>

                                            <!-- Dropdown Menu -->
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenu{{ $sewa->id }}">

                                                <!-- Detail -->
                                                <a class="dropdown-item" href="{{ route('penyewaan.show', $sewa->id) }}">
                                                    <i class="fas fa-eye mr-2"></i> Detail
                                                </a>


                                                <!-- Nota (jika sudah bayar) -->
                                                @if ($sewa->status_bayar)
                                                    <a class="dropdown-item"
                                                        href="{{ route('pembayaran.nota', $sewa->id) }}" target="_blank">
                                                        <i class="fas fa-file-invoice mr-2"></i> Nota
                                                    </a>
                                                @endif

                                                <!-- Hapus (Admin) -->
                                                @if (Auth::user()->role === 'admin')
                                                    <button class="dropdown-item text-danger" data-toggle="modal"
                                                        data-target="#deleteModal{{ $sewa->id }}">
                                                        <i class="fas fa-trash mr-2"></i> Hapus
                                                    </button>
                                                @endif
                                            </div>
                                            <!-- Tombol Kembalikan (jika belum kembali) -->
                                            @if (!$sewa->status)
                                                <form action="{{ route('pengembalian.destroy', $sewa->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-undo mr-2"></i> Kembalikan
                                                    </button>
                                                </form>
                                            @endif

                                        </div>

                                        <!-- Modal Konfirmasi Hapus -->
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
                                                            Apakah Anda yakin ingin menghapus Data Pengembalian
                                                            <strong>{{ $sewa->penyewa->nama_penyewa ?? 'Penyewa Tidak Ditemukan' }}</strong>?
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Batal</button>

                                                            <form action="{{ route('pengembalian.hapus', $sewa->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Hapus</button>
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
    <script src="{{ asset('sbadmin2/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/js/demo/datatables-demo.js') }}"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 1500,
                showConfirmButton: false
            });
        </script>
    @elseif (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('error') }}",
            });
        </script>
    @endif
@endpush
