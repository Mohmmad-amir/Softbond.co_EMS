<?php
$pageTitle = 'Tasks';
require_once __DIR__ . '/header.blade.php';
$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $pid = (int)$_POST['project_id']; $aid = (int)$_POST['assigned_to'];
    $tt  = trim($_POST['title']); $ds = trim($_POST['description']);
    $due = $_POST['due_date']; $pri = $_POST['priority']; $st = $_POST['status'];
    if ($_POST['action'] === 'add') {
        $db->query("INSERT INTO tasks (project_id,assigned_to,title,description,due_date,priority,status) VALUES ($pid,$aid,'".db()->real_escape_string($tt)."','".db()->real_escape_string($ds)."','$due','$pri','$st')");
        flash('Task assigned.');
    } elseif ($_POST['action'] === 'edit' && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        $db->query("UPDATE tasks SET project_id=$pid,assigned_to=$aid,title='".db()->real_escape_string($tt)."',description='".db()->real_escape_string($ds)."',due_date='$due',priority='$pri',status='$st' WHERE id=$id");
        flash('Task updated.');
    }
    header('Location: ' . BASE_URL . '/admin/tasks.php'); exit;
}
if (isset($_GET['delete'])) {
    $db->query("DELETE FROM tasks WHERE id=".(int)$_GET['delete']);
    flash('Task deleted.', 'warning');
    header('Location: ' . BASE_URL . '/admin/tasks.php'); exit;
}
if (isset($_GET['status_update'])) {
    $id = (int)$_GET['id']; $st = db()->real_escape_string($_GET['status_update']);
    $db->query("UPDATE tasks SET status='$st' WHERE id=$id");
    header('Location: ' . BASE_URL . '/admin/tasks.php'); exit;
}

$filterProj = (int)($_GET['project'] ?? 0);
$filterEmp  = (int)($_GET['employee'] ?? 0);
$where = "WHERE 1";
if ($filterProj) $where .= " AND t.project_id=$filterProj";
if ($filterEmp)  $where .= " AND t.assigned_to=$filterEmp";

$tasks = $db->query("SELECT t.*,p.name as project_name,e.name as emp_name FROM tasks t JOIN projects p ON p.id=t.project_id JOIN employees e ON e.id=t.assigned_to $where ORDER BY t.created_at DESC");
$projects  = $db->query("SELECT id,name FROM projects ORDER BY name");
$employees = $db->query("SELECT id,name FROM employees WHERE status='active' ORDER BY name");

$editTask = null;
if (isset($_GET['edit'])) $editTask = $db->query("SELECT * FROM tasks WHERE id=".(int)$_GET['edit'])->fetch_assoc();

$pArr = []; $eArr = [];
$projects->data_seek(0); while ($r = $projects->fetch_assoc()) $pArr[$r['id']] = $r['name'];
$employees->data_seek(0); while ($r = $employees->fetch_assoc()) $eArr[$r['id']] = $r['name'];
?>
<div class="page-header">
  <div><h2>Tasks</h2><p>Assign and manage project tasks</p></div>
  <button class="btn btn-primary" onclick="document.getElementById('addModal').classList.add('open')">+ Assign Task</button>
</div>

<form method="GET" style="display:flex;gap:10px;margin-bottom:20px">
  <select name="project" class="form-control" style="max-width:200px">
    <option value="">All Projects</option>
    <?php foreach ($pArr as $id => $name): ?><option value="<?= $id ?>" <?= $filterProj===$id?'selected':'' ?>><?= e($name) ?></option><?php endforeach; ?>
  </select>
  <select name="employee" class="form-control" style="max-width:200px">
    <option value="">All Employees</option>
    <?php foreach ($eArr as $id => $name): ?><option value="<?= $id ?>" <?= $filterEmp===$id?'selected':'' ?>><?= e($name) ?></option><?php endforeach; ?>
  </select>
  <button type="submit" class="btn btn-outline">Filter</button>
  <a href="<?= BASE_URL ?>/admin/tasks.php" class="btn btn-outline">Reset</a>
</form>

