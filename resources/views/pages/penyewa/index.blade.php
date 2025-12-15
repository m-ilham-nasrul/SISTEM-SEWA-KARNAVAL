@extends('layout.app')
@section('title', 'Penyewa')

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Data Penyewa</h1>

            @if (Auth::user()->role === 'admin')
                <a href="{{ route('penyewa.create') }}" class="btn btn-sm btn-primary shadow-sm mt-3 mt-md-0">
                    <i class="fas fa-plus-circle"></i> Tambah Penyewa
                </a>
            @else
                <a href="{{ route('penyewa.create') }}" class="btn btn-sm btn-primary shadow-sm mt-3 mt-md-0">
                    <i class="fas fa-user-plus"></i> Daftar Sebagai Penyewa
                </a>
            @endif
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Data Penyewa</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">
                    <table class="table table-bordered text-center" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>User</th>
                                <th>Nama</th>
                                <th>Telepon</th>
                                <th>Alamat</th>
                                @if (Auth::user()->role === 'admin')
                                    <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penyewas as $penyewa)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $penyewa->user?->name ?? '-' }}</td>
                                    <td>{{ $penyewa->nama_penyewa }}</td>
                                    <td>{{ $penyewa->no_telp }}</td>
                                    <td>{{ $penyewa->alamat }}</td>

                                    @if (Auth::user()->role === 'admin')
                                        <td class="text-center">

                                            <!-- Tombol Titik 3 -->
                                            <div class="dropdown">
                                                <button class="btn btn-light btn-sm" type="button"
                                                    id="dropdownMenu{{ $penyewa->id }}" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false"
                                                    style="border-radius: 50%; width: 32px; height: 32px; padding: 0;">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>

                                                <!-- Dropdown Menu -->
                                                <div class="dropdown-menu"
                                                    aria-labelledby="dropdownMenu{{ $penyewa->id }}">

                                                    <!-- Edit -->
                                                    <a class="dropdown-item"
                                                        href="{{ route('penyewa.edit', $penyewa->id) }}">
                                                        <i class="fas fa-edit mr-2"></i> Edit
                                                    </a>

                                                    <!-- Hapus (buka modal) -->
                                                    <button class="dropdown-item text-danger" data-toggle="modal"
                                                        data-target="#modalHapus{{ $penyewa->id }}">
                                                        <i class="fas fa-trash mr-2"></i> Hapus
                                                    </button>
                                                </div>
                                            </div>

                                        </td>
                                    @endif
                                </tr>

                                <!-- Modal Konfirmasi Hapus -->
                                <div class="modal fade" id="modalHapus{{ $penyewa->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">

                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus Data Penyewa
                                                <strong>{{ $penyewa->nama_penyewa }}</strong>?
                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>

                                                <form action="{{ route('penyewa.destroy', $penyewa->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data penyewa</td>
                                </tr>
                            @endforelse
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
        Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: "{{ session('success') }}",
        timer: 1500,
        showConfirmButton: false
        });
    @endif
@endpush
