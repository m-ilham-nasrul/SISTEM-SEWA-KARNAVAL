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

        <!-- Tabel Data -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Data User</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center" id="dataTable" width="100%">
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
                            {{-- DIISI OLEH AJAX --}}
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

    <script>
        $(document).ready(function() {

            let table = $('#dataTable').DataTable({
                processing: true,
                serverSide: false, // jika semua data dikirim sekaligus, bisa false
                ajax: "{{ route('user.index') }}",
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'role',
                        render: data =>
                            `<span class="badge badge-${data=='admin'?'primary':'success'}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: id => `
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="/user/${id}/edit" class="dropdown-item">
                                <i class="fas fa-edit mr-2"></i> Edit
                            </a>
                            <button class="dropdown-item text-danger btn-delete" data-id="${id}">
                                <i class="fas fa-trash mr-2"></i> Hapus
                            </button>
                        </div>
                    </div>
                `
                    }
                ]
            });

            // DELETE AJAX + SWEETALERT
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin?',
                    text: 'User akan dihapus permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/user/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: res.message ||
                                        'User berhasil dihapus',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                table.ajax.reload(null, false);
                            },
                            error: function(err) {
                                Swal.fire('Gagal', 'Data gagal dihapus', 'error');
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
