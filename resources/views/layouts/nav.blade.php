<!-- BEGIN: Navbar -->
<nav class="layout-navbar container-lg navbar navbar-detached navbar-expand-xxl align-items-center bg-navbar-theme"
    id="layout-navbar">

    <!-- Menu toggle (mobile only) -->
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="icon-base ti tabler-menu-2 icon-md"></i>
        </a>
    </div>


    <!-- Right Side -->
    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">

        <ul class="navbar-nav flex-row align-items-center ms-auto">

            <!-- Style Switcher -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                    id="nav-theme" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="icon-base ti tabler-sun icon-22px theme-icon-active text-heading"></i>
                    <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
                    <li>
                        <button type="button" class="dropdown-item align-items-center active"
                            data-bs-theme-value="light" aria-pressed="false">
                            <span><i class="icon-base ti tabler-sun icon-22px me-3" data-icon="sun"></i>Light</span>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark"
                            aria-pressed="true">
                            <span><i class="icon-base ti tabler-moon-stars icon-22px me-3"
                                    data-icon="moon-stars"></i>Dark</span>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system"
                            aria-pressed="false">
                            <span><i class="icon-base ti tabler-device-desktop-analytics icon-22px me-3"
                                    data-icon="device-desktop-analytics"></i>System</span>
                        </button>
                    </li>
                </ul>
            </li>
            <!-- / Style Switcher-->

            <!-- Guest (Login) -->
            @guest
                <li class="nav-item">
                    <a class="nav-link btn btn-sm btn-primary d-flex align-items-center" href="{{ route('login') }}">
                        <i class="icon-base ti tabler-login me-1"></i>
                        <span>{{ __('Login') }}</span>
                    </a>
                </li>
            @endguest

            <!-- Authenticated (User Dropdown) -->
            @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center" href="javascript:void(0);"
                        data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="{{ Auth::user()->avatar_url ?? asset('vuexy/assets/img/avatars/1.png') }}" alt
                                class="rounded-circle" />
                        </div>
                        <span class="d-none d-md-inline ms-2 fw-medium">
                            {{ Auth::user()->name }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="icon-base ti tabler-logout me-2"></i>
                                <span>Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            @endauth

        </ul>
    </div>
</nav>
<!-- END: Navbar -->
