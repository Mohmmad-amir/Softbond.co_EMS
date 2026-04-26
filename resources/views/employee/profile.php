<?php
$pageTitle = 'My Profile';
require_once __DIR__ . '/_header.php';
$db = db();
$empId = $_SESSION['employee_id'] ?? 0;
$emp = $db->query("SELECT * FROM employees WHERE id=$empId")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $phone = trim($_POST['phone']); $address = trim($_POST['address']);
    $db->query("UPDATE employees SET phone='".db()->real_escape_string($phone)."', address='".db()->real_escape_string($address)."' WHERE id=$empId");
    if (!empty($_POST['new_password']) && !empty($_POST['current_password'])) {
        $user = $db->query("SELECT password FROM users WHERE id=".(int)$_SESSION['user_id'])->fetch_assoc();
        if (password_verify($_POST['current_password'], $user['password'])) {
            $hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $db->query("UPDATE users SET password='$hash' WHERE id=".(int)$_SESSION['user_id']);
            flash('Profile and password updated.');
        } else {
            flash('Current password is incorrect.', 'danger');
        }
    } else {
        flash('Profile updated.');
    }
    header('Location: ' . BASE_URL . '/employee/profile.php'); exit;
}

$docs = $db->query("SELECT * FROM employee_documents WHERE employee_id=$empId ORDER BY uploaded_at DESC");
?>
<div class="page-header">
  <div><h2>My Profile</h2><p>View your information and documents</p></div>
</div>

<div class="grid-2">
  <div>
    <div class="card">
      <div class="card-header"><span class="card-title">Personal Information</span></div>
      <div class="card-body">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border)">
          <?php if (!empty($emp['photo'])): ?>
            <img src="<?= BASE_URL ?>/uploads/profiles/<?= e($emp['photo']) ?>" style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:3px solid var(--border);flex-shrink:0">
          <?php else: ?>
            <div class="avatar av-blue" style="width:60px;height:60px;font-size:20px;flex-shrink:0"><?= strtoupper(substr($emp['name'],0,2)) ?></div>
          <?php endif; ?>
          <div>
            <div style="font-size:18px;font-weight:700"><?= e($emp['name']) ?></div>
            <div class="text-muted"><?= e($emp['designation']) ?></div>
            <span class="badge badge-primary mt-4"><?= e($emp['department']) ?></span>
          </div>
        </div>
        <table style="width:100%;font-size:13px">
          <tr style="border-bottom:1px solid var(--border)"><td style="padding:8px 0;color:var(--text-muted);width:40%">Email</td><td style="padding:8px 0;font-weight:500"><?= e($emp['email']) ?></td></tr>
          <tr style="border-bottom:1px solid var(--border)"><td style="padding:8px 0;color:var(--text-muted)">Phone</td><td style="padding:8px 0;font-weight:500"><?= e($emp['phone']) ?: '—' ?></td></tr>
          <tr style="border-bottom:1px solid var(--border)"><td style="padding:8px 0;color:var(--text-muted)">NID</td><td style="padding:8px 0;font-weight:500"><?= e($emp['nid']) ?: '—' ?></td></tr>
          <tr style="border-bottom:1px solid var(--border)"><td style="padding:8px 0;color:var(--text-muted)">Joined</td><td style="padding:8px 0;font-weight:500"><?= $emp['join_date'] ? date('d M Y', strtotime($emp['join_date'])) : '—' ?></td></tr>
          <tr style="border-bottom:1px solid var(--border)"><td style="padding:8px 0;color:var(--text-muted)">Salary</td><td style="padding:8px 0;font-weight:500">৳<?= number_format($emp['salary']) ?></td></tr>
          <tr style="border-bottom:1px solid var(--border)"><td style="padding:8px 0;color:var(--text-muted)">Payment Via</td><td style="padding:8px 0;font-weight:500"><?php
            $pmL=['cash'=>'Cash','bank'=>'Bank Transfer','bkash'=>'bKash','nagad'=>'Nagad','rocket'=>'Rocket'];
            echo $pmL[$emp['payment_method']] ?? 'Cash';
            if ($emp['payment_method']==='bank' && $emp['bank_name']) echo '<br><span style="font-size:11px;color:var(--text-muted)">'.e($emp['bank_name']).' · '.e($emp['bank_account']).'</span>';
            elseif (in_array($emp['payment_method'],['bkash','nagad','rocket']) && $emp['mobile_banking_number']) echo '<br><span style="font-size:11px;color:var(--text-muted)">'.e($emp['mobile_banking_number']).'</span>';
          ?></td></tr>
          <tr><td style="padding:8px 0;color:var(--text-muted)">Address</td><td style="padding:8px 0;font-weight:500"><?= e($emp['address']) ?: '—' ?></td></tr>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><span class="card-title">Update Profile</span></div>
      <form method="POST" class="card-body">
        <input type="hidden" name="update_profile" value="1">
        <div class="form-group"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?= e($emp['phone']) ?>"></div>
        <div class="form-group"><label class="form-label">Address</label><textarea name="address" class="form-control"><?= e($emp['address']) ?></textarea></div>
        <div style="border-top:1px solid var(--border);padding-top:16px;margin-top:8px">
          <div class="form-group"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-control" placeholder="Leave blank to keep"></div>
          <div class="form-group"><label class="form-label">New Password</label><input type="password" name="new_password" class="form-control" placeholder="New password"></div>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">My Documents</span></div>
    <div class="card-body">
    <?php $hasDocs = false; while ($doc = $docs->fetch_assoc()): $hasDocs = true; ?>
    <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border)">
      <div style="flex:1">
        <div class="fw-600" style="font-size:13px"><?= e($doc['doc_name']) ?></div>
        <div class="text-muted" style="font-size:11px"><?= e($doc['doc_type']) ?> · <?= date('d M Y', strtotime($doc['uploaded_at'])) ?></div>
      </div>
      <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/uploads/documents/<?= e($doc['file_path']) ?>" target="_blank">Download</a>
    </div>
    <?php endwhile; ?>
    <?php if (!$hasDocs): ?><div class="empty-state">No documents uploaded yet.<br><small>Ask your admin to upload your documents.</small></div><?php endif; ?>
    </div>
  </div>
</div>
<?php require '_footer.php'; ?>
