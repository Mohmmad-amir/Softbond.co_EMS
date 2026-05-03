<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SoftCo — @yield('title', 'My Panel')</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<div class="layout">

    {{--  Sidebar --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <h1>SoftCo</h1>
            <p>Employee Portal</p>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-label">My Panel</div>

                {{-- Dashboard --}}
                <a class="nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}"
                   href="{{ route('employee.dashboard') }}">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
                    Dashboard
                </a>

                {{-- My Profile --}}
                <a class="nav-item {{ request()->routeIs('employee.profile*') ? 'active' : '' }}"
                   href="{{ route('employee.profile.index') }}">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                    </svg>
                    My Profile
                </a>

                {{-- My Tasks --}}
                <a class="nav-item {{ request()->routeIs('employee.tasks*') ? 'active' : '' }}"
                   href="{{ route('employee.tasks.index') }}">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="9 11 12 14 22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                    My Tasks
                </a>

                {{-- My Attendance --}}
                <a class="nav-item {{ request()->routeIs('employee.attendance*') ? 'active' : '' }}"
                   href="{{ route('employee.attendance.index') }}">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <path d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                    My Attendance
                </a>

                {{-- Salary --}}
                <a class="nav-item {{ request()->routeIs('employee.salary*') ? 'active' : '' }}"
                   href="{{ route('employee.salary.index') }}">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2"/>
                        <path d="M2 10h20"/>
                    </svg>
                    Salary
                </a>

            </div>
        </nav>

        {{--  Sidebar Footer --}}
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="avatar av-teal">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div>
                    <div class="name">{{ Auth::user()->name }}</div>
                    <div class="role">
                        {{ Auth::user()->employee->designation ?? 'Employee' }}
                    </div>
                </div>
                {{--  Logout --}}
                <a class="logout" href="{{ route('admin.logout') }}" title="Logout" onclick="return confirm('Are you sure you want to logout?')">
                    &#x2715;
                </a>
            </div>
        </div>
    </aside>

    {{--  Main Content --}}
    <div class="main-content">

        {{--  Topbar --}}
        <div class="topbar">
            <div class="topbar-title">@yield('title', 'My Panel')</div>
            <div class="topbar-right">
                <span class="text-muted" style="font-size:12px">
                    {{ now()->format('D, d M Y') }}
                </span>
            </div>
        </div>

        {{--  Page Body --}}
        <div class="page-body">

            {{--  Success Message --}}
            @if(session('success'))
                <div class="alert alert-success" id="flashAlert">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error Message --}}
            @if(session('error'))
                <div class="alert alert-danger" id="flashAlert">
                    {{ session('error') }}
                </div>
            @endif

            {{--  Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{--  Page Content --}}
            @yield('content')

        </div>
    </div>
</div>

{{--  Auto hide flash message --}}
<script>
    setTimeout(() => {
        const el = document.getElementById('flashAlert');
        if (el) {
            el.style.transition = 'opacity 0.5s ease';
            el.style.opacity    = '0';
            setTimeout(() => el.remove(), 500);
        }
    }, 3000);
</script>

@stack('scripts') {{--  for page specific JS --}}

</body>
</html>
