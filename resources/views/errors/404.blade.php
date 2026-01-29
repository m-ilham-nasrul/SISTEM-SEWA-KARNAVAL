<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>404 | Halaman Tidak Ditemukan</title>

    <!-- Fonts & SB Admin 2 -->
    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Auth Style (SAMA DENGAN LOGIN & REGISTER) -->
    <link href="{{ asset('css/auth-style.css') }}" rel="stylesheet">
</head>

<body class="login-bg d-flex align-items-center justify-content-center min-vh-100">
    <div class="glass-card text-center">

        <div class="mb-3">
            <i class="fas fa-exclamation-triangle login-icon"></i>
        </div>
        <h1 class="display-4 font-weight-bold text-white mb-2">404</h1>
        <h5 class="text-white font-weight-semibold mb-2">
            Halaman Tidak Ditemukan
        </h5>
        <p class="text-light small mb-4">
            Maaf, halaman yang Anda cari tidak tersedia atau telah dipindahkan.
        </p>
        <!-- Button -->
        @auth
            <a href="{{ url('/dashboard') }}" class="btn btn-login-modern btn-block">
                Kembali ke Halaman
            </a>
        @else
            <a href="{{ url('/') }}" class="btn btn-login-modern btn-block">
                Kembali ke Halaman
            </a>
        @endauth
    </div>
</body>

</html>
