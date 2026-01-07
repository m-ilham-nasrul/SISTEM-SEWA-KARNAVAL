<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/') }}">
        <div class="sidebar-brand-icon">
            <i class="fa-solid fa-masks-theater"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SEWA KARNAVAL</div>
    </a>
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fa-solid fa-house-chimney"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Manajemen -->
    @if (Auth::user()->role === 'admin')
        <hr class="sidebar-divider">
        <div class="sidebar-heading">Menu admin</div>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster">
                <i class="fa-solid fa-folder-open"></i>
                <span>Manajemen Data</span>
            </a>
            <div id="collapseMaster" class="collapse">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('user.index') }}">
                        <i class="fa-solid fa-user-gear fa-sm text-info mr-2"></i> Data Akun User
                    </a>
                    <a class="collapse-item" href="{{ route('penyewa.index') }}">
                        <i class="fa-solid fa-user-group fa-sm text-warning mr-2"></i> Data Penyewa
                    </a>
                    <a class="collapse-item" href="{{ route('kostum.index') }}">
                        <i class="fa-solid fa-shirt fa-sm text-primary mr-2"></i> Data Kostum
                    </a>
                </div>
            </div>
        </li>
    @endif

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Menu Penyewaan</div>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('penyewa.index') }}">
            <i class="fa-solid fa-user"></i>
            <span>Daftar Penyewa</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('penyewaan.select') }}">
            <i class="fas fa-shopping-cart"></i>
            <span>Sewa Kostum</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('penyewaan.index') }}">
            <i class="fa-solid fa-handshake"></i>
            <span>Penyewaan</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('pembayaran.index') }}">
            <i class="fa-solid fa-money-check-dollar"></i>
            <span>Pembayaran</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('pengembalian.index') }}">
            <i class="fa-solid fa-rotate-left"></i>
            <span>Pengembalian</span>
        </a>
    </li>
    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