<div class="card">
  <div class="table-wrap">
  <table class="data-table">
    <thead><tr><th>Task</th><th>Project</th><th>Assigned To</th><th>Due Date</th><th>Priority</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php while ($t = $tasks->fetch_assoc()):
      $pb=['low'=>'gray','medium'=>'warning','high'=>'danger'];
      $sb=['pending'=>'gray','in_progress'=>'info','done'=>'success'];
    ?>
    <tr>
      <td class="fw-600"><?= e($t['title']) ?></td>
      <td><?= e($t['project_name']) ?></td>
      <td><?= e($t['emp_name']) ?></td>
      <td><?= $t['due_date'] ? date('d M Y', strtotime($t['due_date'])) : '—' ?></td>
      <td><span class="badge badge-<?= $pb[$t['priority']]??'gray' ?>"><?= ucfirst($t['priority']) ?></span></td>
      <td>
        <select class="form-control" style="width:120px;font-size:12px" onchange="location='?status_update='+this.value+'&id=<?= $t['id'] ?>'">
          <option value="pending" <?= $t['status']==='pending'?'selected':'' ?>>Pending</option>
          <option value="in_progress" <?= $t['status']==='in_progress'?'selected':'' ?>>In Progress</option>
          <option value="done" <?= $t['status']==='done'?'selected':'' ?>>Done</option>
        </select>
      </td>
      <td><div style="display:flex;gap:4px"><a href="?edit=<?= $t['id'] ?>" class="btn btn-outline btn-xs">Edit</a><a href="?delete=<?= $t['id'] ?>" class="btn btn-danger btn-xs" onclick="return confirm('Delete?')">Del</a></div></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  </div>
</div>

<!-- ADD MODAL -->
<div class="modal-overlay" id="addModal">
<div class="modal">
  <div class="modal-header"><h3>Assign New Task</h3><button class="modal-close" onclick="document.getElementById('addModal').classList.remove('open')">×</button></div>
  <form method="POST" class="modal-body">
    <input type="hidden" name="action" value="add">
    <div class="form-group"><label class="form-label">Task Title *</label><input name="title" class="form-control" required></div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Project *</label><select name="project_id" class="form-control" required><option value="">Select Project</option><?php foreach ($pArr as $id => $name): ?><option value="<?= $id ?>"><?= e($name) ?></option><?php endforeach; ?></select></div>
      <div class="form-group"><label class="form-label">Assign To *</label><select name="assigned_to" class="form-control" required><option value="">Select Employee</option><?php foreach ($eArr as $id => $name): ?><option value="<?= $id ?>"><?= e($name) ?></option><?php endforeach; ?></select></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Due Date</label><input type="date" name="due_date" class="form-control"></div>
      <div class="form-group"><label class="form-label">Priority</label><select name="priority" class="form-control"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option></select></div>
    </div>
    <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-control"><option value="pending">Pending</option><option value="in_progress">In Progress</option><option value="done">Done</option></select></div>
    <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control"></textarea></div>
    <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="document.getElementById('addModal').classList.remove('open')">Cancel</button><button type="submit" class="btn btn-primary">Assign Task</button></div>
  </form>
</div></div>

<?php if ($editTask): ?>
<div class="modal-overlay open">
<div class="modal">
  <div class="modal-header"><h3>Edit Task</h3><a class="modal-close" href="<?= BASE_URL ?>/admin/tasks.php">×</a></div>
  <form method="POST" class="modal-body">
    <input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= $editTask['id'] ?>">
    <div class="form-group"><label class="form-label">Title</label><input name="title" class="form-control" value="<?= e($editTask['title']) ?>" required></div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Project</label><select name="project_id" class="form-control"><?php foreach ($pArr as $id => $name): ?><option value="<?= $id ?>" <?= $editTask['project_id']==$id?'selected':'' ?>><?= e($name) ?></option><?php endforeach; ?></select></div>
      <div class="form-group"><label class="form-label">Assigned To</label><select name="assigned_to" class="form-control"><?php foreach ($eArr as $id => $name): ?><option value="<?= $id ?>" <?= $editTask['assigned_to']==$id?'selected':'' ?>><?= e($name) ?></option><?php endforeach; ?></select></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Due Date</label><input type="date" name="due_date" class="form-control" value="<?= $editTask['due_date'] ?>"></div>
      <div class="form-group"><label class="form-label">Priority</label><select name="priority" class="form-control"><option value="low" <?= $editTask['priority']==='low'?'selected':'' ?>>Low</option><option value="medium" <?= $editTask['priority']==='medium'?'selected':'' ?>>Medium</option><option value="high" <?= $editTask['priority']==='high'?'selected':'' ?>>High</option></select></div>
    </div>
    <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-control"><option value="pending" <?= $editTask['status']==='pending'?'selected':'' ?>>Pending</option><option value="in_progress" <?= $editTask['status']==='in_progress'?'selected':'' ?>>In Progress</option><option value="done" <?= $editTask['status']==='done'?'selected':'' ?>>Done</option></select></div>
    <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control"><?= e($editTask['description']) ?></textarea></div>
    <div class="modal-footer"><a class="btn btn-outline" href="<?= BASE_URL ?>/admin/tasks.php">Cancel</a><button type="submit" class="btn btn-primary">Save</button></div>
  </form>
</div></div>
<?php endif; ?>
<?php require 'footer.blade.php'; ?>
