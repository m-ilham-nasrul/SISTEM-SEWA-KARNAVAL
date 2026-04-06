@extends('layout.app')
@section('title', 'Penyewaan')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Data Penyewaan</h1>
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('penyewaan.select') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle"></i> Tambah Penyewaan
                </a>
            @endif
        </div>

        <!-- Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Data Penyewaan</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Penyewa</th>
                                <th>Nama Kostum</th>
                                <th>Tanggal Sewa</th>
                                <th>Tanggal Kembali</th>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    <script>
        $(document).ready(function() {

            let table = $('#dataTable').DataTable({
                processing: true,
                ajax: "{{ route('penyewaan.index') }}", // Route harus mengembalikan JSON
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'penyewa.user.name',
                        defaultContent: '<span class="text-muted">Data user telah dihapus</span>'
                    },
                    {
                        data: 'kostum_list',
                        orderable: false,
                        searchable: false,
                        render: kostums => {
                            if (kostums.length) {
                                return kostums.map(k => k.nama_kostum || 'Kostum telah dihapus')
                                    .join('<br>');
                            }
                            return '<small>Data kostum telah dihapus!</small>';
                        }
                    },
                    {
                        data: 'tanggal_sewa',
                        render: t => moment(t).format('DD-MMMM-YYYY')
                    },
                    {
                        data: 'tanggal_kembali',
                        render: t => moment(t).format('DD-MMMM-YYYY')
                    },
                    {
                        data: null,
                        render: d => {
                            let status = '';
                            let extra = '';
                            if (d.status == 0) {
                                status = `
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-hourglass-half mr-1"></i>
                                        Masa Sewa
                                    </span>
                                `;
                                const today = moment();
                                const kembali = moment(d.tanggal_kembali);
                                if (today.isAfter(kembali)) {
                                    extra = `
                                        <br>
                                        <span class="badge badge-danger">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Terlambat
                                        </span>
                                    `;
                                } else if (today.isSame(kembali, 'day')) {
                                    extra = `
                                            <br>
                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock"></i>
                                                Hari Terakhir
                                            </span>
                                        `;
                                }
                            } else if (d.status == 1) {
                                status = `
                                    <span class="badge badge-warning px-3 py-2">
                                        <i class="fas fa-hourglass mr-1"></i>
                                        Menunggu Verifikasi
                                    </span>
                                `;
                            } else if (d.status == 2) {
                                if (!d.status_bayar) {
                                    status = `
                                    <span class="badge badge-info px-3 py-2">
                                        <i class="fas fa-credit-card mr-1"></i>
                                        Menunggu Pembayaran
                                    </span>
                                `;
                                } else {
                                    status = `
                                        <span class="badge badge-success px-3 py-2">
                                            <i class="fas fa-check-circle mr-1"></i>
                                        Selesai
                                    </span>
                                `;
                                }
                            }
                            return status + extra;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: data => {
                            let id = data.id;
                            let role = '{{ Auth::user()->role }}';

                            let editBtn = '';
                            let deleteBtn = '';

                            // ===== EDIT =====
                            if (
                                role === 'admin' ||
                                (role === 'penyewa' && data.status == 0)
                            ) {
                                editBtn = `
                                <a href="/penyewaan/${id}/edit" class="dropdown-item">
                                    <i class="fas fa-edit mr-2"></i> Edit
                                </a>
                            `;
                            }

                            // ===== BATALKAN =====
                            if (
                                role === 'admin' ||
                                (role === 'penyewa' && data.status == 0)
                            ) {
                                deleteBtn = `
                                <button class="dropdown-item text-danger btn-delete"
                                        data-id="${id}">
                                    <i class="fas fa-trash mr-2"></i> Batalkan
                                </button>
                            `;
                            }

                            return `
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm" data-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a href="/penyewaan/${id}" class="dropdown-item">
                                        <i class="fas fa-eye mr-2"></i> Detail
                                    </a>

                                    ${editBtn}
                                 ${deleteBtn}
                                </div>
                            </div>
                        `;
                        }
                    }
                ]
            });

            // DELETE AJAX + SWEETALERT
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin?',
                    text: 'Data penyewaan akan dibatalkan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/penyewaan/${id}`,
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
                            error: function(xhr) {
                                console.log(xhr.responseText);
                                Swal.fire('Gagal', 'Data gagal dihapus', 'error');
                            }
                        });
                    }
                });
            });
        /* ===============================
           AUTO RELOAD TIAP 30 DETIK
        =============================== */
            setInterval(function() {
                table.ajax.reload(null, false); /
            }, 30000);
        });
    </script>
@endpush
