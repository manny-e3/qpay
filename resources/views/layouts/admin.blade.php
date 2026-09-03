<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'OTP Cloud Admin')</title>
    <!-- DashLite CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/dashlite.css') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    @stack('css')
</head>
<body class="nk-body bg-lighter npc-general has-sidebar">
    <div class="nk-app-root">
        <div class="nk-main">
            <!-- Sidebar -->
            <div class="nk-sidebar nk-sidebar-fixed is-dark" data-content="sidebarMenu">
                <div class="nk-sidebar-element nk-sidebar-head">
                    <div class="nk-sidebar-brand">
                        <a href="{{ route('admin.dashboard') }}" class="logo-link">
                            <h1 class="logo-text" style="color: white; font-size: 18px; font-weight: 700; margin: 0;">
                                <em class="icon ni ni-shield-check-fill"></em> OTP Cloud
                            </h1>
                        </a>
                    </div>
                    <div class="nk-menu-trigger me-n2">
                        <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
                    </div>
                </div>
                <div class="nk-sidebar-element">
                    <div class="nk-sidebar-content">
                        <div class="nk-sidebar-menu" data-simplebar>
                            <ul class="nk-menu">
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">Main Menu</h6>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.dashboard') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-dashlite"></em></span>
                                        <span class="nk-menu-link-text">Dashboard</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.apps.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-grid-alt"></em></span>
                                        <span class="nk-menu-link-text">Applications</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.payment.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-wallet-out"></em></span>
                                        <span class="nk-menu-link-text">Payments</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('test.payment') }}" class="nk-menu-link" target="_blank">
                                        <span class="nk-menu-icon"><em class="icon ni ni-play-circle"></em></span>
                                        <span class="nk-menu-link-text">Payment Test Page</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.settings.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-setting-alt"></em></span>
                                        <span class="nk-menu-link-text">Global Settings</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.logs.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-list-check"></em></span>
                                        <span class="nk-menu-link-text">Audit Logs</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sidebar End -->

            <!-- Main Wrap -->
            <div class="nk-wrap">
                <!-- Header -->
                <div class="nk-header nk-header-fixed is-light">
                    <div class="container-fluid">
                        <div class="nk-header-wrap">
                            <div class="nk-menu-trigger d-xl-none ms-n1">
                                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
                            </div>
                            <div class="nk-header-brand d-xl-none">
                                <a href="{{ route('admin.dashboard') }}" class="logo-link">
                                    <em class="icon ni ni-shield-check-fill text-primary"></em>
                                </a>
                            </div>
                            <div class="nk-header-tools">
                                <ul class="nk-quick-nav">
                                    <li class="dropdown user-dropdown">
                                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                                            <div class="user-toggle">
                                                <div class="user-avatar sm">
                                                    <em class="icon ni ni-user-alt"></em>
                                                </div>
                                                <div class="user-info d-none d-md-block">
                                                    <div class="user-status">Administrator</div>
                                                    <div class="user-name dropdown-indicator">{{ Auth::user()->name ?? 'Admin User' }}</div>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end dropdown-menu-s1">
                                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                                <div class="user-card">
                                                    <div class="user-avatar">
                                                        <span>{{ substr(Auth::user()->name ?? 'AD', 0, 2) }}</span>
                                                    </div>
                                                    <div class="user-info">
                                                        <span class="lead-text">{{ Auth::user()->name ?? 'Admin User' }}</span>
                                                        <span class="sub-text">{{ Auth::user()->email ?? 'admin@example.com' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li>
                                                        <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                                            @csrf
                                                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><em class="icon ni ni-signout"></em><span>Sign out</span></a>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Header End -->

                <!-- Content -->
                <div class="nk-content">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            <div class="nk-content-body">
                                <div class="nk-block-head nk-block-head-sm">
                                    <div class="nk-block-between">
                                        <div class="nk-block-head-content">
                                            <h3 class="nk-block-title page-title">@yield('page_title')</h3>
                                            <div class="nk-block-des text-soft">
                                                <p>@yield('page_subtitle')</p>
                                            </div>
                                        </div>
                                        <div class="nk-block-head-content">
                                            @yield('page_actions')
                                        </div>
                                    </div>
                                </div>

                                @if(session('success'))
                                    <div class="alert alert-success alert-icon alert-dismissible">
                                        <em class="icon ni ni-check-circle"></em> {{ session('success') }}
                                        <button class="close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger alert-icon alert-dismissible">
                                        <em class="icon ni ni-cross-circle"></em> {{ session('error') }}
                                        <button class="close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Content End -->

                <!-- Footer -->
                <div class="nk-footer">
                    <div class="container-fluid">
                        <div class="nk-footer-wrap">
                            <div class="nk-footer-copyright"> &copy; {{ date('Y') }} OTP Cloud. </div>
                        </div>
                    </div>
                </div>
                <!-- Footer End -->
            </div>
            <!-- Main Wrap End -->
        </div>
    </div>
    <!-- JavaScript -->
    <script src="{{ asset('assets/js/bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    @stack('js')
</body>
</html>
