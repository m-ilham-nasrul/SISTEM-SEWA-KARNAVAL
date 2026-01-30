<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Login</title>

    <!-- Fonts & SB Admin 2 -->
    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Custom Auth Style -->
    <link href="{{ asset('css/auth-style.css') }}" rel="stylesheet">
</head>

<body class="login-bg d-flex align-items-center justify-content-center min-vh-100">

    <div class="glass-card p-5">
        <div class="text-center mb-4">
            <i class="fas fa-theater-masks login-icon"></i>
            <h1 class="h4 font-weight-bold text-white mt-3">SEWA KARNAVAL</h1>
            <p class="text-light small">Silakan masuk untuk melanjutkan</p>
        </div>

        <form method="POST" action="{{ route('login.process') }}">
            @csrf

            <!-- Email -->
            <div class="form-group">
                <input type="email" name="email" class="form-control input-modern" placeholder="Masukkan Email"
                    value="{{ old('email') }}" required>
            </div>

            <!-- Password + Eye Toggle (Sesuai permintaan Anda) -->
            <div class="form-group position-relative">
                <input type="password" name="password" id="password" class="form-control input-modern pr-5"
                    placeholder="Masukkan Password" required>

                <span class="toggle-password" onclick="togglePassword('password','iconPass')">
                    <i id="iconPass" data-lucide="eye"></i>
                </span>
            </div>

            <button type="submit" class="btn btn-login-modern btn-block py-2 mt-2">
                Login
            </button>
        </form>

        <hr class="divider-modern">

        <div class="text-center">
            <small class="text-light">Kembali ke Beranda?
                <a href="{{ Auth::check() ? route('dashboard') : url('/') }}" class="text-primary font-weight-bold">Klik
                    Disini</a>
            </small>
        </div>

        <div class="text-center mt-2">
            <small class="text-light">Belum punya akun?
                <a href="{{ route('register') }}" class="text-primary font-weight-bold">Daftar</a>
            </small>
        </div>

    </div>

</body>

</html>

@stack('addon-script')

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            text: "{{ session('error') }}"
        });
    @endif
</script>

<!-- Eye Toggle Script (Untuk lucide icon) -->
<script>
    function togglePassword(fieldId, iconId) {
        const input = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);

        if (input.type === "password") {
            input.type = "text";
            icon.setAttribute("data-lucide", "eye-off");
        } else {
            input.type = "password";
            icon.setAttribute("data-lucide", "eye");
        }

        lucide.createIcons(); // refresh icon
    }
</script>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
