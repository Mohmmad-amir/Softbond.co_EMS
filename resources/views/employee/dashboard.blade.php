

@extends('employee/header')

@section('content')

    {{--  Page Header --}}
    <div class="page-header">
        <div>
            <h2>My Dashboard</h2>
            <p>Welcome back, {{ $emp->name }}!</p>
        </div>
    </div>

    {{--  Today's Attendance Card --}}
    <div class="card" style="background:linear-gradient(135deg,#1e40af,#2563eb);border:none;margin-bottom:24px">
        <div class="card-body" style="display:flex;align-items:center;justify-content:space-between">
            <div>
                <div style="color:rgba(255,255,255,.7);font-size:12px;text-transform:uppercase;letter-spacing:.06em">
                    Today's Status
                </div>
                <div style="color:#fff;font-size:22px;font-weight:700;margin:4px 0">
                    @if($todayAtt)
                        {{ Str::headline($todayAtt->status) }}
                    @else
                        Not Marked
                    @endif
                </div>
                @if($todayAtt && $todayAtt->check_in)
                    <div style="color:rgba(255,255,255,.7);font-size:13px">
                        Check-in: {{ \Carbon\Carbon::parse($todayAtt->check_in)->format('h:i A') }}
                    </div>
                @endif
            </div>
            <div style="text-align:right">
                <div style="color:rgba(255,255,255,.7);font-size:12px">
                    Days Present This Month
                </div>
                <div style="color:#fff;font-size:36px;font-weight:800">{{ $monthAtt }}</div>
            </div>
        </div>
    </div>

    {{--  Metrics --}}
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-label">Total Tasks</div>
            <div class="metric-val">{{ $totalTasks }}</div>
            <div class="metric-sub">Assigned to me</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Completed</div>
            <div class="metric-val" style="color:var(--success)">{{ $doneTasks }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Pending</div>
            <div class="metric-val" style="color:var(--warning)">{{ $pendingTasks }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">My Salary</div>
            <div class="metric-val">৳{{ number_format($emp->salary ?? 0) }}</div>
            <div class="metric-sub">Per month</div>
        </div>
    </div>

    <div class="grid-2">

        {{--  Recent Tasks --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">My Recent Tasks</span>
                <a href="{{ route('employee.tasks.index') }}" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div style="padding:0 4px">
                @forelse($recentTasks as $t)
                    @php
                        $sb = ['pending' => 'gray', 'in_progress' => 'info', 'done' => 'success'];
                        $pb = ['low' => 'gray', 'medium' => 'warning', 'high' => 'danger'];
                    @endphp
                    <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border)">
                        <div style="flex:1">
                            <div class="fw-600" style="font-size:13px">{{ $t->title }}</div>
                            <div class="text-muted" style="font-size:11px">
                                {{ $t->project->name ?? '—' }} ·
                                Due {{ $t->due_date ? $t->due_date->format('d M') : '—' }}
                            </div>
                        </div>
                        <span class="badge badge-{{ $pb[$t->priority] ?? 'gray' }}">
                    {{ ucfirst($t->priority) }}
                </span>
                        <span class="badge badge-{{ $sb[$t->status] ?? 'gray' }}">
                    {{ Str::headline($t->status) }}
                </span>
                    </div>
                @empty
                    <div class="empty-state" style="padding:24px">
                        No tasks assigned yet.
                    </div>
                @endforelse
            </div>
        </div>

        {{--  Salary Requests --}}
        {{--  Salary Requests --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">My Salary Requests</span>
                <a href="{{ route('employee.salary.index') }}" class="btn btn-outline btn-sm">Manage</a>
            </div>
            <div style="padding:0 4px">
                @forelse($salaryRequests as $r)
                    @php
                        $sb = ['pending' => 'warning', 'approved' => 'success', 'denied' => 'danger'];
                    @endphp
                    <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border)">
                        <div style="flex:1">
                            <div class="fw-600" style="font-size:13px">
                                ৳{{ number_format($r->amount) }} — {{ $r->month }}
                            </div>
                            <div class="text-muted" style="font-size:11px">
                                {{ $r->requested_at->format('d M Y') }}
                            </div>
                        </div>
                        <span class="badge badge-{{ $sb[$r->status] ?? 'gray' }}">
                    {{ ucfirst($r->status) }}
                </span>
                    </div>
                @empty
                    <div class="empty-state" style="padding:24px">
                        No salary requests yet.
                    </div>
                @endforelse
            </div>
        </div>

    </div>



@endsection
