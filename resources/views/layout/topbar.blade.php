<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">
        @auth
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">

                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                        @if (Auth::user()->role === 'admin')
                            Admin - {{ Auth::user()->name }}
                        @elseif (Auth::user()->role === 'penyewa')
                            Penyewa - {{ Auth::user()->name }}
                        @else
                            {{ Auth::user()->name }}
                        @endif
                    </span>

                    <img class="img-profile rounded-circle"
                        src="{{ Auth::user()->photo
                            ? asset('storage/profile/' . Auth::user()->photo)
                            : asset('sbadmin2/img/undraw_profile.svg') }}">
                </a>

                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">

                    <!-- PROFILE -->
                    <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('profile.index') }}">
                        <i class="fas fa-user-circle fa-2x text-primary"></i>
                        <div class="ml-3">
                            <div class="font-weight-bold">Profil Saya</div>
                            <small class="text-muted">Kelola akun Anda</small>
                        </div>
                    </a>

                    <div class="dropdown-divider"></div>

                    <!-- INFO USER -->
                    <div class="px-3 py-2">
                        <small class="text-muted d-block">Nama</small>
                        <strong>{{ Auth::user()->name }}</strong>

                        <small class="text-muted d-block mt-2">Email</small>
                        <strong>{{ Auth::user()->email }}</strong>

                        <small class="text-muted d-block mt-2">Role</small>

                        @if (Auth::user()->role === 'admin')
                            <span class="badge badge-warning">Admin</span>
                        @elseif (Auth::user()->role === 'penyewa')
                            <span class="badge badge-success">Penyewa</span>
                        @else
                            <span class="badge badge-secondary">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                        @endif
                    </div>

                    <div class="dropdown-divider"></div>

                    <!-- LOGOUT -->
                    <a class="dropdown-item text-danger" href="#" onclick="confirmLogout(event)">
                        <i class="fa-solid fa-right-from-bracket fa-sm fa-fw mr-2"></i>
                        Logout
                    </a>
                </div>
            </li>
        @endauth
    </ul>
</nav>
