<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SoftCo Admin — </title>
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
</head>
<body>
<div class="layout">
<aside class="sidebar">
  <div class="sidebar-logo">
    <h1>SoftCo</h1>
    <p>Business Management</p>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">
      <div class="nav-section-label">Overview</div>
      <a class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
         href="{{ route('admin.dashboard') }}">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        Dashboard
      </a>
    </div>
    <div class="nav-section">
      <div class="nav-section-label">People</div>
      <a class="nav-item {{ request()->routeIs('admin.employees') ? 'active' : '' }}" href="{{ route('admin.employees') }}">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Employees
      </a>
      <a class="nav-item {{ request()->routeIs('admin.attendance') ? 'active' : '' }}" href="{{ route('admin.attendance') }}">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        Attendance
      </a>
      <a class="nav-item {{ request()->routeIs('admin.salary') ? 'active' : '' }}" href="{{ route('admin.salary') }}">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 2"/></svg>
        Salary Requests
{{--          @if($pendingSalary > 0)--}}
{{--              <span class="nav-badge">{{ $pendingSalary }}</span>--}}
{{--          @endif          --}}
                </a>
    </div>
    <div class="nav-section">
      <div class="nav-section-label">Work</div>
      <a class="nav-item {{ request()->routeIs('admin.project') ? 'active' : '' }}" href="{{ route('admin.project') }}">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
        Projects
      </a>
      <a class="nav-item {{ request()->routeIs('admin.task') ? 'active' : '' }}" href="{{ route('admin.tasks.index') }}">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        Tasks
      </a>
    </div>
    <div class="nav-section">
      <div class="nav-section-label">Finance</div>
      <a class="nav-item " href="/admin/expenses.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
        Expenses
      </a>
      <a class="nav-item " href="/admin/profit.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
        Profit &amp; Loss
      </a>
    </div>
  </nav>
  <div class="sidebar-footer">
    <a href="/admin/profile.php" style="display:flex;align-items:center;gap:10px;padding:8px;border-radius:6px;text-decoration:none;transition:background .15s" onmouseover="this.style.background='rgba(255,255,255,.06)'" onmouseout="this.style.background='transparent'">
        <div class="avatar av-blue">
            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
        </div>
        <div>
            <div class="name">{{ Auth::user()->name }}</div>
            <div class="role" style="color:#64748b">Administrator</div>
        </div>
        <a class="logout" href="{{ route('admin.logout') }}" title="Logout" onclick="return confirm('Are you sure you want to logout?')">
            &#x2715;
        </a>    </a>
  </div>
</aside>
<div class="main-content">
<div class="topbar">
  <div class="topbar-title">{{--<?= //e($pageTitle ?? 'Dashboard') ?>--}}</div>
  <div class="topbar-right">
    <span class="text-muted" style="font-size:12px">{{ date('D, d M Y') }}></span>
  </div>
</div>
<div class="page-body">

        @if ($errors->any())
            <div style="background:#fee;padding:10px;margin-bottom:10px;border-radius:5px;color:red;">
                <ul style="margin:0;padding-left:15px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

@yield('content')


</div></div></div>
</body>
</html>
