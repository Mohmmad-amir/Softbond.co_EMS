<?php
$pageTitle = 'My Dashboard';
require_once __DIR__ . '/_header.php';
$db = db();
$empId = $_SESSION['employee_id'] ?? 0;
$emp = db()->query("SELECT * FROM employees WHERE id=$empId")->fetch_assoc();

$totalTasks   = $db->query("SELECT COUNT(*) c FROM tasks WHERE assigned_to=$empId")->fetch_assoc()['c'];
$doneTasks    = $db->query("SELECT COUNT(*) c FROM tasks WHERE assigned_to=$empId AND status='done'")->fetch_assoc()['c'];
$pendingTasks = $db->query("SELECT COUNT(*) c FROM tasks WHERE assigned_to=$empId AND status='pending'")->fetch_assoc()['c'];
$monthAtt     = $db->query("SELECT COUNT(*) c FROM attendance WHERE employee_id=$empId AND status='present' AND MONTH(date)=MONTH(CURDATE()) AND YEAR(date)=YEAR(CURDATE())")->fetch_assoc()['c'];
$todayAtt     = $db->query("SELECT * FROM attendance WHERE employee_id=$empId AND date=CURDATE()")->fetch_assoc();
$recentTasks  = $db->query("SELECT t.*, p.name as project_name FROM tasks t JOIN projects p ON p.id=t.project_id WHERE t.assigned_to=$empId ORDER BY t.created_at DESC LIMIT 5");
$salReq       = $db->query("SELECT * FROM salary_requests WHERE employee_id=$empId ORDER BY requested_at DESC LIMIT 3");
?>
<div class="page-header">
  <div><h2>My Dashboard</h2><p>Welcome back, <?= e($emp['name'] ?? $_SESSION['name']) ?>!</p></div>
</div>

<!-- Today's Attendance Card -->
<div class="card" style="background:linear-gradient(135deg,#1e40af,#2563eb);border:none;margin-bottom:24px">
  <div class="card-body" style="display:flex;align-items:center;justify-content:space-between">
    <div>
      <div style="color:rgba(255,255,255,.7);font-size:12px;text-transform:uppercase;letter-spacing:.06em">Today's Status</div>
      <div style="color:#fff;font-size:22px;font-weight:700;margin:4px 0">
        <?php if ($todayAtt): ?>
          <?= ucfirst(str_replace('_',' ',$todayAtt['status'])) ?>
        <?php else: ?>Not Marked<?php endif; ?>
      </div>
      <?php if ($todayAtt && $todayAtt['check_in']): ?>
        <div style="color:rgba(255,255,255,.7);font-size:13px">Check-in: <?= date('h:i A', strtotime($todayAtt['check_in'])) ?></div>
      <?php endif; ?>
    </div>
    <div style="text-align:right">
      <div style="color:rgba(255,255,255,.7);font-size:12px">Days Present This Month</div>
      <div style="color:#fff;font-size:36px;font-weight:800"><?= $monthAtt ?></div>
    </div>
  </div>
</div>

<div class="metrics-grid">
  <div class="metric-card">
    <div class="metric-label">Total Tasks</div>
    <div class="metric-val"><?= $totalTasks ?></div>
    <div class="metric-sub">Assigned to me</div>
  </div>
  <div class="metric-card">
    <div class="metric-label">Completed</div>
    <div class="metric-val" style="color:var(--success)"><?= $doneTasks ?></div>
  </div>
  <div class="metric-card">
    <div class="metric-label">Pending</div>
    <div class="metric-val" style="color:var(--warning)"><?= $pendingTasks ?></div>
  </div>
  <div class="metric-card">
    <div class="metric-label">My Salary</div>
    <div class="metric-val">৳<?= number_format($emp['salary'] ?? 0) ?></div>
    <div class="metric-sub">Per month</div>
  </div>
</div>

<div class="grid-2">
  <div class="card">
    <div class="card-header"><span class="card-title">My Recent Tasks</span><a href="<?= BASE_URL ?>/employee/tasks.php" class="btn btn-outline btn-sm">View All</a></div>
    <div style="padding:0 4px">
    <?php $hasTasks = false; while ($t = $recentTasks->fetch_assoc()): $hasTasks = true;
      $sb=['pending'=>'gray','in_progress'=>'info','done'=>'success'];
      $pb=['low'=>'gray','medium'=>'warning','high'=>'danger'];
    ?>
    <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border)">
      <div style="flex:1">
        <div class="fw-600" style="font-size:13px"><?= e($t['title']) ?></div>
        <div class="text-muted" style="font-size:11px"><?= e($t['project_name']) ?> · Due <?= $t['due_date'] ? date('d M', strtotime($t['due_date'])) : '—' ?></div>
      </div>
      <span class="badge badge-<?= $pb[$t['priority']]??'gray' ?>"><?= ucfirst($t['priority']) ?></span>
      <span class="badge badge-<?= $sb[$t['status']]??'gray' ?>"><?= ucfirst(str_replace('_',' ',$t['status'])) ?></span>
    </div>
    <?php endwhile; ?>
    <?php if (!$hasTasks): ?><div class="empty-state" style="padding:24px">No tasks assigned yet.</div><?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">My Salary Requests</span><a href="<?= BASE_URL ?>/employee/salary.php" class="btn btn-outline btn-sm">Manage</a></div>
    <div style="padding:0 4px">
    <?php $hasSal = false; while ($r = $salReq->fetch_assoc()): $hasSal = true;
      $sb=['pending'=>'warning','approved'=>'success','denied'=>'danger'];
    ?>
    <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border)">
      <div style="flex:1">
        <div class="fw-600" style="font-size:13px">৳<?= number_format($r['amount']) ?> — <?= e($r['month']) ?></div>
        <div class="text-muted" style="font-size:11px"><?= date('d M Y', strtotime($r['requested_at'])) ?></div>
      </div>
      <span class="badge badge-<?= $sb[$r['status']] ?>"><?= ucfirst($r['status']) ?></span>
    </div>
    <?php endwhile; ?>
    <?php if (!$hasSal): ?><div class="empty-state" style="padding:24px">No salary requests yet.</div><?php endif; ?>
    </div>
  </div>
</div>
<?php require '_footer.php'; ?>
