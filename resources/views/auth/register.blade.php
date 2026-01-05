<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Register</title>

    <!-- Fonts -->
    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">

    <!-- SB Admin CSS -->
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Style CSS -->
    <link href="{{ asset('css/auth-style.css') }}" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="login-bg d-flex align-items-center justify-content-center min-vh-100">

    <div class="glass-card">
        <div class="text-center mb-4">
            <i class="fas fa-user-plus register-icon"></i>
            <h1 class="h4 font-weight-bold text-white mt-3">Buat Akun Baru</h1>
            <p class="text-light small">Silakan isi data lengkap Anda</p>
        </div>

        <form method="POST" action="{{ route('register.process') }}">
            @csrf

            <div class="form-group">
                <input type="text" name="name" class="form-control input-modern" placeholder="Nama Lengkap"
                    required>
            </div>

            <div class="form-group">
                <input type="email" name="email" class="form-control input-modern" placeholder="Email" required>
            </div>

            @php
                $adminExists = \App\Models\User::where('role', 'admin')->exists();
            @endphp

            <!-- SELECT ROLE -->
            @if (!$adminExists)
                <div class="form-group select-modern-wrapper">
                    <select name="role" class="select-modern" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin">Admin</option>
                        <option value="penyewa">Penyewa</option>
                    </select>
                </div>
            @else
                <!-- Admin sudah ada → role otomatis penyewa -->
                <input type="hidden" name="role" value="penyewa">
            @endif

            <!-- PASSWORD + EYE -->
            <div class="form-group password-wrapper">
                <input type="password" id="password" name="password" class="form-control input-modern"
                    placeholder="Password" required>

                <span class="toggle-password" onclick="togglePassword('password','iconPass')">
                    <i id="iconPass" data-lucide="eye"></i>
                </span>
            </div>

            <!-- CONFIRM PASSWORD + EYE -->
            <div class="form-group password-wrapper">
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="form-control input-modern" placeholder="Konfirmasi Password" required>

                <span class="toggle-password" onclick="togglePassword('password_confirmation','iconConfirm')">
                    <i id="iconConfirm" data-lucide="eye"></i>
                </span>
            </div>

            <button type="submit" class="btn btn-login-modern btn-block mt-2">Register</button>
        </form>

        <hr class="divider-modern">

        <div class="text-center">
            <small class="text-light">Sudah punya akun?
                <a href="{{ route('login') }}" class="text-primary font-weight-bold">Login disini</a>
            </small>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function togglePassword(inputId, iconId) {
            const field = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (field.type === "password") {
                field.type = "text";
                icon.setAttribute("data-lucide", "eye-off");
            } else {
                field.type = "password";
                icon.setAttribute("data-lucide", "eye");
            }

            lucide.createIcons();
        }
    </script>

    @stack('addon-script')

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
                text: "{{ session('error') }}",
            });
        @endif
    </script>

</body>

</html>
