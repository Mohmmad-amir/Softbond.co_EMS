<div class="page-header">
  <div><h2>Attendance</h2><p>Track daily attendance for all employees</p></div>
</div>

<div class="metrics-grid">
  <div class="metric-card"><div class="metric-label">Total Employees</div><div class="metric-val"></div></div>
  <div class="metric-card"><div class="metric-label">Present</div><div class="metric-val" style="color:var(--success)"></div></div>
  <div class="metric-card"><div class="metric-label">On Leave</div><div class="metric-val" style="color:var(--warning)"></div></div>
  <div class="metric-card"><div class="metric-label">Absent</div><div class="metric-val" style="color:var(--danger)"></div></div>
</div>

<div class="card">
  <div class="card-header">
    <span class="card-title">Attendance Log</span>
    <form method="GET" style="display:flex;gap:8px;align-items:center">
      <input type="date" name="date" class="form-control" style="width:auto" value="">
      <button type="submit" class="btn btn-outline btn-sm">Load</button>
    </form>
  </div>
  <form method="POST">
    <input type="hidden" name="att_date" value="">
    <input type="hidden" name="save_attendance" value="1">
    <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>Employee</th><th>Department</th><th>Status</th><th>Check In</th><th>Check Out</th></tr></thead>
      <tbody>

      <tr>
        <td><div class="avatar-row"><div class="avatar av-blue"></div><div class="info"><div class="name"></div><div class="sub"></div></div></div></td>
        <td></td>
        <td>
          <select name="att[<?= $emp['id'] ?>][status]" class="form-control" style="width:130px">
            <option value="present" <?= $st==='present'?'selected':'' ?>>Present</option>
            <option value="absent" <?= $st==='absent'?'selected':'' ?>>Absent</option>
            <option value="on_leave" <?= $st==='on_leave'?'selected':'' ?>>On Leave</option>
            <option value="half_day" <?= $st==='half_day'?'selected':'' ?>>Half Day</option>
          </select>
        </td>
        <td><input type="time" name="att[<?= $emp['id'] ?>][check_in]" class="form-control" style="width:120px" value="<?= e($a['check_in'] ?? '') ?>"></td>
        <td><input type="time" name="att[<?= $emp['id'] ?>][check_out]" class="form-control" style="width:120px" value="<?= e($a['check_out'] ?? '') ?>"></td>
      </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
    </div>
    <div style="padding:16px 20px;border-top:1px solid var(--border)">
      <button type="submit" class="btn btn-primary">Save Attendance</button>
    </div>
  </form>
</div>
<?php require 'footer.blade.php'; ?>
