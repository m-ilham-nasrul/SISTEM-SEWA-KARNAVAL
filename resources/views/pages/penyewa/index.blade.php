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
            @endif
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Data Penyewa</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Penyewa</th>
                                <th>Telepon</th>
                                <th>Alamat</th>
                                @if (Auth::user()->role === 'admin')
                                    <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            {{-- AJAX DataTables --}}
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
                serverSide: false,
                ajax: "{{ route('penyewa.index') }}",
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'user',
                        render: data => data ? data.name : '-'
                    },
                    {
                        data: 'no_telp'
                    },
                    {
                        data: 'alamat'
                    },
                    @if (Auth::user()->role === 'admin')
                        {
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(id, type, row) {
                                return `
                <div class="dropdown">
                    <button class="btn btn-light btn-sm" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="/penyewa/${id}/edit">
                            <i class="fas fa-edit mr-2"></i> Edit
                        </a>
                        <button class="dropdown-item text-danger btn-delete" data-id="${id}">
                            <i class="fas fa-trash mr-2"></i> Hapus
                        </button>
                    </div>
                </div>`;
                            }
                        }
                    @endif
                ]
            });

            // DELETE AJAX + SweetAlert
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin?',
                    text: 'Data penyewa akan dihapus permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/penyewa/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                table.ajax.reload(null, false);
                            },
                            error: function() {
                                Swal.fire('Gagal', 'Data gagal dihapus', 'error');
                            }
                        });
                    }
                });
            });

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('success') }}",
                    timer: 1500,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@endpush
