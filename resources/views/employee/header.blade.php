<?php
require_once __DIR__ . '/../includes/auth.php';
requireEmployee();
$current = basename($_SERVER['PHP_SELF'], '.php');
$empId = $_SESSION['employee_id'] ?? 0;
$emp = db()->query("SELECT * FROM employees WHERE id=$empId")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SoftCo — <?= e($pageTitle ?? 'My Panel') ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="layout">
<aside class="sidebar">
  <div class="sidebar-logo">
    <h1>SoftCo</h1>
    <p>Employee Portal</p>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">
      <div class="nav-section-label">My Panel</div>
      <a class="nav-item <?= $current==='dashboard'?'active':'' ?>" href="<?= BASE_URL ?>/employee/dashboard.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        Dashboard
      </a>
      <a class="nav-item <?= $current==='profile'?'active':'' ?>" href="<?= BASE_URL ?>/employee/profile.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        My Profile
      </a>
      <a class="nav-item <?= $current==='tasks'?'active':'' ?>" href="<?= BASE_URL ?>/employee/tasks.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        My Tasks
      </a>
      <a class="nav-item <?= $current==='attendance'?'active':'' ?>" href="<?= BASE_URL ?>/employee/attendance.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        My Attendance
      </a>
      <a class="nav-item <?= $current==='salary'?'active':'' ?>" href="<?= BASE_URL ?>/employee/salary.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
        Salary
      </a>
    </div>
  </nav>
  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="avatar av-teal"><?= strtoupper(substr($_SESSION['name'],0,2)) ?></div>
      <div><div class="name"><?= e($_SESSION['name']) ?></div><div class="role"><?= e($emp['designation'] ?? 'Employee') ?></div></div>
      <a class="logout" href="<?= BASE_URL ?>/employee/logout.php" title="Logout">&#x2715;</a>
    </div>
  </div>
</aside>
<div class="main-content">
<div class="topbar">
  <div class="topbar-title"><?= e($pageTitle ?? 'My Panel') ?></div>
  <div class="topbar-right">
    <span class="text-muted" style="font-size:12px"><?= date('D, d M Y') ?></span>
  </div>
</div>
<div class="page-body">
<?php
$flash = getFlash();
if ($flash): ?>
  <div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['msg']) ?></div>
<?php endif; ?>

    @yield('content')

</div></div></div>
</body>
</html>
