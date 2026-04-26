{{--<?php--}}
{{--////require_once __DIR__ . '/../includes/auth.php';--}}
{{--//startSession();--}}
{{--//if (isLoggedIn() && isAdmin()) { header('Location: ' . BASE_URL . '/admin/dashboard.blade.php'); exit; }--}}
{{--//$error = '';--}}
{{--//if ($_SERVER['REQUEST_METHOD'] === 'POST') {--}}
{{--//    $role = login(trim($_POST['email']), $_POST['password']);--}}
{{--//    if ($role === 'admin') { header('Location: ' . BASE_URL . '/admin/dashboard.blade.php'); exit; }--}}
{{--//    else $error = 'Invalid credentials or not an admin.';--}}
{{--//}--}}
{{--//?><!---->--}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>SoftCo Admin Login</title>
<link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
</head>
<body>
<div class="login-page">
  <div class="login-box">
    <div class="login-logo">
      <h1>SoftCo</h1>
      <p>Business Management System</p>
    </div>
    <h2>Admin Login</h2>
      @if(session('error'))
          <div class="alert alert-danger">
              {{ session('error') }}
          </div>
      @endif
    <form action="{{route('login')}}" method="POST">
        @csrf
        @method('post')
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="admin@softco.com" required value="{{ old('email') }}">
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-primary w-full" style="justify-content:center;margin-top:8px">Login to Admin Panel</button>
    </form>
{{--    <p style="text-align:center;margin-top:16px;font-size:12px;color:#64748b">--}}
{{--      Employee? <a href="<?php //= BASE_URL ?>/employee/login.php">Login here</a>--}}
{{--    </p>--}}
  </div>
</div>
</body>
</html>
