@extends('layout.app')
@section('title', 'Pengembalian')

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Pengembalian Kostum</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Data Pengembalian</h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Sewa</th>
                                <th>Nama Penyewa</th>
                                <th>Nama Kostum</th>
                                <th>Tanggal Sewa</th>
                                <th>Tanggal Kembali</th>
                                <th>Total Bayar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
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
        $(function() {

            let table = $('#dataTable').DataTable({
                ajax: "{{ route('pengembalian.index') }}",
                columns: [{
                        data: null,
                        render: (d, t, r, m) => m.row + 1
                    },
                    {
                        data: 'kode_sewa'
                    },
                    {
                        data: 'penyewa.user.name',
                        defaultContent: '<small>Penyewa dihapus</small>'
                    },
                    {
                        data: 'kostum_list',
                        orderable: false,
                        searchable: false,
                        render: k => k.length ?
                            k.map(x => x.nama_kostum).join('<br>') : '<small>Kostum dihapus</small>'
                    },
                    {
                        data: 'tanggal_sewa',
                        render: d => moment(d).format('DD-MMMM-YYYY')
                    },
                    {
                        data: 'tanggal_kembali',
                        render: d => moment(d).format('DD-MMMM-YYYY')
                    },
                    {
                        data: 'total_biaya',
                        render: d => `Rp. ${Number(d).toLocaleString()}`
                    },
                    {
                        data: null,
                        render: d => `
                    <span class="badge badge-${d.status ? 'success' : 'secondary'}">
                        ${d.status ? 'Kembali' : 'Masa Sewa'}
                    </span><br>
                    <span class="badge badge-${d.status_bayar ? 'success' : 'danger'} mt-1">
                        ${d.status_bayar ? 'Terbayar' : 'Belum Bayar'}
                    </span>
                `
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: data => {
                            let id = data.id;
                            let role = '{{ Auth::user()->role }}';

                            // ===== NOTA =====
                            let notaBtn = '';
                            if (data.status_bayar) {
                                notaBtn = `
                <a href="/pembayaran/${id}/nota"
                   class="btn btn-info btn-sm mb-1 w-100">
                    <i class="fas fa-file-invoice mr-1"></i> Nota
                </a>
            `;
                            }

                            // ===== KEMBALIKAN =====
                            let returnBtn = '';
                            if (!data.status) {
                                returnBtn = `
        <button class="btn btn-warning btn-sm mb-1 w-100 btn-return"
                data-id="${id}"
                data-paid="${data.status_bayar}">
            <i class="fas fa-undo mr-1"></i> Kembalikan
        </button>
        `;
                            }


                            // ===== EDIT & BATALKAN (ROLE-BASED) =====
                            let editBtn = '';
                            let deleteBtn = '';

                            if (role === 'admin' || (role === 'penyewa' && data.status == 0)) {
                                editBtn = `
                <a href="/penyewaan/${id}/edit" class="dropdown-item">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
            `;
                                deleteBtn = `
                <button class="dropdown-item text-danger btn-delete" data-id="${id}">
                    <i class="fas fa-trash mr-2"></i> Hapus
                </button>
            `;
                            }

                            return `
            <div class="d-flex flex-column align-items-center">
                ${notaBtn}
                ${returnBtn}

                <div class="dropdown mt-1">
                    <button class="btn btn-light btn-sm w-100" data-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="/penyewaan/${id}" class="dropdown-item">
                            <i class="fas fa-eye mr-2"></i> Detail
                        </a>
                        ${editBtn}
                        ${deleteBtn}
                    </div>
                </div>
            </div>
        `;
                        }
                    }

                ]
            });

            // KEMBALIKAN KOSTUM
            $(document).on('click', '.btn-return', function() {
                let id = $(this).data('id');
                let paid = $(this).data('paid');

                //JIKA BELUM BAYAR
                if (!paid) {
                    Swal.fire({
                        title: 'Pembayaran Diperlukan',
                        text: 'Penyewaan belum dibayar. Silakan lakukan pembayaran terlebih dahulu.',
                        icon: 'warning',
                        confirmButtonText: 'Bayar Sekarang',
                        confirmButtonColor: '#28a745'
                    }).then(res => {
                        if (res.isConfirmed) {
                            window.location.href = `/pengembalian/${id}/edit`;
                        }
                    });
                    return;
                }

                //JIKA SUDAH BAYAR → LANJUTKAN PENGEMBALIAN
                Swal.fire({
                    title: 'Kembalikan Kostum',
                    text: 'Pastikan kostum sudah diterima kembali.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, kembalikan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d'
                }).then(res => {
                    if (res.isConfirmed) {
                        $.ajax({
                            url: `/pengembalian/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(r) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: r.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('pembayaran.index') }}";
                                });
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Gagal',
                                    xhr.responseJSON?.message ??
                                    'Terjadi kesalahan',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });


            // HAPUS DATA
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Hapus data pengembalian?',
                    text: 'Data tidak bisa dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then(res => {
                    if (res.isConfirmed) {
                        $.ajax({
                            url: `/pengembalian/hapus/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(r) {
                                Swal.fire('Berhasil', r.message, 'success');
                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Gagal',
                                    xhr.responseJSON?.message ??
                                    'Terjadi kesalahan',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
