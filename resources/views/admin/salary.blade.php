<?php
$pageTitle = 'Salary Requests';
require_once __DIR__ . '/header.blade.php';
$db = db();

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'] === 'approve' ? 'approved' : 'denied';
    $db->query("UPDATE salary_requests SET status='$action', actioned_at=NOW() WHERE id=$id");
    flash('Request '.($action === 'approved' ? 'approved' : 'denied').'.');
    header('Location: ' . BASE_URL . '/admin/salary.php'); exit;
}

$pending  = $db->query("SELECT sr.*,e.name,e.designation,e.department FROM salary_requests sr JOIN employees e ON e.id=sr.employee_id WHERE sr.status='pending' ORDER BY sr.requested_at DESC");
$history  = $db->query("SELECT sr.*,e.name,e.designation FROM salary_requests sr JOIN employees e ON e.id=sr.employee_id WHERE sr.status!='pending' ORDER BY sr.actioned_at DESC LIMIT 30");
$pendingCount = $db->query("SELECT COUNT(*) c FROM salary_requests WHERE status='pending'")->fetch_assoc()['c'];
?>
<div class="page-header">
  <div><h2>Salary Requests</h2><p>Approve or deny employee salary disbursements</p></div>
  <?php if ($pendingCount): ?><span class="badge badge-danger" style="font-size:13px"><?= $pendingCount ?> pending</span><?php endif; ?>
</div>

<div class="card">
  <div class="card-header"><span class="card-title">Pending Requests</span></div>
  <div class="table-wrap">
  <table class="data-table">
    <thead><tr><th>Employee</th><th>Department</th><th>Amount</th><th>Month</th><th>Requested</th><th>Note</th><th>Action</th></tr></thead>
    <tbody>
    <?php $hasPending = false; while ($r = $pending->fetch_assoc()): $hasPending = true; ?>
    <tr>
      <td><div class="avatar-row"><div class="avatar av-blue"><?= strtoupper(substr($r['name'],0,2)) ?></div><div class="info"><div class="name"><?= e($r['name']) ?></div><div class="sub"><?= e($r['designation']) ?></div></div></div></td>
      <td><span class="badge badge-primary"><?= e($r['department']) ?></span></td>
      <td class="fw-600">৳<?= number_format($r['amount']) ?></td>
      <td><?= e($r['month']) ?></td>
      <td><?= date('d M Y', strtotime($r['requested_at'])) ?></td>
      <td class="text-muted"><?= e($r['note']) ?: '—' ?></td>
      <td><div style="display:flex;gap:4px">
        <a href="?action=approve&id=<?= $r['id'] ?>" class="btn btn-success btn-sm">Approve</a>
        <a href="?action=deny&id=<?= $r['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Deny this request?')">Deny</a>
      </div></td>
    </tr>
    <?php endwhile; ?>
    <?php if (!$hasPending): ?><tr><td colspan="7"><div class="empty-state">No pending salary requests</div></td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</div>

<div class="card">
  <div class="card-header"><span class="card-title">History</span></div>
  <div class="table-wrap">
  <table class="data-table">
    <thead><tr><th>Employee</th><th>Amount</th><th>Month</th><th>Status</th><th>Actioned</th></tr></thead>
    <tbody>
    <?php while ($r = $history->fetch_assoc()): ?>
    <tr>
      <td><div class="avatar-row"><div class="avatar av-teal"><?= strtoupper(substr($r['name'],0,2)) ?></div><div class="info"><div class="name"><?= e($r['name']) ?></div><div class="sub"><?= e($r['designation']) ?></div></div></div></td>
      <td>৳<?= number_format($r['amount']) ?></td>
      <td><?= e($r['month']) ?></td>
      <td><?= $r['status']==='approved' ? '<span class="badge badge-success">Approved</span>' : '<span class="badge badge-danger">Denied</span>' ?></td>
      <td><?= $r['actioned_at'] ? date('d M Y', strtotime($r['actioned_at'])) : '—' ?></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  </div>
</div>
<?php require 'footer.blade.php'; ?>
