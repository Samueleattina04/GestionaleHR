<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GestionaleHR')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root{--sidebar-width:260px;--primary:#2563eb;--sidebar-bg:#1e293b;--sidebar-hover:#334155;--sidebar-text:#94a3b8;}
        *{font-family:'Segoe UI',system-ui,-apple-system,sans-serif;}
        body{background:#f1f5f9;min-height:100vh;}
        .sidebar{position:fixed;top:0;left:0;bottom:0;width:var(--sidebar-width);background:var(--sidebar-bg);z-index:1000;overflow-y:auto;transition:transform .3s;}
        .sidebar-brand{padding:1.25rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.1);display:flex;align-items:center;gap:.75rem;}
        .sidebar-brand h4{color:#fff;margin:0;font-weight:700;font-size:1.1rem;}
        .sidebar-brand .logo-icon{width:38px;height:38px;background:var(--primary);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.2rem;}
        .nav-section{padding:.5rem 1.5rem;font-size:.7rem;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:600;margin-top:.5rem;}
        .sidebar .nav-link{display:flex;align-items:center;padding:.6rem 1.5rem;color:var(--sidebar-text);text-decoration:none;font-size:.875rem;gap:.75rem;border-left:3px solid transparent;transition:all .2s;}
        .sidebar .nav-link:hover{background:var(--sidebar-hover);color:#e2e8f0;}
        .sidebar .nav-link.active{background:rgba(59,130,246,.1);color:#fff;border-left-color:#3b82f6;}
        .sidebar .nav-link i{font-size:1.1rem;width:20px;text-align:center;}
        .main-content{margin-left:var(--sidebar-width);min-height:100vh;}
        .top-navbar{background:#fff;padding:.75rem 1.5rem;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:999;}
        .content-area{padding:1.5rem;}
        .stat-card{background:#fff;border-radius:12px;padding:1.25rem;border:1px solid #e2e8f0;transition:transform .2s;}
        .stat-card:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.08);}
        .stat-card .stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;}
        .stat-card .stat-value{font-size:1.5rem;font-weight:700;color:#1e293b;}
        .stat-card .stat-label{font-size:.8rem;color:#64748b;}
        .card{border:1px solid #e2e8f0;border-radius:12px;box-shadow:none;}
        .card-header{background:#fff;border-bottom:1px solid #e2e8f0;font-weight:600;}
        .table th{font-weight:600;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:#64748b;}
        .btn-primary{background:var(--primary);border-color:var(--primary);}
        @media(max-width:768px){.sidebar{transform:translateX(-100%);}.sidebar.show{transform:translateX(0);}.main-content{margin-left:0;}}
    </style>
    @stack('styles')
</head>
<body>
@auth
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-icon"><i class="bi bi-people-fill"></i></div>
        <h4>GestionaleHR</h4>
    </div>
    <nav>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>

        <div class="nav-section">Presenze & Permessi</div>
        <a href="{{ route('attendances.index') }}" class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}"><i class="bi bi-clock-fill"></i> Presenze</a>
        <a href="{{ route('leaves.index') }}" class="nav-link {{ request()->routeIs('leaves.*') ? 'active' : '' }}"><i class="bi bi-calendar-event-fill"></i> Ferie & Permessi</a>

        @if(in_array(auth()->user()->role, ['admin','hr','manager']))
        <div class="nav-section">Gestione</div>
        @if(in_array(auth()->user()->role, ['admin','hr']))
        <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.index','employees.create','employees.edit','employees.show') ? 'active' : '' }}"><i class="bi bi-people-fill"></i> Dipendenti</a>
        <a href="{{ route('employees.pending') }}" class="nav-link {{ request()->routeIs('employees.pending') ? 'active' : '' }}"><i class="bi bi-person-check-fill"></i> Approvazioni</a>
        <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}"><i class="bi bi-building"></i> Dipartimenti</a>
        @else
        <a href="{{ route('employees.list') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}"><i class="bi bi-people-fill"></i> Team</a>
        @endif
        <a href="{{ route('performance.index') }}" class="nav-link {{ request()->routeIs('performance.*') ? 'active' : '' }}"><i class="bi bi-graph-up-arrow"></i> Valutazioni</a>
        @endif

        <div class="nav-section">Amministrazione</div>
        <a href="{{ route('payrolls.index') }}" class="nav-link {{ request()->routeIs('payrolls.*') ? 'active' : '' }}"><i class="bi bi-wallet2"></i> Cedolini</a>
        <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}"><i class="bi bi-receipt"></i> Note Spese</a>
        <a href="{{ route('documents.index') }}" class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}"><i class="bi bi-folder-fill"></i> Documenti</a>

        <div class="nav-section">Altro</div>
        <a href="{{ route('training.index') }}" class="nav-link {{ request()->routeIs('training.*') ? 'active' : '' }}"><i class="bi bi-mortarboard-fill"></i> Formazione</a>
        <a href="{{ route('communications.index') }}" class="nav-link {{ request()->routeIs('communications.*') ? 'active' : '' }}"><i class="bi bi-megaphone-fill"></i> Comunicazioni</a>
    </nav>
</aside>
<div class="main-content">
    <div class="top-navbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')"><i class="bi bi-list"></i></button>
            <span class="fw-semibold">@yield('page-title', 'Dashboard')</span>
        </div>
        <div class="dropdown">
            <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:.8rem;">{{ strtoupper(substr(auth()->user()->name,0,1)) }}{{ strtoupper(substr(auth()->user()->surname,0,1)) }}</div>
                <span class="d-none d-sm-inline">{{ auth()->user()->name }} {{ auth()->user()->surname }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text text-muted small">{{ ucfirst(auth()->user()->role) }}</span></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>Profilo</a></li>
                <li><form action="{{ route('logout') }}" method="POST">@csrf<button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Esci</button></form></li>
            </ul>
        </div>
    </div>
    <div class="content-area">
        @if(session('success'))<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
        @if(session('error'))<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
        @if(session('warning'))<div class="alert alert-warning alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>{{ session('warning') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        @yield('content')
    </div>
</div>
@else
@yield('content')
@endauth
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
