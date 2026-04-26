{{-- GANTI ISI file resources/views/layouts/fleet-adminlte.blade.php dengan ini --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Fleet Dashboard' }}</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <style>
        :root {
            --fleet-primary: #1746a2;
            --fleet-secondary: #5f9df7;
            --fleet-dark: #0f172a;
            --fleet-soft: #eef4ff;
            --fleet-success: #16a34a;
            --fleet-warning: #f59e0b;
            --fleet-danger: #dc2626;
            --fleet-card-radius: 20px;
        }
        body { font-family: 'Inter', sans-serif; background: #f4f7fb; }
        .main-sidebar { background: linear-gradient(180deg, #0f172a, #172554 60%, #1d4ed8); }
        .brand-link { border-bottom: 1px solid rgba(255,255,255,.08) !important; padding-top: 1rem; padding-bottom: 1rem; }
        .brand-text { color: #fff; font-weight: 700 !important; letter-spacing: .3px; }
        .nav-sidebar .nav-link { border-radius: 12px; margin-bottom: 6px; color: rgba(255,255,255,.85); }
        .nav-sidebar .nav-link.active, .nav-sidebar .nav-link:hover { background: rgba(255,255,255,.12); color: #fff; }
        .nav-sidebar .nav-header { color: rgba(255,255,255,.45); font-size: 10px; letter-spacing: 1px; padding: .5rem 1rem; }
        .content-wrapper { background: radial-gradient(circle at top right, #eef4ff 0%, #f7f9fc 48%, #f4f7fb 100%); }
        .main-header { border-bottom: 1px solid #e5e7eb; background: rgba(255,255,255,.85); backdrop-filter: blur(8px); }
        .small-box, .card { border: 0; border-radius: var(--fleet-card-radius); box-shadow: 0 15px 40px rgba(15, 23, 42, .06); overflow: hidden; }
        .small-box { min-height: 168px; }
        .small-box .inner { padding: 1.25rem; }
        .small-box .icon { top: 10px; right: 14px; }
        .small-box .icon i { font-size: 56px; opacity: .18; }
        .small-box h3 { font-weight: 700; margin-bottom: .35rem; }
        .small-box p { margin-bottom: .25rem; font-weight: 600; }
        .metric-note { font-size: 12px; opacity: .88; }
        .bg-fleet-primary { background: linear-gradient(135deg, #1746a2, #2563eb); color: #fff; }
        .bg-fleet-success { background: linear-gradient(135deg, #15803d, #22c55e); color: #fff; }
        .bg-fleet-warning { background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff; }
        .bg-fleet-danger { background: linear-gradient(135deg, #b91c1c, #ef4444); color: #fff; }
        .bg-fleet-info { background: linear-gradient(135deg, #0369a1, #0ea5e9); color: #fff; }
        .page-title { font-size: 1.75rem; font-weight: 800; color: #0f172a; }
        .filter-card { background: linear-gradient(135deg, #ffffff, #f8fbff); }
        .stat-chip { display: inline-flex; align-items: center; gap: .4rem; padding: .35rem .7rem; border-radius: 999px; background: #eff6ff; color: #1746a2; font-size: 12px; font-weight: 700; }
        .table thead th { white-space: nowrap; font-size: 12px; text-transform: uppercase; letter-spacing: .2px; border-bottom-width: 1px; }
        .table td { vertical-align: middle; }
        .status-badge { padding: .45rem .8rem; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .status-excellent { background: #dcfce7; color: #166534; }
        .status-good { background: #dbeafe; color: #1d4ed8; }
        .status-monitor { background: #fef3c7; color: #92400e; }
        .status-critical, .status-high-risk { background: #fee2e2; color: #b91c1c; }
        .status-scheduled { background: #dbeafe; color: #1d4ed8; }
        .status-done { background: #dcfce7; color: #166534; }
        .status-overdue { background: #fee2e2; color: #b91c1c; }
        .mini-kpi { border-radius: 18px; background: #fff; padding: 1rem; box-shadow: 0 10px 24px rgba(15, 23, 42, .05); height: 100%; }
        .mini-kpi .value { font-size: 1.35rem; font-weight: 800; color: #0f172a; }
        .mini-kpi .label { color: #64748b; font-size: .875rem; font-weight: 600; }
        .recommendation-list li { margin-bottom: .75rem; line-height: 1.5; }
        @media (max-width: 991.98px) { .small-box { min-height: auto; } .page-title { font-size: 1.4rem; } }
    </style>

    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item d-flex align-items-center pr-2">
                <span class="stat-chip"><i class="fas fa-calendar-alt"></i> {{ $selectedDate ?? now()->toDateString() }}</span>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    <i class="fas fa-user-circle"></i> {{ auth()->user()->name ?? 'Admin' }}
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar elevation-4">
        <a href="{{ route('fleet.dashboard') }}" class="brand-link text-center">
            <span class="brand-text">Fleet SCM</span>
        </a>

        <div class="sidebar pt-3">
            <nav class="mt-2 px-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                    <li class="nav-header">MONITORING</li>
                    <li class="nav-item">
                        <a href="{{ route('fleet.dashboard') }}" class="nav-link {{ request()->routeIs('fleet.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Dashboard Summary</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('fleet.metrics.index') }}" class="nav-link {{ request()->routeIs('fleet.metrics*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Data Harian Unit</p>
                        </a>
                    </li>

                    <li class="nav-header">MASTER DATA</li>
                    <li class="nav-item">
                        <a href="{{ route('fleet.units.index') }}" class="nav-link {{ request()->routeIs('fleet.units*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-truck"></i>
                            <p>Master Unit</p>
                        </a>
                    </li>

                    <li class="nav-header">MAINTENANCE</li>
                    <li class="nav-item">
                        <a href="{{ route('fleet.pm.index') }}" class="nav-link {{ request()->routeIs('fleet.pm.index', 'fleet.pm.create', 'fleet.pm.edit') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>PM Schedule</p>
                            @php $overdueCount = \App\Models\PmSchedule::where('status','overdue')->count(); @endphp
                            @if($overdueCount > 0)
                                <span class="badge badge-danger right">{{ $overdueCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item has-treeview {{ request()->routeIs('fleet.pm-reports*', 'fleet.bus-lv-reports*', 'fleet.p2h*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('fleet.pm-reports*', 'fleet.bus-lv-reports*', 'fleet.p2h*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>
                                Report Weekly
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('fleet.pm-reports.index') }}" class="nav-link {{ request()->routeIs('fleet.pm-reports*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>PM Check</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('fleet.bus-lv-reports.index') }}" class="nav-link {{ request()->routeIs('fleet.bus-lv-reports*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Bus & LV</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('fleet.p2h.index') }}" class="nav-link {{ request()->routeIs('fleet.p2h.index', 'fleet.p2h.dashboard', 'fleet.p2h.daily.*', 'fleet.p2h.units.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>P2H Online</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible mx-3 mt-3 border-0 rounded-lg shadow-sm" role="alert">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible mx-3 mt-3 border-0 rounded-lg shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>
