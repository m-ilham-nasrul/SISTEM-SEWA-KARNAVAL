@extends('layout.app')
@section('title', 'Dashboard')
@section('content')
    <div class="container-fluid">

        <!-- Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                Dashboard {{ Auth::user()->role === 'admin' ? 'Admin' : 'Penyewa' }}
            </h1>
        </div>

        {{-- ================= ADMIN AREA ================= --}}
        @if (Auth::user()->role === 'admin')
            <div class="row">

                <!-- Total Penyewa -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Penyewa
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" data-dashboard="penyewa">
                                    {{ $penyewa }}
                                </div>
                            </div>
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>

                <!-- Penyewaan Aktif -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Penyewaan Aktif
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $sewa }}
                                </div>
                            </div>
                            <i class="fas fa-calendar-check fa-2x text-info"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Kostum -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-secondary shadow h-100 py-2">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Kostum
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" data-dashboard="kostum">
                                    {{ $kostum }}
                                </div>
                            </div>
                            <i class="fas fa-masks-theater fa-2x text-secondary"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Penyewaan -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Total Penyewaan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" data-dashboard="sewa">
                                    {{ $sewa }}
                                </div>
                            </div>
                            <i class="fas fa-clipboard-list fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Transaksi -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-dark shadow h-100 py-2">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Total Transaksi
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" data-dashboard="total_transaksi">
                                    {{ $total_transaksi }}
                                </div>
                            </div>
                            <i class="fas fa-receipt fa-2x text-dark"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Pendapatan -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Pendapatan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" data-dashboard="total_pendapatan">
                                    Rp {{ number_format($total_pendapatan, 0, ',', '.') }}
                                </div>
                            </div>
                            <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                        </div>
                    </div>
                </div>

            </div>
        @endif

        {{-- ================= Penyewa AREA ================= --}}
        @if (Auth::user()->role === 'penyewa')
            <div class="row">

                <!-- Penyewaan Aktif Saya -->
                <div class="col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Penyewaan Aktif Saya
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $sewa }}
                                </div>
                            </div>
                            <i class="fas fa-calendar-check fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Transaksi Saya -->
                <div class="col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Transaksi Saya
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $total_transaksi }}
                                </div>
                            </div>
                            <i class="fas fa-receipt fa-2x text-success"></i>
                        </div>
                    </div>
                </div>

            </div>
        @endif

        {{-- ================= RIWAYAT SEWA ================= --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history mr-2"></i>Riwayat Penyewaan Terakhir
                </h6>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 text-center align-middle">
                        <thead class="bg-light">
                            <tr class="text-uppercase small text-muted">
                                <th>Kostum</th>
                                <th>Tanggal Sewa</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($riwayatSewa as $item)
                                @php
                                    $today = \Carbon\Carbon::today();
                                    $kembali = \Carbon\Carbon::parse($item->tanggal_kembali);
                                @endphp
                                <tr>
                                    <td class="text-left">
                                        @forelse ($item->kostum_list as $kostum)
                                            <span class="badge badge-info mb-1 d-inline-block px-2 py-1">
                                                {{ $kostum->nama_kostum }}
                                            </span>
                                        @empty
                                            <span class="text-muted fst-italic">Kostum dihapus</span>
                                        @endforelse
                                    </td>

                                    <td>
                                        <span class="badge badge-light px-3 py-2">
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            {{ $item->tanggal_sewa->format('d M Y') }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge badge-light px-3 py-2">
                                            <i class="fas fa-undo mr-1"></i>
                                            {{ $kembali->format('d M Y') }}
                                        </span>
                                    </td>

                                    <td>
                                        @if ($item->status == 1)
                                            <span class="badge badge-success px-3 py-2">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Dikembalikan
                                            </span>
                                        @else
                                            <span class="badge badge-secondary px-3 py-2">
                                                <i class="fas fa-hourglass-half mr-1"></i>
                                                Masa Sewa
                                            </span>

                                            @if ($today->gt($kembali))
                                                <div class="mt-1">
                                                    <span class="badge badge-danger px-3 py-2">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                                        Terlambat
                                                    </span>
                                                </div>
                                            @elseif ($today->equalTo($kembali))
                                                <div class="mt-1">
                                                    <span class="badge badge-warning px-3 py-2">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        Hari Terakhir
                                                    </span>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-muted">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Belum ada riwayat penyewaan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('addon-script')
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        // Fungsi untuk load data dashboard via AJAX
        function loadDashboardData() {
            $.ajax({
                url: "{{ route('dashboard.data') }}", // Route yang mengembalikan JSON
                method: "GET",
                dataType: "json",
                success: function(data) {
                    // Update tiap card dashboard
                    $('[data-dashboard="penyewa"]').text(data.penyewa ?? 0);
                    $('[data-dashboard="kostum"]').text(data.kostum ?? 0);
                    $('[data-dashboard="sewa"]').text(data.sewa ?? 0);
                    $('[data-dashboard="total_transaksi"]').text(data.total_transaksi ?? 0);
                    $('[data-dashboard="total_pendapatan"]').text(
                        'Rp ' + (data.total_pendapatan ? new Intl.NumberFormat('id-ID').format(data
                            .total_pendapatan) : '0')
                    );
                },
                error: function(xhr, status, error) {
                    console.error("Gagal memuat data dashboard:", error);
                }
            });
        }

        // Load data saat halaman pertama kali dibuka
        $(document).ready(function() {
            loadDashboardData();

            // Optional: refresh data tiap 30 detik
            setInterval(loadDashboardData, 30000);
        });
    </script>
@endpush
