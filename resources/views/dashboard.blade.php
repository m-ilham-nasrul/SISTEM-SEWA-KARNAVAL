@extends('layout.app')
@section('title', 'Dashboard')

@section('content')

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

        <!-- Content Row -->
        <div class="row">

            <!-- Total Penyewa -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Penyewa
                                </div>
                                <div class="h5 mb-0 font-weight-bold" data-dashboard="penyewa">{{ $penyewa }}</div>
                            </div>

                            <div class="d-flex align-items-center justify-content-center"
                                style="width:50px;height:50px;border-radius:10px;background:#3b82f6;margin-left:10px;">
                                <i class="fas fa-users text-white" style="font-size:22px;"></i>
                            </div>

                        </div>
                    </div>
                </div>
            </div>



            <!-- Total Kostum -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Kostum
                                </div>
                                <div class="h5 mb-0 font-weight-bold" data-dashboard="kostum">{{ $kostum }}</div>
                            </div>

                            <div class="d-flex align-items-center justify-content-center"
                                style="width:50px;height:50px;border-radius:10px;background:#8b5cf6;margin-left:10px;">
                                <i class="fas fa-masks-theater text-white" style="font-size:22px;"></i>
                            </div>

                        </div>
                    </div>
                </div>
            </div>



            <!-- Total Penyewaan -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Total Penyewaan
                                </div>
                                <div class="h5 mb-0 font-weight-bold" data-dashboard="sewa">{{ $sewa }}</div>
                            </div>

                            <div class="d-flex align-items-center justify-content-center"
                                style="width:50px;height:50px;border-radius:10px;background:#facc15;margin-left:10px;">
                                <i class="fas fa-calendar-check text-white" style="font-size:22px;"></i>
                            </div>

                        </div>
                    </div>
                </div>
            </div>




            <!-- Total Transaksi -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Total Transaksi
                                </div>
                                <div class="h5 mb-0 font-weight-bold" data-dashboard="total_transaksi">
                                    {{ $total_transaksi }}</div>
                            </div>

                            <div class="d-flex align-items-center justify-content-center"
                                style="width:50px;height:50px;border-radius:10px;background:#6b7280;margin-left:10px;">
                                <i class="fas fa-clipboard-list text-white" style="font-size:22px;"></i>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Pendapatan -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Pendapatan
                                </div>
                                <div class="h5 mb-0 font-weight-bold" data-dashboard="total_pendapatan">
                                    Rp {{ number_format($total_pendapatan, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-center"
                                style="width:50px;height:50px;border-radius:10px;background:#22c55e;margin-left:10px;">
                                <i class="fas fa-money-bill-wave text-white" style="font-size:22px;"></i>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /.container-fluid -->

@endsection

@push('addon-script')
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        function loadDashboardData() {
            $.ajax({
                url: "{{ route('dashboard.data') }}",
                method: "GET",
                success: function(data) {
                    $('[data-dashboard="penyewa"]').text(data.penyewa);
                    $('[data-dashboard="kostum"]').text(data.kostum);
                    $('[data-dashboard="sewa"]').text(data.sewa);
                    $('[data-dashboard="total_transaksi"]').text(data.total_transaksi);
                    $('[data-dashboard="total_pendapatan"]').text(
                        'Rp ' + new Intl.NumberFormat('id-ID').format(data.total_pendapatan)
                    );
                },
                error: function() {
                    console.error("Gagal memuat data dashboard.");
                }
            });
        }

        // Load awal
        loadDashboardData();

        // Optional: auto-refresh tiap 30 detik
        setInterval(loadDashboardData, 30000);
    </script>
@endpush
