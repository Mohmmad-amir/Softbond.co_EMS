<?php
$pageTitle = 'Salary';
require_once __DIR__ . '/_header.php';
$db = db();
$empId = $_SESSION['employee_id'] ?? 0;
$emp = $db->query("SELECT * FROM employees WHERE id=$empId")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_salary'])) {
    $amount = (float)$_POST['amount'];
    $month  = trim($_POST['month']);
    $note   = trim($_POST['note']);
    // Check if already requested this month
    $existing = $db->query("SELECT id FROM salary_requests WHERE employee_id=$empId AND month='".db()->real_escape_string($month)."' AND status='pending'")->fetch_assoc();
    if ($existing) {
        flash('You already have a pending request for this month.', 'warning');
    } else {
        $db->query("INSERT INTO salary_requests (employee_id,amount,month,note) VALUES ($empId,$amount,'".db()->real_escape_string($month)."','".db()->real_escape_string($note)."')");
        flash('Salary request submitted! Waiting for admin approval.');
    }
    header('Location: ' . BASE_URL . '/employee/salary.blade.php'); exit;
}

$requests = $db->query("SELECT * FROM salary_requests WHERE employee_id=$empId ORDER BY requested_at DESC");
$approved = $db->query("SELECT COALESCE(SUM(amount),0) s FROM salary_requests WHERE employee_id=$empId AND status='approved'")->fetch_assoc()['s'];
$pending  = $db->query("SELECT COUNT(*) c FROM salary_requests WHERE employee_id=$empId AND status='pending'")->fetch_assoc()['c'];
?>
<div class="page-header">
  <div><h2>Salary</h2><p>Request your salary and view payment history</p></div>
</div>

<div class="grid-2" style="margin-bottom:24px">
  <div class="card" style="background:linear-gradient(135deg,#065f46,#10b981);border:none">
    <div class="card-body">
      <div style="color:rgba(255,255,255,.7);font-size:12px;text-transform:uppercase;letter-spacing:.06em">My Monthly Salary</div>
      <div style="color:#fff;font-size:32px;font-weight:800;margin:8px 0">৳<?= number_format($emp['salary'] ?? 0) ?></div>
      <div style="color:rgba(255,255,255,.7);font-size:13px"><?= e($emp['designation']) ?> · <?= e($emp['department']) ?></div>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="metrics-grid" style="grid-template-columns:1fr 1fr;gap:12px;margin-bottom:0">
        <div class="metric-card"><div class="metric-label">Total Approved</div><div class="metric-val" style="font-size:20px">৳<?= number_format($approved) ?></div></div>
        <div class="metric-card"><div class="metric-label">Pending</div><div class="metric-val" style="font-size:20px;color:var(--warning)"><?= $pending ?></div><div class="metric-sub">request(s)</div></div>
      </div>
    </div>
  </div>
</div>

<!-- Request Form -->
<div class="card" style="margin-bottom:24px">
  <div class="card-header"><span class="card-title">Submit Salary Request</span></div>
  <form method="POST" class="card-body">
    <input type="hidden" name="request_salary" value="1">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Amount (৳)</label>
        <input type="number" name="amount" class="form-control" value="<?= $emp['salary'] ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">For Month</label>
        <input type="month" name="month" class="form-control" value="<?= date('Y-m') ?>" required>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Note (optional)</label>
      <textarea name="note" class="form-control" placeholder="Any additional note for admin..."></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Submit Request</button>
  </form>
</div>

<!-- History -->
<div class="card">
  <div class="card-header"><span class="card-title">Request History</span></div>
  <div class="table-wrap">
  <table class="data-table">
    <thead><tr><th>Month</th><th>Amount</th><th>Status</th><th>Requested</th><th>Actioned</th><th>Note</th></tr></thead>
    <tbody>
    <?php $hasReq = false; while ($r = $requests->fetch_assoc()): $hasReq = true;
      $sb=['pending'=>'warning','approved'=>'success','denied'=>'danger'];
    ?>
    <tr>
      <td class="fw-600"><?= e($r['month']) ?></td>
      <td>৳<?= number_format($r['amount']) ?></td>
      <td><span class="badge badge-<?= $sb[$r['status']] ?>"><?= ucfirst($r['status']) ?></span></td>
      <td><?= date('d M Y', strtotime($r['requested_at'])) ?></td>
      <td><?= $r['actioned_at'] ? date('d M Y', strtotime($r['actioned_at'])) : '—' ?></td>
      <td class="text-muted"><?= e($r['note']) ?: '—' ?></td>
    </tr>
    <?php endwhile; ?>
    <?php if (!$hasReq): ?><tr><td colspan="6"><div class="empty-state">No salary requests yet.</div></td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php require '_footer.php'; ?>
