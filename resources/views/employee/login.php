<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
if (isLoggedIn() && !isAdmin()) { header('Location: ' . BASE_URL . '/employee/dashboard.blade.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = login(trim($_POST['email']), $_POST['password']);
    if ($role === 'employee') { header('Location: ' . BASE_URL . '/employee/dashboard.blade.php'); exit; }
    elseif ($role === 'admin') { header('Location: ' . BASE_URL . '/admin/dashboard.blade.php'); exit; }
    else $error = 'Invalid email or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>SoftCo Employee Login</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="login-page">
  <div class="login-box">
    <div class="login-logo">
      <h1>SoftCo</h1>
      <p>Employee Portal</p>
    </div>
    <h2>Employee Login</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="your@email.com" required value="<?= e($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-primary w-full" style="justify-content:center;margin-top:8px">Login</button>
    </form>
    <p style="text-align:center;margin-top:16px;font-size:12px;color:#64748b">
      Admin? <a href="<?= BASE_URL ?>/admin/login.php">Admin Panel</a>
    </p>
  </div>
</div>
</body>
</html>
