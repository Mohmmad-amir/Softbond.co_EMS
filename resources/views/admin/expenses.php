<?php
$pageTitle = 'Expenses';
require_once __DIR__ . '/header.blade.php';
$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $pid = !empty($_POST['project_id']) ? (int)$_POST['project_id'] : 'NULL';
    $ds  = trim($_POST['description']); $cat = $_POST['category'];
    $amt = (float)$_POST['amount']; $dt = $_POST['date']; $nt = trim($_POST['note']);
    if ($_POST['action'] === 'add') {
        $db->query("INSERT INTO expenses (project_id,description,category,amount,date,note) VALUES ($pid,'".db()->real_escape_string($ds)."','$cat',$amt,'$dt','".db()->real_escape_string($nt)."')");
        flash('Expense added.');
    } elseif ($_POST['action'] === 'edit' && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        $db->query("UPDATE expenses SET project_id=$pid,description='".db()->real_escape_string($ds)."',category='$cat',amount=$amt,date='$dt',note='".db()->real_escape_string($nt)."' WHERE id=$id");
        flash('Expense updated.');
    }
    header('Location: ' . BASE_URL . '/admin/expenses.php'); exit;
}
if (isset($_GET['delete'])) {
    $db->query("DELETE FROM expenses WHERE id=".(int)$_GET['delete']);
    flash('Expense deleted.', 'warning');
    header('Location: ' . BASE_URL . '/admin/expenses.php'); exit;
}

$editExp = null;
if (isset($_GET['edit'])) $editExp = $db->query("SELECT * FROM expenses WHERE id=".(int)$_GET['edit'])->fetch_assoc();

$month = $_GET['month'] ?? date('Y-m');
[$y,$m] = explode('-', $month);
$expenses  = $db->query("SELECT e.*,p.name as project_name FROM expenses e LEFT JOIN projects p ON p.id=e.project_id WHERE YEAR(e.date)=$y AND MONTH(e.date)=$m ORDER BY e.date DESC");
$totalMonth = $db->query("SELECT COALESCE(SUM(amount),0) s FROM expenses WHERE YEAR(date)=$y AND MONTH(date)=$m")->fetch_assoc()['s'];
$byCat = $db->query("SELECT category, SUM(amount) s FROM expenses WHERE YEAR(date)=$y AND MONTH(date)=$m GROUP BY category ORDER BY s DESC");
$byProj = $db->query("SELECT p.name, SUM(e.amount) s FROM expenses e JOIN projects p ON p.id=e.project_id WHERE YEAR(e.date)=$y AND MONTH(e.date)=$m GROUP BY e.project_id ORDER BY s DESC LIMIT 6");
$catData = []; while ($r = $byCat->fetch_assoc()) $catData[] = $r;
$projData = []; while ($r = $byProj->fetch_assoc()) $projData[] = $r;
$projects  = $db->query("SELECT id,name FROM projects ORDER BY name");
$categories = ['Salary','Software','Hosting','Operations','Tools','Marketing','Other'];
?>
<div class="page-header">
  <div><h2>Expenses</h2><p>Track all business costs by project and category</p></div>
  <button class="btn btn-primary" onclick="document.getElementById('addModal').classList.add('open')">+ Add Expense</button>
</div>

<div class="metrics-grid" style="grid-template-columns:repeat(3,1fr)">
  <div class="metric-card"><div class="metric-label">Total This Month</div><div class="metric-val">৳<?= number_format($totalMonth) ?></div></div>
  <div class="metric-card"><div class="metric-label">Top Category</div><div class="metric-val" style="font-size:18px"><?= $catData[0]['category'] ?? '—' ?></div><div class="metric-sub">৳<?= number_format($catData[0]['s'] ?? 0) ?></div></div>
  <div class="metric-card"><div class="metric-label">Month</div><div class="metric-val" style="font-size:18px"><?= date('M Y', strtotime("$y-$m-01")) ?></div></div>
</div>

<div class="grid-2">
  <div class="card">
    <div class="card-header"><span class="card-title">Expenses by Category</span></div>
    <div class="card-body"><canvas id="catChart" height="200" role="img" aria-label="Expenses by category">Category breakdown.</canvas></div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">Expenses by Project</span></div>
    <div class="card-body"><canvas id="projChart" height="200" role="img" aria-label="Expenses by project">Project breakdown.</canvas></div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <span class="card-title">All Expenses</span>
    <form method="GET" style="display:flex;gap:8px;align-items:center">
      <input type="month" name="month" class="form-control" style="width:auto" value="<?= e($month) ?>">
      <button type="submit" class="btn btn-outline btn-sm">Filter</button>
    </form>
  </div>
  <div class="table-wrap">
  <table class="data-table">
    <thead><tr><th>Date</th><th>Description</th><th>Project</th><th>Category</th><th>Amount</th><th>Note</th><th>Action</th></tr></thead>
    <tbody>
    <?php while ($e = $expenses->fetch_assoc()): ?>
    <tr>
      <td><?= date('d M', strtotime($e['date'])) ?></td>
      <td class="fw-600"><?= e($e['description']) ?></td>
      <td><?= $e['project_name'] ? e($e['project_name']) : '<span class="text-muted">—</span>' ?></td>
      <td><span class="badge badge-gray"><?= e($e['category']) ?></span></td>
      <td class="fw-600">৳<?= number_format($e['amount']) ?></td>
      <td class="text-muted"><?= e($e['note']) ?: '—' ?></td>
      <td><div style="display:flex;gap:4px"><a href="?edit=<?= $e['id'] ?>" class="btn btn-outline btn-xs">Edit</a><a href="?delete=<?= $e['id'] ?>" class="btn btn-danger btn-xs" onclick="return confirm('Delete?')">Del</a></div></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  </div>
