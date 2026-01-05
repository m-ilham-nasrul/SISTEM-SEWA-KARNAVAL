@extends('layout.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="container-fluid">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Profil Saya</h1>

            <a href="#" id="btnKembali" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>

        </div>

        <div class="row">

            {{-- INFO USER --}}
            <div class="col-md-4">
                <div class="card shadow text-center">
                    <div class="card-body">
                        <img id="previewFoto" class="img-profile rounded-circle mb-3" width="120"
                            src="{{ $user->photo ? asset('storage/profile/' . $user->photo) : asset('sbadmin2/img/undraw_profile.svg') }}">

                        <form id="formPhoto">
                            @csrf

                            {{-- input file disembunyikan --}}
                            <input type="file" name="photo" id="photoInput" class="d-none" accept="image/*">

                            <button type="button" id="btnUbahFoto" class="btn btn-sm btn-info">
                                <i class="fas fa-camera"></i> Ubah Foto
                            </button>

                            @if ($user->photo)
                                <button type="button" id="btnHapusFoto" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Hapus Foto
                                </button>
                            @endif
                        </form>



                        <h5 class="font-weight-bold">{{ $user->name }}</h5>
                        <p class="text-muted mb-1">{{ $user->email }}</p>

                        @if ($user->role === 'admin')
                            <span class="badge badge-warning">
                                Admin
                            </span>
                        @elseif ($user->role === 'penyewa')
                            <span class="badge badge-success">
                                Penyewa
                            </span>
                        @else
                            <span class="badge badge-secondary">
                                {{ ucfirst($user->role) }}
                            </span>
                        @endif

                        <hr>

                        <small class="text-muted">
                            Bergabung sejak<br>
                            {{ $user->created_at->format('d M Y') }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-8">

                {{-- EDIT PROFIL --}}
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Edit Profil</h6>
                    </div>
                    <div class="card-body">
                        <form id="formProfil">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                            </div>

                            @if ($user->role === 'penyewa' && $user->penyewa)
                                <div class="form-group">
                                    <label>No. Telepon</label>
                                    <input type="text" name="no_telp" class="form-control"
                                        value="{{ $user->penyewa->no_telp ?? '' }}" placeholder="Masukkan nomor telepon">
                                </div>

                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap">{{ $user->penyewa->alamat ?? '' }}</textarea>
                                </div>
                            @endif

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Profil
                            </button>
                        </form>
                    </div>
                </div>

                {{-- GANTI PASSWORD --}}
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h6 class="m-0 font-weight-bold">Ganti Password</h6>
                    </div>
                    <div class="card-body">
                        <form id="formPassword">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>Password Lama</label>
                                <input type="password" name="password_lama" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Password Baru</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>

                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-key"></i> Ubah Password
                            </button>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        /* =======================
                    UPDATE PROFIL
                ======================= */
        $('#formProfil').submit(function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Menyimpan...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: "{{ route('profile.update') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Profil berhasil diperbarui',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('penyewa.index') }}";
                    });
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let pesan = '';
                    $.each(errors, function(key, value) {
                        pesan += value[0] + '<br>';
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        html: pesan
                    });
                }
            });
        });

        /* =======================
           UPDATE PASSWORD
        ======================= */
        $('#formPassword').submit(function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: "{{ route('profile.password') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Password diubah',
                        text: 'Password berhasil diperbarui',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#formPassword')[0].reset();
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let pesan = '';
                    $.each(errors, function(key, value) {
                        pesan += value[0] + '<br>';
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        html: pesan
                    });
                }
            });
        });

        /* =======================
            KEMBALI KE DASHBOARD
        ======================= */
        $('#btnKembali').click(function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Kembali ke Dashboard?',
                text: 'Perubahan yang belum disimpan akan hilang.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, kembali',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {
                    window.location.href = "{{ route('dashboard') }}";
                }
            });
        });

        /* =======================
           PILIH & UPLOAD FOTO
        ======================= */

        // klik tombol → buka file chooser
        $('#btnUbahFoto').on('click', function() {
            $('#photoInput').click();
        });

        // setelah file dipilih → upload otomatis
        $('#photoInput').on('change', function() {

            if (!this.files.length) return;

            let formData = new FormData();
            formData.append('photo', this.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            Swal.fire({
                title: 'Mengunggah foto...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: "{{ route('profile.photo') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // update foto tanpa reload
                    $('#previewFoto').attr(
                        'src',
                        res.photo + '?' + new Date().getTime()
                    );

                    // reset input file
                    $('#photoInput').val('');
                },
                error: function() {
                    Swal.fire('Gagal', 'Upload foto gagal', 'error');
                }
            });
        });

        /* =======================
            HAPUS FOTO PROFIL
           =======================*/
        $('#btnHapusFoto').click(function() {

            Swal.fire({
                title: 'Hapus foto profil?',
                text: 'Foto akan dikembalikan ke default.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((res) => {

                if (!res.isConfirmed) return;

                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: "{{ route('profile.photo.delete') }}",
                    method: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Ganti ke gambar default SB Admin
                        $('#previewFoto').attr(
                            'src',
                            "{{ asset('sbadmin2/img/undraw_profile.svg') }}"
                        );

                        // Hilangkan tombol hapus
                        $('#btnHapusFoto').remove();
                    },
                    error: function() {
                        Swal.fire('Gagal', 'Tidak dapat menghapus foto', 'error');
                    }
                });
            });
        });
    </script>
@endpush
