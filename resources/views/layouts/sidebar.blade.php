<!-- BEGIN: Sidebar -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('vuexy/assets/img/favicon/icon-banner.png') }}" alt="Vemto Logo"
                    class="w-px-120 h-auto" />
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2"></span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="icon-base ti tabler-x d-block d-xl-none"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @auth
            <!-- Dashboard -->
            <li class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <a href="{{ route('home') }}" class="menu-link">
                    <i class="menu-icon ti tabler-home"></i>
                    <div>Dashboard</div>
                </a>
            </li>

            @canany(['view-any:Category', 'view-any:SubCategory'])
                <!-- Apps -->
                <li
                    class="menu-item {{ request()->is('categories*') || request()->is('sub-categories*') ? 'open active' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon ti tabler-apps"></i>
                        <div>Management</div>
                    </a>
                    <ul class="menu-sub">
                        @can('view-any', App\Models\Category::class)
                            <li class="menu-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                <a href="{{ route('categories.index') }}" class="menu-link">
                                    <div>Categories</div>
                                </a>
                            </li>
                        @endcan
                        @can('view-any', App\Models\SubCategory::class)
                            <li class="menu-item {{ request()->routeIs('sub-categories.*') ? 'active' : '' }}">
                                <a href="{{ route('sub-categories.index') }}" class="menu-link">
                                    <div>Sub Categories</div>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @can('view-any', App\Models\Value::class)
                <li class="menu-item {{ request()->routeIs('value') ? 'active' : '' }}">
                    <a href="{{ route('value') }}" class="menu-link">
                        <i class="menu-icon ti tabler-activity"></i>
                        <div>Rekap</div>
                    </a>
                </li>
            @endcan


            <!-- Access Management -->
            @if (Auth::user()->can('view-any', App\Models\User::class) ||
                    Auth::user()->can('view-any', Spatie\Permission\Models\Role::class) ||
                    Auth::user()->can('view-any', Spatie\Permission\Models\Permission::class))
                <li class="menu-item {{ request()->is('roles*') || request()->is('permissions*') ? 'open active' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon ti tabler-key"></i>
                        <div>Access Management</div>
                    </a>
                    <ul class="menu-sub">
                        @can('view-any', App\Models\User::class)
                            <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <a href="{{ route('users.index') }}" class="menu-link">
                                    <div>Users</div>
                                </a>
                            </li>
                        @endcan
                        @can('view-any', Spatie\Permission\Models\Role::class)
                            <li class="menu-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                <a href="{{ route('roles.index') }}" class="menu-link">
                                    <div>Roles</div>
                                </a>
                            </li>
                        @endcan
                        @can('view-any', Spatie\Permission\Models\Permission::class)
                            <li class="menu-item {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                                <a href="{{ route('permissions.index') }}" class="menu-link">
                                    <div>Permissions</div>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
        @endauth

        <!-- Logout -->
        @auth
            <li class="menu-item">
                <a href="{{ route('logout') }}" class="menu-link"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="menu-icon ti tabler-logout"></i>
                    <div>{{ __('Logout') }}</div>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        @endauth
    </ul>
</aside>
<!-- END: Sidebar -->
