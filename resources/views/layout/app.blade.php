<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SEWA KARNAVAL | Dashboard')</title>

    <!-- Font dan CSS -->
    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <!-- Custom Dashboard Style -->
    <link href="{{ asset('css/dashboard-modern.css') }}" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('addon-style')
</head>


<body id="page-top">

    <div id="wrapper">

        @include('layout.sidebar')

        <!-- CONTENT WRAPPER -->
        <div id="content-wrapper" class="d-flex flex-column">

            @include('layout.topbar')

            <!-- AREA SCROLL -->
            <div id="content" class="content-scroll">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="text-center my-auto">
                        <span>© 2025 Sistem Informasi Penyewaan Karnaval</span>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <!-- Scroll Button -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script>
        function confirmLogout(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Yakin ingin logout?',
                text: 'Anda akan keluar dari sistem',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('logout') }}",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Logout berhasil',
                                timer: 1200,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "{{ route('login') }}";
                            });
                        },
                        error: function() {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat logout', 'error');
                        }
                    });
                }
            });
        }
    </script>

    <!-- SBAdmin2 Scripts -->
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>


    @stack('addon-script')
    @stack('scripts')

    <!-- SweetAlert Notif -->
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 1500,
                showConfirmButton: false
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('error') }}",
                timer: 1500,
                showConfirmButton: false
            });
        @elseif ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                timer: 1500,
                showConfirmButton: false
            });
        @endif
    </script>
</body>

</html>
