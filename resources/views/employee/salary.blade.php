@extends('employee.header')

@section('title', 'Salary')

@section('content')

    {{--  Page Header --}}
    <div class="page-header">
        <div>
            <h2>Salary</h2>
            <p>Request your salary and view payment history</p>
        </div>
    </div>

    {{--  Top Cards --}}
    <div class="grid-2" style="margin-bottom:24px">

        {{--  Monthly Salary Card --}}
        <div class="card" style="background:linear-gradient(135deg,#065f46,#10b981);border:none">
            <div class="card-body">
                <div style="color:rgba(255,255,255,.7);font-size:12px;text-transform:uppercase;letter-spacing:.06em">
                    My Monthly Salary
                </div>
                <div style="color:#fff;font-size:32px;font-weight:800;margin:8px 0">
                    ৳{{ number_format($emp->salary ?? 0) }}
                </div>
                <div style="color:rgba(255,255,255,.7);font-size:13px">
                    {{ $emp->designation }} · {{ $emp->department }}
                </div>
            </div>
        </div>

        {{--  Stats Card --}}
        <div class="card">
            <div class="card-body">
                <div class="metrics-grid" style="grid-template-columns:1fr 1fr;gap:12px;margin-bottom:0">
                    <div class="metric-card">
                        <div class="metric-label">Total Approved</div>
                        <div class="metric-val" style="font-size:20px">
                            ৳{{ number_format($approved) }}
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Pending</div>
                        <div class="metric-val" style="font-size:20px;color:var(--warning)">
                            {{ $pending }}
                        </div>
                        <div class="metric-sub">request(s)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--  Request Form --}}
    <div class="card" style="margin-bottom:24px">
        <div class="card-header">
            <span class="card-title">Submit Salary Request</span>
        </div>
        <form method="POST" action="{{ route('employee.salary.store') }}" class="card-body">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Amount (৳)</label>
                    <input type="number" name="amount" class="form-control"
                           value="{{ old('amount', $emp->salary) }}" required>
                    @error('amount')
                    <span style="color:red;font-size:12px">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">For Month</label>
                    <input type="month" name="month" class="form-control"
                           value="{{ old('month', now()->format('Y-m')) }}" required>
                    @error('month')
                    <span style="color:red;font-size:12px">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Note (optional)</label>
                <textarea name="note" class="form-control"
                          placeholder="Any additional note for admin...">{{ old('note') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Submit Request</button>
        </form>
    </div>

    {{--  Request History --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Request History</span>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                <tr>
                    <th>Month</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th>Actioned</th>
                    <th>Note</th>
                </tr>
                </thead>
                <tbody>
                @forelse($requests as $r)
                    @php
                        $sb = ['pending' => 'warning', 'approved' => 'success', 'denied' => 'danger'];
                    @endphp
                    <tr>
                        <td class="fw-600">{{ $r->month }}</td>
                        <td>৳{{ number_format($r->amount) }}</td>
                        <td>
                        <span class="badge badge-{{ $sb[$r->status] ?? 'gray' }}">
                            {{ ucfirst($r->status) }}
                        </span>
                        </td>
                        <td>
                            {{ $r->requested_at ? \Carbon\Carbon::parse($r->requested_at)->format('d M Y') : '—' }}
                        </td>
                        <td>
                            {{ $r->actioned_at ? \Carbon\Carbon::parse($r->actioned_at)->format('d M Y') : '—' }}
                        </td>
                        <td class="text-muted">{{ $r->note ?: '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">No salary requests yet.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
