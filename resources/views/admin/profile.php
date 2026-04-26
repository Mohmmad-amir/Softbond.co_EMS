<?php
$pageTitle = 'My Profile';
require_once __DIR__ . '/header.blade.php';
$db = db();
$uid = (int)$_SESSION['user_id'];

// Ensure admin_profile row exists
$db->query("INSERT IGNORE INTO admin_profile (user_id, company_name) VALUES ($uid, 'SoftCo')");
$profile = $db->query("SELECT u.*, ap.company_name, ap.phone AS c_phone, ap.address FROM users u
    LEFT JOIN admin_profile ap ON ap.user_id=u.id WHERE u.id=$uid")->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $company = trim($_POST['company_name']);
    $phone   = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $db->query("UPDATE users SET name='".$db->real_escape_string($name)."', email='".$db->real_escape_string($email)."' WHERE id=$uid");
    $db->query("UPDATE admin_profile SET company_name='".$db->real_escape_string($company)."', phone='".$db->real_escape_string($phone)."', address='".$db->real_escape_string($address)."' WHERE user_id=$uid");
    $_SESSION['name'] = $name;

    // Password change
    if (!empty($_POST['new_password'])) {
        if (!empty($_POST['current_password'])) {
            $user = $db->query("SELECT password FROM users WHERE id=$uid")->fetch_assoc();
            if (password_verify($_POST['current_password'], $user['password'])) {
                if ($_POST['new_password'] === $_POST['confirm_password']) {
                    $hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                    $db->query("UPDATE users SET password='$hash' WHERE id=$uid");
                    flash('Profile and password updated successfully.');
                } else {
                    flash('New passwords do not match.', 'danger');
                    header('Location: ' . BASE_URL . '/admin/profile.php'); exit;
                }
            } else {
                flash('Current password is incorrect.', 'danger');
                header('Location: ' . BASE_URL . '/admin/profile.php'); exit;
            }
        } else {
            flash('Enter current password to change it.', 'warning');
            header('Location: ' . BASE_URL . '/admin/profile.php'); exit;
        }
    } else {
        flash('Profile updated successfully.');
    }
    header('Location: ' . BASE_URL . '/admin/profile.php'); exit;
}
?>
<div class="page-header">
  <div><h2>My Profile</h2><p>Update your admin account details and password</p></div>
</div>

<div class="grid-2">
  <!-- Profile Form -->
  <div class="card">
    <div class="card-header"><span class="card-title">Account Details</span></div>
    <form method="POST" class="card-body">
      <input type="hidden" name="update_profile" value="1">

      <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border)">
        <div class="avatar av-blue" style="width:60px;height:60px;font-size:22px;flex-shrink:0">
          <?= strtoupper(substr($profile['name'],0,2)) ?>
        </div>
        <div>
          <div style="font-size:17px;font-weight:700"><?= e($profile['name']) ?></div>
          <div class="text-muted"><?= e($profile['email']) ?></div>
          <span class="badge badge-primary mt-4">Administrator</span>
        </div>
      </div>

      <p style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px">Personal Info</p>
      <div class="form-group"><label class="form-label">Full Name</label>
        <input name="name" class="form-control" value="<?= e($profile['name']) ?>" required>
      </div>
      <div class="form-group"><label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= e($profile['email']) ?>" required>
      </div>

      <p style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin:20px 0 12px;border-top:1px solid var(--border);padding-top:16px">Company Info</p>
      <div class="form-group"><label class="form-label">Company Name</label>
        <input name="company_name" class="form-control" value="<?= e($profile['company_name']) ?>">
      </div>
      <div class="form-group"><label class="form-label">Contact Phone</label>
        <input name="phone" class="form-control" value="<?= e($profile['c_phone']) ?>">
      </div>
      <div class="form-group"><label class="form-label">Address</label>
        <textarea name="address" class="form-control"><?= e($profile['address']) ?></textarea>
      </div>

      <button type="submit" class="btn btn-primary">Save Profile</button>
    </form>
  </div>

  <!-- Password Change -->
  <div class="card">
    <div class="card-header"><span class="card-title">Change Password</span></div>
    <form method="POST" class="card-body">
      <input type="hidden" name="update_profile" value="1">
      <!-- Keep profile fields as hidden so they don't get blanked -->
      <input type="hidden" name="name" value="<?= e($profile['name']) ?>">
      <input type="hidden" name="email" value="<?= e($profile['email']) ?>">
      <input type="hidden" name="company_name" value="<?= e($profile['company_name']) ?>">
      <input type="hidden" name="phone" value="<?= e($profile['c_phone']) ?>">
      <input type="hidden" name="address" value="<?= e($profile['address']) ?>">

      <div style="background:var(--warning-light);border:1px solid #fde68a;border-radius:var(--radius);padding:12px 14px;margin-bottom:20px;font-size:13px;color:var(--warning)">
        Choose a strong password. Minimum 8 characters recommended.
      </div>
      <div class="form-group">
        <label class="form-label">Current Password *</label>
        <input type="password" name="current_password" class="form-control" placeholder="Your current password">
      </div>
      <div class="form-group">
        <label class="form-label">New Password *</label>
        <input type="password" name="new_password" id="new_pass" class="form-control" placeholder="New password" oninput="checkStrength(this.value)">
        <div id="strength_bar" style="height:4px;border-radius:2px;margin-top:6px;background:#e2e8f0;overflow:hidden">
          <div id="strength_fill" style="height:100%;width:0;border-radius:2px;transition:all .3s"></div>
        </div>
        <div id="strength_label" style="font-size:11px;color:var(--text-muted);margin-top:4px"></div>
      </div>
      <div class="form-group">
        <label class="form-label">Confirm New Password *</label>
        <input type="password" name="confirm_password" class="form-control" placeholder="Repeat new password">
      </div>
      <button type="submit" class="btn btn-primary">Update Password</button>
    </form>

    <!-- Account Info Card -->
    <div style="margin-top:20px;padding:20px;border-top:1px solid var(--border)">
      <p style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px">Account Info</p>
      <table style="width:100%;font-size:13px">
        <tr style="border-bottom:1px solid var(--border)"><td style="padding:7px 0;color:var(--text-muted)">Role</td><td style="padding:7px 0;font-weight:500">Administrator</td></tr>
        <tr style="border-bottom:1px solid var(--border)"><td style="padding:7px 0;color:var(--text-muted)">Account Created</td><td style="padding:7px 0;font-weight:500"><?= date('d M Y', strtotime($profile['created_at'])) ?></td></tr>
        <tr><td style="padding:7px 0;color:var(--text-muted)">Last Login</td><td style="padding:7px 0;font-weight:500">Today</td></tr>
      </table>
    </div>
  </div>
</div>

<script>
function checkStrength(pw) {
    let score = 0;
    if (pw.length >= 8) score++;
    if (/[A-Z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;
    const colors = ['#ef4444','#f59e0b','#10b981','#2563eb'];
    const labels = ['Weak','Fair','Good','Strong'];
    const fill = document.getElementById('strength_fill');
    const label = document.getElementById('strength_label');
    if (pw.length === 0) { fill.style.width='0'; label.textContent=''; return; }
    fill.style.width = ((score)/4*100)+'%';
    fill.style.background = colors[score-1] || colors[0];
    label.textContent = labels[score-1] || 'Weak';
    label.style.color = colors[score-1] || colors[0];
}
</script>
<?php require 'footer.blade.php'; ?>
