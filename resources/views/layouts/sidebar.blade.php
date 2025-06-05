<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link">
        <img src="{{ asset('assets/favicon/favicon-32x32.png') }}" alt="Vemto Logo" class="brand-image bg-white img-circle">
        <span class="brand-text font-weight-light">task</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu">

                @auth
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link">
                            <i class="nav-icon icon ion-md-speedometer"></i>
                            <p>
                                Dashboard
                            </p>
                        </a>
                    </li>

                    @can('view-any', App\Models\Employee::class)
                        <li class="nav-item">
                            <a href="{{ route('employees.index') }}" class="nav-link">
                                <i class="nav-icon icon ion-md-people"></i>
                                <p>Employees</p>
                            </a>
                        </li>
                    @endcan
                    @can('view-any', App\Models\Task::class)
                        <li class="nav-item">
                            <a href="{{ route('tasks.index') }}" class="nav-link">
                                <i class="nav-icon icon ion-md-list-box"></i>
                                <p>Tasks</p>
                            </a>
                        </li>
                    @endcan
                    @can('view-any', App\Models\User::class)
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link">
                                <i class="nav-icon icon ion-md-person"></i>
                                <p>Users</p>
                            </a>
                        </li>
                    @endcan
                    @can('view-any', App\Models\AccessToken::class)
                        <li class="nav-item">
                            <a href="{{ route('access-tokens.index') }}" class="nav-link">
                                <i class="nav-icon icon ion-md-key"></i>
                                <p>Access Tokens</p>
                            </a>
                        </li>
                    @endcan
                @endauth


                {{-- <li class="nav-item">
                    <a href="https://adminlte.io/docs/3.1//index.html" target="_blank" class="nav-link">
                        <i class="nav-icon icon ion-md-help-circle-outline"></i>
                        <p>Docs</p>
                    </a>
                </li> --}}

                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="nav-icon icon ion-md-exit"></i>
                            <p>{{ __('Logout') }}</p>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                @endauth
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
