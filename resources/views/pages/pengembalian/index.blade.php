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
                                <th>Kondisi</th>
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
        $(document).ready(function() {

            let role = '{{ Auth::user()->role }}';

            let table = $('#dataTable').DataTable({

                ajax: "{{ route('pengembalian.index') }}",

                columns: [

                    {
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
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
                        render: function(k) {

                            if (k && k.length) {
                                return k.map(x => x.nama_kostum).join('<br>');
                            }

                            return '<small>Kostum dihapus</small>';
                        }
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
                        data: null,
                        render: d => {
                            let total = (d.total_biaya || 0) + (d.denda || 0);

                            let dendaHtml = '';

                            if (d.denda > 0) {
                                dendaHtml = `
                                    <br>
                                    <small class="text-danger">
                                        Denda: Rp ${Number(d.denda).toLocaleString()}
                                    </small>
                                `;
                            }

                            return `
                            Rp. ${total.toLocaleString()}
                            ${dendaHtml}
                        `;
                        }
                    },

                    {
                        data: null,
                        render: function(d) {

                            // PRIORITAS: kalau status belum selesai
                            if (d.status == 0 || d.status == 1) {
                                return `<span class="badge badge-secondary">Belum Dicek</span>`;
                            }

                            if (d.kondisi === 'baik') {
                                return `<span class="badge badge-success">Baik</span>`;
                            } else {
                                return `
                                <span class="badge badge-danger" title="${d.catatan || 'Tidak ada catatan'}">
                                    Rusak
                                </span>
                            `;
                            }
                        }
                    },

                    {
                        data: null,
                        render: function(d) {
                            let status = '';
                            if (d.status == 0) {
                                if (d.status_bayar === 'pending') {
                                    status = `
                                    <span class="badge badge-danger px-3 py-2">
                                        <i class="fas fa-clock"></i>
                                        Menunggu DP
                                    </span>
                                `;
                                } else if (d.status_bayar === 'dp_paid') {
                                    status = `
                                    <span class="badge badge-secondary px-3 py-2">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Masa Sewa
                                    </span>
                                    <br>
                                    <span class="badge badge-success px-3 py-2 mt-1">
                                        <i class="fas fa-money-check-alt mr-1"></i>
                                        Sudah DP
                                    </span>
                                `;
                                }
                            } else if (d.status == 1) {
                                status = `
                                    <span class="badge badge-warning px-3 py-2 ">
                                        <i class="fas fa-user-check"></i>
                                        Menunggu Verifikasi
                                    </span>
                                `;
                            } else if (d.status == 2) {
                                status = `
                                    <span class="badge badge-primary px-3 py-2">
                                        <i class="fas fa-money-bill-wave"></i>
                                        Menunggu Pelunasan
                                    </span>
                                `;
                            } else if (d.status == 3) {
                                status = `
                                    <span class="badge badge-success px-3 py-2">
                                        <i class="fas fa-check-circle"></i>
                                        Selesai
                                    </span>
                                `;
                            }
                            return status;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            let id = data.id;
                            /* ===== NOTA ===== */
                            let notaBtn = '';
                            // show nota only when fully paid
                            if (data.status_bayar === 'paid') {
                                notaBtn = `
                            <a href="/pembayaran/${id}/nota"
                               class="btn btn-info btn-sm mb-1 w-100">
                               <i class="fas fa-file-invoice"></i> Nota
                            </a>
                        `;
                            }
                            /* ===== PENYEWA AJUKAN ===== */
                            let returnBtn = '';
                            if (role === 'penyewa' && data.status == 0 && data.status_bayar ===
                                'dp_paid') {
                                returnBtn = `
                            <button class="btn btn-warning btn-sm mb-1 w-100 btn-request" data-id="${id}">
                                <i class="fas fa-paper-plane"></i> Ajukan Pengembalian
                            </button>
                        `;
                            }

                            /* ===== ADMIN VERIFIKASI ===== */
                            if (role === 'admin' && data.status == 1) {

                                returnBtn = `
                            <button class="btn btn-success btn-sm mb-1 w-100 btn-verifikasi" data-id="${id}"> 
                                <i class="fas fa-check-circle"></i> Verifikasi
                            </button>
                        `;
                            }
                            /* ===== PEMBAYARAN ===== */
                            let bayarBtn = '';
                            if (role === 'penyewa' && data.status == 2 && data.status_bayar !==
                                'paid') {
                                const label = data.status_bayar === 'dp_paid' ?
                                    'Lanjutkan Pelunasan' : 'Lanjutkan Pembayaran';
                                bayarBtn = `
                            <a href="/pembayaran/${id}/bayar"
                            class="btn btn-primary btn-sm mb-1 w-100">
                            <i class="fas fa-credit-card"></i> ${label}
                            </a>
                        `;
                            }
                            /* ===== DELETE ADMIN ===== */
                            let editBtn = '';
                            let deleteBtn = '';
                            if (role === 'admin') {
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
                            ${bayarBtn}
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


            /* ===============================
               PENYEWA AJUKAN PENGEMBALIAN
            =============================== */
            $(document).on('click', '.btn-request', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Ajukan Pengembalian?',
                    text: 'Admin akan memverifikasi kondisi kostum.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {

                    if (result.isConfirmed) {

                        $.ajax({
                            url: `/pengembalian/request/${id}`,
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },

                            success: function(res) {

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Pengajuan pengembalian berhasil dikirim. Silakan menunggu verifikasi dari admin.',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    table.ajax.reload(null, false);
                                });

                            },

                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Pengajuan pengembalian gagal dikirim.',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });

                    }

                });
            });
            /* ===============================
               ADMIN VERIFIKASI
                =============================== */
            $(document).on('click', '.btn-verifikasi', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Verifikasi Kostum',
                    html: `
        <label>Kondisi Kostum</label>
        <select id="kondisi" class="form-control">
            <option value="baik">Baik</option>
            <option value="rusak">Rusak</option>
        </select>

        <div id="form-rusak" style="display:none;">
            <label class="mt-2">Denda</label>
            <input type="number" id="denda"
                   class="form-control"
                   value="0"
                   min="0">

            <label class="mt-2">Catatan Kerusakan</label>
            <textarea id="catatan"
                      class="form-control"
                      placeholder="Isi jika ada kerusakan"></textarea>
        </div>
    `,
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',

                    preConfirm: () => {
                        const kondisi = $('#kondisi').val();
                        const denda = $('#denda').val();
                        const catatan = $('#catatan').val();

                        // VALIDASI CATATAN
                        if (kondisi === 'rusak' && (!catatan || catatan.trim() === '')) {
                            Swal.showValidationMessage('Catatan wajib diisi jika rusak');
                            return false;
                        }

                        // VALIDASI DENDA
                        if (kondisi === 'rusak') {
                            if (!denda || denda <= 0) {
                                Swal.showValidationMessage('Denda harus diisi jika rusak');
                                return false;
                            }

                            if (isNaN(denda)) {
                                Swal.showValidationMessage('Denda harus berupa angka');
                                return false;
                            }
                        }

                        return {
                            kondisi: kondisi,
                            denda: denda,
                            catatan: catatan
                        };
                    }

                }).then(result => {

                    if (result.isConfirmed) {

                        $.ajax({
                            url: `/pengembalian/verifikasi/${id}`,
                            type: 'POST',
                            data: {
                                kondisi: result.value.kondisi,
                                denda: result.value.denda,
                                catatan: result.value.catatan
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(r) {

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Verifikasi Berhasil',
                                    html: `
                                    Hari Terlambat: <b>${r.hari_terlambat}</b><br>
                                    Denda Keterlambatan:
                                    <b>Rp ${Number(r.denda_terlambat).toLocaleString()}</b><br>
                                    Total Denda:
                                    <b>Rp ${Number(r.total_denda).toLocaleString()}</b>
                                `
                                });
                                table.ajax.reload(null, false);
                                table.columns.adjust();
                            }
                        });
                    }
                });
            });

            $(document).on('change', '#kondisi', function() {
                if ($(this).val() === 'rusak') {
                    $('#form-rusak').show();
                } else {
                    $('#form-rusak').hide();

                    // reset nilai
                    $('#denda').val(0);
                    $('#catatan').val('');
                }
            });
            /* ===============================
                NOTIFIKASI PENYEWA
            =============================== */
            let notified = false;
            table.on('xhr', function() {
                if (notified) return;
                let data = table.ajax.json().data;
                data.forEach(function(d) {
                    if (role === 'penyewa' && d.status == 2 && d.status_bayar === 'dp_paid') {
                        notified = true;
                        Swal.fire({
                            icon: 'info',
                            title: 'Pengembalian Diverifikasi',
                            text: 'Admin sudah memverifikasi pengembalian kostum. Silakan lanjutkan pembayaran.',
                            confirmButtonText: 'OK',
                            showConfirmButton: true
                        });
                    }
                });
            });
            /* ===============================
                NOTIFIKASI ADMIN
            ================================ */
            let adminNotified = false;
            table.on('xhr', function() {
                if (adminNotified) return;
                let data = table.ajax.json().data;
                data.forEach(function(d) {
                    if (role === 'admin' && d.status == 1) {
                        adminNotified = true;
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pengajuan Pengembalian',
                            text: 'Ada penyewa yang mengajukan pengembalian kostum.',
                            confirmButtonText: 'OK',
                            showConfirmButton: true
                        });
                    }
                });
            });
            /* ===============================
               DELETE DATA
            =============================== */
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin?',
                    text: 'Data pengembalian akan dihapus!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/pengembalian/hapus/${id}`,
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
                table.ajax.reload(null, false);
            }, 30000);
        });
    </script>
@endpush
