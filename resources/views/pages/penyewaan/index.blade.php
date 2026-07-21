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

                            // ===============================
                            // Menunggu Pembayaran
                            // ===============================
                            if (d.status_bayar === 'pending' &&
                                d.metode_pembayaran === 'dp') {

                                status = `
                            <span class="badge badge-danger px-3 py-2">
                                <i class="fas fa-hourglass-half mr-1"></i>
                                Menunggu Pembayaran DP
                            </span>
                        `;
                            } else if (d.status_bayar === 'pending' &&
                                d.metode_pembayaran === 'lunas') {

                                status = `
                            <span class="badge badge-warning px-3 py-2">
                                <i class="fas fa-wallet mr-1"></i>
                                Menunggu Pembayaran Lunas
                            </span>
                        `;
                            }

                            // ===============================
                            // Masa Sewa (DP)
                            // ===============================
                            else if (d.status == 0 &&
                                d.status_bayar === 'dp_paid') {

                                status = `
                            <span class="badge badge-secondary px-3 py-2">
                                <i class="fas fa-user-clock mr-1"></i>
                                Masa Sewa
                            </span>
                            <br>
                            <span class="badge badge-success mt-1 px-3 py-2">
                                <i class="fas fa-money-check-alt mr-1"></i>
                                Sudah DP
                            </span>
                        `;
                            }

                            // ===============================
                            // Masa Sewa (Lunas)
                            // ===============================
                            else if (d.status == 0 &&
                                d.status_bayar === 'paid') {

                                status = `
                            <span class="badge badge-secondary px-3 py-2">
                                <i class="fas fa-user-clock mr-1"></i>
                                Masa Sewa
                            </span>
                            <br>
                            <span class="badge badge-primary mt-1 px-3 py-2">
                                <i class="fas fa-check-circle mr-1"></i>
                                Lunas
                            </span>
                        `;
                            }

                            // ===============================
                            // Menunggu Verifikasi
                            // ===============================
                            else if (d.status == 1) {

                                status = `
                            <span class="badge badge-warning px-3 py-2">
                                <i class="fas fa-user-check mr-1"></i>
                                Menunggu Verifikasi
                            </span>
                        `;
                            }

                            // ===============================
                            // Menunggu Pelunasan
                            // ===============================
                            else if (d.status == 2 &&
                                d.status_bayar === 'dp_paid') {

                                status = `
                            <span class="badge badge-primary px-3 py-2">
                                <i class="fas fa-money-bill-wave mr-1"></i>
                                Menunggu Pelunasan
                            </span>
                        `;
                            }

                            // ===============================
                            // Sudah Lunas tetapi ada Denda
                            // ===============================
                            else if (d.status == 2 &&
                                d.status_bayar === 'paid' &&
                                Number(d.denda) > 0) {

                                status = `
                            <span class="badge badge-danger px-3 py-2">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Menunggu Pembayaran Denda
                            </span>
                        `;
                            }

                            // ===============================
                            // Lunas tanpa Denda
                            // ===============================
                            else if (d.status == 2 &&
                                d.status_bayar === 'paid') {

                                status = `
                            <span class="badge badge-success px-3 py-2">
                                <i class="fas fa-check-circle mr-1"></i>
                                Selesai
                            </span>
                        `;
                            }

                            // ===============================
                            // Selesai
                            // ===============================
                            else if (d.status == 3) {

                                status = `
                            <span class="badge badge-success px-3 py-2">
                                <i class="fas fa-check-circle mr-1"></i>
                                Selesai
                            </span>
                        `;
                            }

                            // ===============================
                            // Cek keterlambatan
                            // ===============================

                            if (d.status == 0 &&
                                (d.status_bayar === 'dp_paid' || d.status_bayar === 'paid')) {

                                const today = moment();
                                const kembali = moment(d.tanggal_kembali);

                                if (today.isAfter(kembali, 'day')) {

                                    extra = `
                                    <br>
                                    <span class="badge badge-danger mt-1">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Terlambat
                                    </span>
                                `;

                                } else if (today.isSame(kembali, 'day')) {

                                    extra = `
                                    <br>
                                    <span class="badge badge-warning mt-1">
                                        <i class="fas fa-clock"></i>
                                        Hari Terakhir
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
                            let bayarBtn = '';

                            if (data.status == 0 &&
                                data.status_bayar === 'pending' &&
                                data.metode_pembayaran === 'dp') {

                                bayarBtn = `
                            <button
                                class="btn btn-warning btn-sm mb-1 w-100 btn-bayar"
                                data-id="${id}">
                                <i class="fas fa-money-bill-wave mr-1"></i>
                                Bayar DP
                            </button>
                        `;
                            } else if (data.status == 0 &&
                                data.status_bayar === 'pending' &&
                                data.metode_pembayaran === 'lunas') {

                                bayarBtn = `
                            <button
                                class="btn btn-success btn-sm mb-1 w-100 btn-bayar-lunas"
                                data-id="${id}">
                                <i class="fas fa-wallet mr-1"></i>
                                Bayar Lunas
                            </button>
                        `;
                            } else if (data.status_bayar === 'dp_paid') {

                                bayarBtn = `
                            <button class="btn btn-success btn-sm mb-1 w-100" disabled>
                                <i class="fas fa-check-circle mr-1"></i>
                                Sudah DP
                            </button>
                        `;
                            } else if (data.status_bayar === 'paid') {

                                bayarBtn = `
                            <button
                                class="btn btn-success btn-sm mb-1 w-100"
                                disabled>
                                <i class="fas fa-check-circle mr-1"></i>
                                Lunas
                            </button>
                        `;
                            }
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
                        ${bayarBtn}
                                            
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
                                    confirmButtonText: 'OK',
                                    showConfirmButton: true
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
                                confirmButtonText: 'OK',
                                showConfirmButton: true
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
                                btn.html(
                                    `<i class="fas fa-money-bill-wave mr-1"></i>Bayar DP`
                                    );
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

        // Midtrans SnapToken Lunas Payment
        $(document).on('click', '.btn-bayar-lunas', function() {

            let id = $(this).data('id');
            let btn = $(this);

            $('#loading').fadeIn();

            $.ajax({
                url: `/pembayaran/${id}/snap-tokenLunas`,
                type: 'GET',

                success: function(res) {

                    $('#loading').fadeOut();

                    if (!res.snap_token) {

                        Swal.fire(
                            'Gagal',
                            res.message || 'Snap Token tidak ditemukan',
                            'error'
                        );

                        return;
                    }

                    snap.pay(res.snap_token, {

                        onSuccess: function(result) {

                            btn.removeClass('btn-success btn-bayar-lunas');
                            btn.prop('disabled', true);
                            btn.html(`
                                <i class="fas fa-check-circle mr-1"></i>
                                Lunas
                            `);

                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil',
                                text: 'Pembayaran lunas berhasil.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                table.ajax.reload(null, false);
                            });

                        },

                        onPending: function(result) {

                            Swal.fire({
                                icon: 'info',
                                title: 'Menunggu Pembayaran',
                                text: 'Silakan selesaikan pembayaran.'
                            });

                        },

                        onError: function(result) {

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Pembayaran gagal.'
                            });

                        },

                        onClose: function() {

                            // tidak perlu apa-apa
                            // webhook Midtrans yang akan update status

                        }

                    });

                },

                error: function(xhr) {

                    $('#loading').fadeOut();

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
