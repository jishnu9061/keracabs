<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('admin/images/fav.png') }}" alt="" height="20">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('admin/images/logo.png') }}" alt="" width="140">
                    </span>
                </a>

            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>


        </div>

        <div class="d-flex">
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item bg-light-subtle border-start border-end"
                    id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <img class="rounded-circle header-profile-user"
                        src="{{ asset('admin/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1 fw-medium">Admin</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item -->
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST"
                        style="display: none;">
                        @csrf
                    </form>
                    <a class="dropdown-item" href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Logout
                    </a>
                </div>

            </div>

        </div>
    </div>
</header>
