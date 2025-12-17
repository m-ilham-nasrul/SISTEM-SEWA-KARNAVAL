@extends('layout.app')
@section('title', 'Kostum')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Data Kostum</h1>
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('kostum.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle"></i> Tambah Kostum
                </a>
            @endif
        </div>

        <!-- Tabel -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Data Kostum</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center" id="dataTable" width="100%">
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
                serverSide: false,
                ajax: "{{ route('kostum.index') }}",
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'image_kostum',
                        render: data => data ?
                            `<img src="/storage/${data}" width="70">` :
                            `<span class="text-muted">Tidak ada</span>`
                    },
                    {
                        data: 'nama_kostum'
                    },
                    {
                        data: 'kategori'
                    },
                    {
                        data: 'harga',
                        render: data => `Rp ${Number(data).toLocaleString('id-ID')}`
                    },
                    {
                        data: 'status',
                        render: data => data == 1 ?
                            `<span class="badge badge-danger">Sedang Digunakan</span>` :
                            `<span class="badge badge-success">Tersedia</span>`
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
                            <a href="/kostum/${id}" class="dropdown-item">
                                <i class="fas fa-eye mr-2"></i> Detail
                            </a>
                            <a href="/kostum/${id}/edit" class="dropdown-item">
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
                    text: 'Data kostum akan dihapus permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/kostum/${id}`,
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

        });
    </script>
@endpush
