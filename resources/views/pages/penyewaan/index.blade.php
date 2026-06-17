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
                                <th>Kode Sewa</th>
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
    {{-- ================= LOADING ================= --}}
    <div id="loading"
        style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;
        background:rgba(255,255,255,0.8);z-index:9999;justify-content:center;align-items:center">
        <div class="text-center">
            <div class="spinner-border text-primary mb-2"></div>
            <p>Memproses...</p>
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
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>

    <script>
        var table;
        $(document).ready(function() {

            table = $('#dataTable').DataTable({
                processing: true,
                ajax: "{{ route('penyewaan.index') }}", // Route harus mengembalikan JSON
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'kode_sewa',
                        defaultContent: '-'
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
                            if (d.status == 0 && d.status_bayar === 'pending') {

                                status = `
                                    <span class="badge badge-danger px-3 py-2">
                                        <i class="fas fa-clock mr-1"></i>
                                        Menunggu DP
                                    </span>
                                `;

                            } else if (d.status == 0 && d.status_bayar === 'dp_paid') {

                                status = `
                                    <span class="badge badge-secondary px-3 py-2">
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
                                        <i class="fas fa-user-check"></i>
                                        Menunggu Verifikasi
                                    </span>
                                `;

                            } else if (d.status == 2) {

                                status = `
                                    <span class="badge badge-primary px-3 py-2">
                                        <i class="fas fa-money-bill-wave mr-1"></i>
                                        Menunggu Pelunasan
                                    </span>
                                `;

                            } else if (d.status == 3) {

                                status = `
                                    <span class="badge badge-success px-3 py-2">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Selesai
                                    </span>
                                `;
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
                            let bayarDpBtn = (data.status == 0 && data.status_bayar === 'pending') ?
                                `
                            <button
                                class="btn btn-warning btn-sm mb-1 w-100 btn-bayar"
                                data-id="${id}">
                                <i class="fas fa-money-bill-wave mr-1"></i>
                                Bayar DP
                            </button>
                            ` :
                            (data.status == 0 && data.status_bayar === 'dp_paid') ?
                                `
                            <button
                                class="btn btn-success btn-sm mb-1 w-100">
                                <i class="fas fa-check-circle mr-1"></i>
                                Sudah DP
                            </button>
                            ` :
                                '';

                            let editBtn = '';
                            let deleteBtn = '';
                            // ===== EDIT =====
                            if (data.status_bayar === 'pending') {
                                editBtn = `
                                    <a href="/penyewaan/${id}/edit" class="dropdown-item">
                                        <i class="fas fa-edit mr-2"></i> Edit
                                    </a>
                                `;
                            }
                            // ===== BATALKAN =====
                            if (data.status_bayar === 'pending') {
                                deleteBtn = `
                                    <button class="dropdown-item text-danger btn-delete"
                                            data-id="${id}">
                                        <i class="fas fa-trash mr-2"></i> Batalkan
                                    </button>
                                `;
                            }
                            return `
                            ${bayarDpBtn}
                            <div class="d-flex flex-column align-items-center">
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
        });

        // Midtrans SnapToken DP Payment
        $(document).on('click', '.btn-bayar', function() {

            let id = $(this).data('id');
            let btn = $(this);
            
            // Show loading
            $('#loading').fadeIn();

            $.ajax({
                url: `/pembayaran/${id}/snap-tokenDP`,
                type: 'GET',

                success: function(res) {
                    $('#loading').fadeOut();
                    console.log(res);

                    if (!res.snap_token) {
                        // Restore button
                        btn.prop('disabled', false);
                        btn.html(`<i class="fas fa-money-bill-wave mr-1"></i>Bayar DP`);
                        
                        Swal.fire(
                            'Gagal',
                            'Snap Token tidak ditemukan',
                            'error'
                        );
                        return;
                    }

                    snap.pay(res.snap_token, {

                        onSuccess: function(result) {

                            // Update button immediately to show success state
                            btn.removeClass('btn-warning btn-bayar');
                            btn.addClass('btn-success');
                            btn.prop('disabled', true);
                            btn.html(`<i class="fas fa-check-circle mr-1"></i>Sudah DP`);

                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil',
                                text: 'DP berhasil dibayar',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                table.ajax.reload(null, false);
                            });
                        },

                        onPending: function(result) {

                            Swal.fire({
                                icon: 'info',
                                title: 'Menunggu Pembayaran',
                                text: 'Silakan selesaikan pembayaran'
                            });
                        },

                        onError: function(result) {

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Pembayaran gagal'
                            });
                            
                            // Restore button only on error
                            setTimeout(() => {
                                btn.prop('disabled', false);
                                btn.html(`<i class="fas fa-money-bill-wave mr-1"></i>Bayar DP`);
                            }, 500);
                        },

                        onClose: function() {
                            // Don't restore button on close - let the notification webhook update DB
                            // Table will reload with correct status from database
                        }
                    });
                },

                error: function(xhr) {
                    // Restore button
                    btn.prop('disabled', false);
                    btn.html(`<i class="fas fa-money-bill-wave mr-1"></i>Bayar DP`);

                    Swal.fire(
                        'Gagal',
                        xhr.responseJSON?.message || 'Gagal mengambil Snap Token',
                        'error'
                    );
                }
            });

        });
    </script>
@endpush
