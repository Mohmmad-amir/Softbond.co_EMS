@extends('admin/header')
@section('content')

{{--  Error Messages --}}
@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

{{--  Page Header --}}
<div class="page-header">
    <div>
        <h2>My Profile</h2>
        <p>Update your admin account details and password</p>
    </div>
</div>

<div class="grid-2">

    {{--  Profile Form --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Account Details</span>
        </div>
        <form method="POST" action="{{ route('admin.profile.update') }}" class="card-body">
            @csrf

            {{--  Avatar --}}
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border)">
                <div class="avatar av-blue" style="width:60px;height:60px;font-size:22px;flex-shrink:0">
                    {{ strtoupper(substr($profile->name, 0, 2)) }}
                </div>
                <div>
                    <div style="font-size:17px;font-weight:700">{{ $profile->name }}</div>
                    <div class="text-muted">{{ $profile->email }}</div>
                    <span class="badge badge-primary mt-4">Administrator</span>
                </div>
            </div>

            {{--  Personal Info --}}
            <p style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px">
                Personal Info
            </p>

            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input name="name" class="form-control"
                       value="{{ old('name', $profile->name) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email', $profile->email) }}" required>
            </div>

            {{--  Company Info --}}
            <p style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin:20px 0 12px;border-top:1px solid var(--border);padding-top:16px">
                Company Info
            </p>

            <div class="form-group">
                <label class="form-label">Company Name</label>
                <input name="company_name" class="form-control"
                       value="{{ old('company_name', $profile->company_name) }}">
            </div>

            <div class="form-group">
                <label class="form-label">Contact Phone</label>
                <input name="phone" class="form-control"
                       value="{{ old('phone', $profile->phone) }}">
            </div>

            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control">{{ old('address', $profile->address) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save Profile</button>
        </form>
    </div>

    {{--  Password Form --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Change Password</span>
        </div>
        <form method="POST" action="{{ route('admin.profile.password') }}" class="card-body">
            @csrf

            <div style="background:var(--warning-light);border:1px solid #fde68a;border-radius:var(--radius);padding:12px 14px;margin-bottom:20px;font-size:13px;color:var(--warning)">
                Choose a strong password. Minimum 8 characters recommended.
            </div>

            <div class="form-group">
                <label class="form-label">Current Password *</label>
                <input type="password" name="current_password" class="form-control"
                       placeholder="Your current password">
                @error('current_password')
                <span style="color:red;font-size:12px">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">New Password *</label>
                <input type="password" name="new_password" id="new_pass"
                       class="form-control" placeholder="New password"
                       oninput="checkStrength(this.value)">
                {{--  Password strength bar --}}
                <div id="strength_bar" style="height:4px;border-radius:2px;margin-top:6px;background:#e2e8f0;overflow:hidden">
                    <div id="strength_fill" style="height:100%;width:0;border-radius:2px;transition:all .3s"></div>
                </div>
                <div id="strength_label" style="font-size:11px;color:var(--text-muted);margin-top:4px"></div>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm New Password *</label>
                <input type="password" name="new_password_confirmation"
                       class="form-control" placeholder="Repeat new password">
            </div>

            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>

        {{--  Account Info --}}
        <div style="margin-top:20px;padding:20px;border-top:1px solid var(--border)">
            <p style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px">
                Account Info
            </p>
            <table style="width:100%;font-size:13px">
                <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:7px 0;color:var(--text-muted)">Role</td>
                    <td style="padding:7px 0;font-weight:500">Administrator</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:7px 0;color:var(--text-muted)">Account Created</td>
                    <td style="padding:7px 0;font-weight:500">
                        {{ \Carbon\Carbon::parse($profile->created_at)->format('d M Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:7px 0;color:var(--text-muted)">Last Login</td>
                    <td style="padding:7px 0;font-weight:500">Today</td>
                </tr>
            </table>
        </div>
    </div>
</div>

{{--  Password Strength JS --}}
<script>
    function checkStrength(pw) {
        let score = 0;
        if (pw.length >= 8)          score++;
        if (/[A-Z]/.test(pw))        score++;
        if (/[0-9]/.test(pw))        score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;

        const colors = ['#ef4444', '#f59e0b', '#10b981', '#2563eb'];
        const labels = ['Weak', 'Fair', 'Good', 'Strong'];
        const fill   = document.getElementById('strength_fill');
        const label  = document.getElementById('strength_label');

        if (pw.length === 0) {
            fill.style.width   = '0';
            label.textContent  = '';
            return;
        }

        fill.style.width      = ((score) / 4 * 100) + '%';
        fill.style.background = colors[score - 1] || colors[0];
        label.textContent     = labels[score - 1] || 'Weak';
        label.style.color     = colors[score - 1] || colors[0];
    }
</script>
@endsection
