<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">
        @if (Auth::check())
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">

                    {{-- Untuk Admin --}}
                    @if (Auth::user()->role == 'admin')
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                            Admin - {{ Auth::user()->name }}
                        </span>
                    @endif

                    {{-- Untuk Penyewa --}}
                    @if (Auth::user()->role == 'penyewa')
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                            Penyewa : {{ Auth::user()->name }}
                        </span>
                    @endif

                    <img class="img-profile rounded-circle" src="{{ asset('sbadmin2/img/undraw_profile.svg') }}">
                </a>

                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                        <i class="fa-solid fa-right-from-bracket fa-sm fa-fw mr-2 text-gray-400"></i>
                        Logout
                    </a>
                </div>
            </li>
        @endif
    </ul>

</nav>