</div>

<!-- ADD MODAL -->
<div class="modal-overlay" id="addModal">
<div class="modal">
  <div class="modal-header"><h3>Add Expense</h3><button class="modal-close" onclick="document.getElementById('addModal').classList.remove('open')">×</button></div>
  <form method="POST" class="modal-body">
    <input type="hidden" name="action" value="add">
    <div class="form-group"><label class="form-label">Description *</label><input name="description" class="form-control" required></div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Category *</label><select name="category" class="form-control"><?php foreach ($categories as $c): ?><option><?= $c ?></option><?php endforeach; ?></select></div>
      <div class="form-group"><label class="form-label">Amount (৳) *</label><input type="number" name="amount" class="form-control" step="0.01" required></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Date *</label><input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
      <div class="form-group"><label class="form-label">Project (optional)</label><select name="project_id" class="form-control"><option value="">— General —</option><?php $projects->data_seek(0); while ($p = $projects->fetch_assoc()): ?><option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option><?php endwhile; ?></select></div>
    </div>
    <div class="form-group"><label class="form-label">Note</label><textarea name="note" class="form-control"></textarea></div>
    <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="document.getElementById('addModal').classList.remove('open')">Cancel</button><button type="submit" class="btn btn-primary">Add Expense</button></div>
  </form>
</div></div>

<?php if ($editExp): ?>
<div class="modal-overlay open">
<div class="modal">
  <div class="modal-header"><h3>Edit Expense</h3><a class="modal-close" href="<?= BASE_URL ?>/admin/expenses.php">×</a></div>
  <form method="POST" class="modal-body">
    <input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= $editExp['id'] ?>">
    <div class="form-group"><label class="form-label">Description</label><input name="description" class="form-control" value="<?= e($editExp['description']) ?>" required></div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Category</label><select name="category" class="form-control"><?php foreach ($categories as $c): ?><option <?= $editExp['category']===$c?'selected':'' ?>><?= $c ?></option><?php endforeach; ?></select></div>
      <div class="form-group"><label class="form-label">Amount (৳)</label><input type="number" name="amount" class="form-control" step="0.01" value="<?= $editExp['amount'] ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Date</label><input type="date" name="date" class="form-control" value="<?= $editExp['date'] ?>"></div>
      <div class="form-group"><label class="form-label">Project</label><select name="project_id" class="form-control"><option value="">— General —</option><?php $projects->data_seek(0); while ($p = $projects->fetch_assoc()): ?><option value="<?= $p['id'] ?>" <?= $editExp['project_id']==$p['id']?'selected':'' ?>><?= e($p['name']) ?></option><?php endwhile; ?></select></div>
    </div>
    <div class="form-group"><label class="form-label">Note</label><textarea name="note" class="form-control"><?= e($editExp['note']) ?></textarea></div>
    <div class="modal-footer"><a class="btn btn-outline" href="<?= BASE_URL ?>/admin/expenses.php">Cancel</a><button type="submit" class="btn btn-primary">Save</button></div>
  </form>
</div></div>
<?php endif; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
const catData = <?= json_encode(array_map(fn($r)=>['label'=>$r['category'],'val'=>(float)$r['s']],$catData)) ?>;
const projData = <?= json_encode(array_map(fn($r)=>['label'=>$r['name'],'val'=>(float)$r['s']],$projData)) ?>;
const colors = ['#2563eb','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899'];
if (catData.length) {
  new Chart(document.getElementById('catChart'),{type:'doughnut',data:{labels:catData.map(d=>d.label),datasets:[{data:catData.map(d=>d.val),backgroundColor:colors,borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{boxWidth:10,font:{size:11}}}}}});
}
if (projData.length) {
  new Chart(document.getElementById('projChart'),{type:'bar',data:{labels:projData.map(d=>d.label),datasets:[{label:'Expenses',data:projData.map(d=>d.val),backgroundColor:'#2563eb',borderRadius:4}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>'৳'+v}},x:{ticks:{font:{size:10}}}}}});
}
</script>
<?php require 'footer.blade.php'; ?>
