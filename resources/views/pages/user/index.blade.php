@extends('layout.app')
@section('title', 'User')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Data Akun User</h1>
            <a href="{{ route('user.create') }}" class="btn btn-sm btn-primary shadow-sm mt-3 mt-md-0">
                <i class="fas fa-plus-circle"></i> Tambah User
            </a>
        </div>

        {{-- Alert Error --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Data Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Data User</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">
                    <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $user->role == 'admin' ? 'primary' : 'success' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button"
                                                id="dropdownMenu{{ $user->id }}" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false"
                                                style="border-radius: 50%; width: 32px; height: 32px; padding: 0;">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>

                                            <!-- Dropdown Menu -->
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenu{{ $user->id }}">

                                                <!-- Edit -->
                                                <a class="dropdown-item" href="{{ route('user.edit', $user->id) }}">
                                                    <i class="fas fa-edit mr-2"></i> Edit
                                                </a>

                                                <!-- Hapus (trigger modal) -->
                                                <button class="dropdown-item text-danger" data-toggle="modal"
                                                    data-target="#deleteModal{{ $user->id }}">
                                                    <i class="fas fa-trash mr-2"></i> Hapus
                                                </button>

                                            </div>
                                        </div>

                                        <!-- Modal Hapus -->
                                        <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="deleteModalLabel{{ $user->id }}"
                                            aria-hidden="true">

                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">

                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $user->id }}">
                                                            Konfirmasi Hapus
                                                        </h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>

                                                    <div class="modal-body">
                                                        Apakah kamu yakin ingin menghapus user
                                                        <strong>{{ $user->name }}</strong>?
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Batal</button>

                                                        <form action="{{ route('user.destroy', $user->id) }}"
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
                                    </td>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data user</td>
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
