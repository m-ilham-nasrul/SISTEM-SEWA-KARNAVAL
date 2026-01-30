@extends('layout.app')
@section('title', 'Pembayaran')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Pembayaran Sewa</h1>
        </div>

        <!-- Ringkasan Pendapatan -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pendapatan Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold">Rp. {{ number_format($pendapatan_hari) }}</div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center bg-success"
                            style="width: 50px; height: 50px; border-radius: 10px;">
                            <i class="fas fa-dollar-sign text-white" style="font-size: 22px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pendapatan Bulan Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold">Rp. {{ number_format($pendapatan_bulan) }}</div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center bg-primary"
                            style="width: 50px; height: 50px; border-radius: 10px;">
                            <i class="fas fa-chart-line text-white" style="font-size: 22px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Pembayaran -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    Data Pembayaran <span class="badge badge-secondary">{{ $statusTitle }}</span>
                </h6>
            </div>
            <div class="card-body">
                <!-- Filter -->
                <div class="mx-auto mb-3">
                    <div class="btn-group" role="group">
                        <button class="btn btn-dark filter-btn" data-status="">All</button>
                        <button class="btn btn-warning filter-btn" data-status="0">Menunggu Pembayaran</button>
                        <button class="btn btn-success filter-btn" data-status="1">Telah Terbayar</button>
                    </div>

                </div>

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
                                <th>Denda</th>
                                <th>Total Bayar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- DataTables AJAX --}}
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

            let statusBayar = '';

            let table = $('#dataTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('pembayaran.index') }}",
                    data: function(d) {
                        d.status_bayar = statusBayar;
                    }
                },
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'kode_sewa',
                        render: (data, type, row) => data ?? `SEWA-${String(row.id).padStart(4,'0')}`
                    },
                    {
                        data: 'penyewa.user.name',
                        defaultContent: '<small>Data penyewa telah dihapus!</small>'
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
                        data: 'denda',
                        render: d => `Rp. ${Number(d).toLocaleString()}`
                    },
                    {
                        data: 'total_biaya',
                        render: d => `Rp. ${Number(d).toLocaleString()}`
                    },
                    {
                        data: null,
                        render: data => {
                            let status = data.status ? 'Kembali' : 'Masa Sewa';
                            let bayar = data.status_bayar ? 'Telah Terbayar' : 'Belum Membayar';
                            let badgeStatus = data.status ? 'success' : 'secondary';
                            let badgeBayar = data.status_bayar ? 'success' : 'danger';

                            return `
                        <span class="badge badge-${badgeStatus}">${status}</span><br>
                        <span class="badge badge-${badgeBayar} mt-1">${bayar}</span>
                    `;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: data => {
                            let id = data.id;
                            let role = '{{ Auth::user()->role }}';

                            /*Bayar*/
                            let bayarBtn = !data.status_bayar ?
                                `<a href="/pengembalian/${id}/edit" class="btn btn-success btn-sm mb-1 w-100">
                               <i class="fas fa-money-bill-wave mr-1"></i> Bayar
                           </a>` :
                                '';
                            /*Nota*/
                            let notaBtn = data.status_bayar ?
                                `<a href="/pembayaran/${id}/nota" class="btn btn-info btn-sm mb-1 w-100">
                               <i class="fas fa-file-invoice mr-1"></i> Nota
                           </a>` :
                                '';

                            let editBtn = '';
                            let deleteBtn = '';

                            if (role === 'admin' || (role === 'penyewa' && !data.status_bayar)) {
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
                            ${bayarBtn}
                            ${notaBtn}
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

            // ===== FILTER BUTTON =====
            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');

                statusBayar = $(this).data('status');
                table.ajax.reload();
            });

            // DELETE AJAX + SWEETALERT
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin?',
                    text: 'Data pembayaran akan dihapus!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/pembayaran/${id}`,
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
        });
    </script>
@endpush
