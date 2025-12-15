@extends('layout.app')
@section('title', 'Kostum')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Data Kostum</h1>
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('kostum.create') }}" class="btn btn-sm btn-primary shadow-sm mt-3 mt-md-0 mt-lg-0">
                    <i class="fas fa-plus-circle"></i> Tambah Kostum
                </a>
            @endif
        </div>

        {{-- Pesan Error --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Tabel Kostum --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Data Kostum</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kostums as $index => $kostum)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if ($kostum->image_kostum)
                                            <img src="{{ asset('storage/' . $kostum->image_kostum) }}" width="80"
                                                alt="Kostum">
                                        @else
                                            <span class="text-muted">Tidak ada gambar</span>
                                        @endif
                                    </td>
                                    <td>{{ $kostum->nama_kostum }}</td>
                                    <td>{{ $kostum->kategori }}</td>
                                    <td>Rp {{ number_format($kostum->harga, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($kostum->status)
                                            <span class="badge badge-danger">Sedang Digunakan</span>
                                        @else
                                            <span class="badge badge-success">Tersedia</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button"
                                                id="dropdownMenu{{ $kostum->id }}" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false"
                                                style="border-radius: 50%; width: 32px; height: 32px; padding: 0;">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>

                                            <!-- Dropdown Menu -->
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenu{{ $kostum->id }}">

                                                <!-- Detail -->
                                                <a class="dropdown-item" href="{{ route('kostum.show', $kostum->id) }}">
                                                    <i class="fas fa-eye mr-2"></i> Detail
                                                </a>

                                                @if (Auth::user()->role === 'admin')
                                                    <!-- Edit -->
                                                    <a class="dropdown-item"
                                                        href="{{ route('kostum.edit', $kostum->id) }}">
                                                        <i class="fas fa-edit mr-2"></i> Edit
                                                    </a>

                                                    <!-- Hapus -->
                                                    <button class="dropdown-item text-danger" data-toggle="modal"
                                                        data-target="#deleteModal{{ $kostum->id }}">
                                                        <i class="fas fa-trash mr-2"></i> Hapus
                                                    </button>
                                                @endif

                                            </div>
                                        </div>

                                        <!-- Modal Konfirmasi Hapus -->
                                        @if (Auth::user()->role === 'admin')
                                            <div class="modal fade" id="deleteModal{{ $kostum->id }}" tabindex="-1"
                                                role="dialog" aria-labelledby="deleteModalLabel{{ $kostum->id }}"
                                                aria-hidden="true">

                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">

                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title"
                                                                id="deleteModalLabel{{ $kostum->id }}">
                                                                Konfirmasi Hapus
                                                            </h5>
                                                            <button type="button" class="close text-white"
                                                                data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body">
                                                            Apakah kamu yakin ingin menghapus kostum
                                                            <strong>{{ $kostum->nama_kostum }}</strong>?
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Batal</button>

                                                            <form action="{{ route('kostum.destroy', $kostum->id) }}"
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
                            @empty
                                <tr>
                                    <td colspan="7">Belum ada data kostum</td>
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
