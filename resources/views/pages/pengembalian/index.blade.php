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
$(document).ready(function () {

    let role = '{{ Auth::user()->role }}';

    let table = $('#dataTable').DataTable({

        ajax: "{{ route('pengembalian.index') }}",

        columns: [

            {
                data: null,
                render: (data, type, row, meta) => meta.row + 1
            },

            { data: 'kode_sewa' },

            {
                data: 'penyewa.user.name',
                defaultContent: '<small>Penyewa dihapus</small>'
            },

            {
                data: 'kostum_list',
                orderable: false,
                searchable: false,
                render: function (k) {

                    if (k && k.length){
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

        // tampilkan denda hanya jika rusak
        if (d.kondisi === 'rusak' && d.denda > 0) {
            dendaHtml = `
                <br>
                <small class="text-danger">
                    Denda: Rp ${ (d.denda || 0).toLocaleString() }
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
    render: function (d) {

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
                render: function (d) {
                    let status = '';
                    if (d.status == 0) {
                        status = `
                            <span class="badge badge-secondary">
                                <i class="fas fa-hourglass-half"></i>
                                Masa Sewa
                            </span>
                        `;
                    }
                    else if (d.status == 1) {
                        status = `
                            <span class="badge badge-warning">
                                <i class="fas fa-user-check"></i>
                                Menunggu Verifikasi
                            </span>
                        `;
                    }
                    else if (d.status == 2) {
                        if(role === 'penyewa' && !d.status_bayar){
                            status = `
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i>
                                    Diverifikasi Admin
                                </span>
                                <br>
                                <span class="badge badge-info">
                                    <i class="fas fa-credit-card"></i>
                                    Silakan lakukan pembayaran
                                </span>
                            `;
                        }
                        else if(role === 'admin' && !d.status_bayar){
                            status = `
                                <span class="badge badge-warning">
                                    <i class="fas fa-clock"></i>
                                    Menunggu Pembayaran
                                </span>
                                <br>
                                <span class="badge badge-secondary">
                                    <i class="fas fa-user-clock"></i>
                                    Menunggu penyewa membayar
                                </span>
                            `;
                        }
                        else{
                            status = `
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i>
                                    Kembali
                                </span>
                            `;
                        }
                    }
                    let bayar = `
                        <br>
                        <span class="badge badge-${d.status_bayar ? 'success' : 'danger'}">
                            <i class="fas ${d.status_bayar ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                            ${d.status_bayar ? 'Telah Terbayar' : 'Belum Membayar'}
                        </span>
                    `;
                    return status + bayar;
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data) {
                    let id = data.id;
                    /* ===== NOTA ===== */
                    let notaBtn = '';
                    if (data.status_bayar) {
                        notaBtn = `
                            <a href="/pembayaran/${id}/nota"
                               class="btn btn-info btn-sm mb-1 w-100">
                               <i class="fas fa-file-invoice"></i> Nota
                            </a>
                        `;
                    }
                    /* ===== PENYEWA AJUKAN ===== */
                    let returnBtn = '';
                    if (role === 'penyewa' && data.status == 0) {
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
                    if (role === 'penyewa' && data.status == 2 && !data.status_bayar) {
                    bayarBtn = `
                            <a href="/pembayaran"
                            class="btn btn-primary btn-sm mb-1 w-100">
                            <i class="fas fa-credit-card"></i> Lanjutkan Pembayaran
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
                        <div class="d-flex flex-column">
                            ${notaBtn}
                            ${bayarBtn}
                            ${returnBtn}
                            <div class="dropdown mt-1">
                                <button class="btn btn-light btn-sm w-100"
                                        data-toggle="dropdown">
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
    $(document).on('click', '.btn-request', function () {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Ajukan Pengembalian?',
            text: 'Admin akan memverifikasi kondisi kostum.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya'
        }).then(res => {
            if (res.isConfirmed) {
                $.ajax({
                    url: `/pengembalian/request/${id}`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (r) {
                        table.ajax.reload();
                    }
                });
            }
        });
    });
    /* ===============================
   ADMIN VERIFIKASI
    =============================== */
$(document).on('click', '.btn-verifikasi', function () { 
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
                success: function (r) {
                    table.ajax.reload();
                }
            });
        }
    });
});

$(document).on('change', '#kondisi', function () {
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
    table.on('xhr', function(){
    if(notified) return;
    let data = table.ajax.json().data;
    data.forEach(function(d){
        if(role === 'penyewa' && d.status == 2 && !d.status_bayar){
            notified = true;
            Swal.fire({
                icon:'info',
                title:'Pengembalian Diverifikasi',
                text:'Admin sudah memverifikasi pengembalian kostum. Silakan lanjutkan pembayaran.',
                timer: 1500,
                showConfirmButton: false
            });
           }
        });
      });
        /* ===============================
            NOTIFIKASI ADMIN
        ================================ */
        let adminNotified = false;
        table.on('xhr', function(){
            if(adminNotified) return;
            let data = table.ajax.json().data;
            data.forEach(function(d){
                if(role === 'admin' && d.status == 1){
                    adminNotified = true;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pengajuan Pengembalian',
                        text: 'Ada penyewa yang mengajukan pengembalian kostum.',
                        timer: 1500,
                        showConfirmButton: false
                 });
                }
            });
        });
    /* ===============================
       DELETE DATA
    =============================== */
    $(document).on('click', '.btn-delete', function () {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Hapus data?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus'
        }).then(res => {
            if (res.isConfirmed) {
                $.ajax({
                    url: `/pengembalian/hapus/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (r) {
                        table.ajax.reload();
                    }
                });
            }
        });
    });
});
</script>

@endpush