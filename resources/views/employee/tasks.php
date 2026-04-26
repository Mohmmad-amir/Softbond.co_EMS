<?php
$pageTitle = 'My Tasks';
require_once __DIR__ . '/_header.php';
$db = db();
$empId = $_SESSION['employee_id'] ?? 0;

// Employee can update status of their own tasks
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id']; $st = db()->real_escape_string($_GET['status']);
    $db->query("UPDATE tasks SET status='$st' WHERE id=$id AND assigned_to=$empId");
    flash('Task status updated.');
    header('Location: ' . BASE_URL . '/employee/tasks.php'); exit;
}

$filter = $_GET['filter'] ?? 'all';
$where = "WHERE t.assigned_to=$empId";
if ($filter === 'pending')     $where .= " AND t.status='pending'";
elseif ($filter === 'active')  $where .= " AND t.status='in_progress'";
elseif ($filter === 'done')    $where .= " AND t.status='done'";

$tasks = $db->query("SELECT t.*, p.name as project_name FROM tasks t JOIN projects p ON p.id=t.project_id $where ORDER BY FIELD(t.status,'in_progress','pending','done'), t.due_date ASC");
$total     = $db->query("SELECT COUNT(*) c FROM tasks WHERE assigned_to=$empId")->fetch_assoc()['c'];
$inprog    = $db->query("SELECT COUNT(*) c FROM tasks WHERE assigned_to=$empId AND status='in_progress'")->fetch_assoc()['c'];
$pending   = $db->query("SELECT COUNT(*) c FROM tasks WHERE assigned_to=$empId AND status='pending'")->fetch_assoc()['c'];
$done      = $db->query("SELECT COUNT(*) c FROM tasks WHERE assigned_to=$empId AND status='done'")->fetch_assoc()['c'];
?>
<div class="page-header">
  <div><h2>My Tasks</h2><p>All tasks assigned to you across projects</p></div>
</div>

<div class="metrics-grid">
  <div class="metric-card"><div class="metric-label">Total</div><div class="metric-val"><?= $total ?></div></div>
  <div class="metric-card"><div class="metric-label">In Progress</div><div class="metric-val" style="color:var(--info)"><?= $inprog ?></div></div>
  <div class="metric-card"><div class="metric-label">Pending</div><div class="metric-val" style="color:var(--warning)"><?= $pending ?></div></div>
  <div class="metric-card"><div class="metric-label">Done</div><div class="metric-val" style="color:var(--success)"><?= $done ?></div></div>
</div>

<div style="display:flex;gap:8px;margin-bottom:16px">
  <?php foreach (['all'=>'All Tasks','pending'=>'Pending','active'=>'In Progress','done'=>'Done'] as $k=>$label): ?>
  <a href="?filter=<?= $k ?>" class="btn <?= $filter===$k?'btn-primary':'btn-outline' ?> btn-sm"><?= $label ?></a>
  <?php endforeach; ?>
</div>

<div class="card">
  <div class="table-wrap">
  <table class="data-table">
    <thead><tr><th>Task</th><th>Project</th><th>Due Date</th><th>Priority</th><th>Status</th><th>Update</th></tr></thead>
    <tbody>
    <?php $hasTasks = false; while ($t = $tasks->fetch_assoc()): $hasTasks = true;
      $pb=['low'=>'gray','medium'=>'warning','high'=>'danger'];
      $sb=['pending'=>'gray','in_progress'=>'info','done'=>'success'];
      $isOverdue = $t['due_date'] && $t['status']!=='done' && strtotime($t['due_date']) < strtotime('today');
    ?>
    <tr>
      <td>
        <div class="fw-600"><?= e($t['title']) ?></div>
        <?php if ($t['description']): ?><div class="text-muted" style="font-size:11px;margin-top:2px"><?= e(substr($t['description'],0,60)) ?>...</div><?php endif; ?>
      </td>
      <td><?= e($t['project_name']) ?></td>
      <td class="<?= $isOverdue?'text-danger fw-600':'' ?>">
        <?= $t['due_date'] ? date('d M Y', strtotime($t['due_date'])) : '—' ?>
        <?php if ($isOverdue): ?><div style="font-size:10px">Overdue!</div><?php endif; ?>
      </td>
      <td><span class="badge badge-<?= $pb[$t['priority']]??'gray' ?>"><?= ucfirst($t['priority']) ?></span></td>
      <td><span class="badge badge-<?= $sb[$t['status']]??'gray' ?>"><?= ucfirst(str_replace('_',' ',$t['status'])) ?></span></td>
      <td>
        <div style="display:flex;gap:4px;flex-wrap:wrap">
          <?php if ($t['status']==='pending'): ?>
            <a href="?status=in_progress&id=<?= $t['id'] ?>" class="btn btn-outline btn-xs">Start</a>
          <?php endif; ?>
          <?php if ($t['status']==='in_progress'): ?>
            <a href="?status=done&id=<?= $t['id'] ?>" class="btn btn-success btn-xs">Mark Done</a>
          <?php endif; ?>
          <?php if ($t['status']==='done'): ?>
            <span class="text-muted" style="font-size:11px">Completed</span>
          <?php endif; ?>
        </div>
      </td>
    </tr>
    <?php endwhile; ?>
    <?php if (!$hasTasks): ?><tr><td colspan="6"><div class="empty-state">No tasks found.</div></td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php require '_footer.php'; ?>
