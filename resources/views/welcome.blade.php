<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Sewa Karnaval</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> />
    <link rel="stylesheet" href="/css/landing.css">

</head>

<body>

    <!-- NAVBAR UPDATED -->
    <nav class="navbar navbar-expand-lg shadow-sm fixed-top"
        style="background: linear-gradient(90deg, #1e3a8a, #2563eb);">
        <div class="container">
            <a class="navbar-brand fw-bold text-white d-flex align-items-center" href="#"><i
                    class="fa-solid fa-masks-theater me-2"></i> SEWA KARNAVAL</a>
            <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 fw-semibold">
                    <li class="nav-item"><a class="nav-link text-white" href="#home">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#layanan">Layanan</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#galeri">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#video">Video</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#kontak">Kontak</a></li>
                </ul>
                @if (Auth::check())
                    <a href="/dashboard" class="btn btn-light ms-3 px-4">Dashboard</a>
                    <a href="#" class="btn btn-danger ms-2 px-4" onclick="confirmLogout(event)">Logout</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-light ms-3 px-4">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-warning ms-2 px-4">Sign Up</a>
                @endif

            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section id="home" class="hero-section" data-aos="fade-up">
        <div class="container">
            <h1 class="display-4 fw-bold" data-aos="fade-up">Sewa Kostum & Ogoh-Ogoh</h1>
            <p class="lead mb-4" data-aos="fade-up" data-aos-delay="200">Solusi terbaik untuk parade, festival, dan
                acara besar Anda.</p>
            <a href="#layanan" class="btn btn-light btn-lg px-5 py-3 fw-semibold" data-aos="zoom-in"
                data-aos-delay="300">Jelajahi Sekarang</a>
        </div>
    </section>

    <!-- LAYANAN SECTION -->
    <section id="layanan" class="py-5" style="background: #f0f4ff;">
        <div class="container text-center">
            <h2 class="fw-bold text-primary mb-5" data-aos="fade-down">Layanan Kami</h2>
            <div class="row g-4">
                <!-- Card 1 -->
                <div class="col-md-6" data-aos="fade-up">
                    <div class="card shadow service-card p-4 border-0 rounded-4"
                        style="background: #ffffff; border-left: 6px solid #1d4ed8;">
                        <div class="fs-1 text-primary">🎭</div>
                        <h4 class="fw-bold mt-3">Sewa Kostum Karnaval</h4>
                        <p class="text-muted">Kostum untuk acara festival dan parade budaya.</p>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="card shadow service-card p-4 border-0 rounded-4"
                        style="background: #ffffff; border-left: 6px solid #1e40af;">
                        <div class="fs-1 text-primary">🔥</div>
                        <h4 class="fw-bold mt-3">Sewa Ogoh-Ogoh</h4>
                        <p class="text-muted">Penyewaan ogoh-ogoh untuk parade budaya dan perayaan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- GALERI SECTION -->
    <section id="galeri" class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="fw-bold text-primary mb-5" data-aos="fade-down">Galeri Kami</h2>

            <div class="row g-4" data-aos="fade-up">
                <div class="col-md-4"><img class="w-100 gallery-img shadow" src="{{ asset('img/img1.jpg') }}">
                </div>
                <div class="col-md-4"><img class="w-100 gallery-img shadow" src="{{ asset('img/img2.jpg') }}">
                </div>
                <div class="col-md-4"><img class="w-100 gallery-img shadow" src="{{ asset('img/img3.jpg') }}">
                </div>
            </div>
        </div>
    </section>

    <!-- VIDEO SECTION -->
    <section id="video" class="py-5 text-center">
        <div class="container">
            <h2 class="fw-bold text-primary mb-4" data-aos="fade-down">Video Parade Ogoh-Ogoh</h2>
            <p class="text-muted mb-4" data-aos="fade-up">Tonton aksi spektakuler parade kami.</p>
            <div class="ratio ratio-16x9 shadow rounded-4 mx-auto" style="max-width: 800px;" data-aos="zoom-in">
                <iframe src="https://www.youtube.com/embed/7EcuirJJZbE" allowfullscreen></iframe>
            </div>
        </div>
    </section>

    <!-- KONTAK SECTION -->
    <section id="kontak" class="py-5 text-white" style="background: linear-gradient(90deg, #1e3a8a, #2563eb);">
        <div class="container text-center">
            <h2 class="fw-bold mb-3" data-aos="fade-up">Hubungi Kami</h2>
            <p class="mb-4" data-aos="fade-up" data-aos-delay="100">Kami siap melayani kebutuhan kostum & ogoh-ogoh
                Anda.</p>
            <a href="https://wa.me/62895364796180" target="_blank" class="btn btn-light btn-lg px-4 py-2 fw-bold"
                data-aos="zoom-in" data-aos-delay="200">💬 Chat via WhatsApp</a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white text-center py-3 shadow-sm">
        <p class="mb-0 text-muted">&copy; 2025 <span class="fw-bold text-primary">Sewa Karnaval </span> All Rights
            Reserved.</p>
    </footer>

    <script>
        AOS.init();
    </script>

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
</body>

</html>
