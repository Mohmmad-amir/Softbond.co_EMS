@extends('admin.header')

@section('title', 'Salary Requests')

@section('content')

    {{--  Success Message --}}
    @if(session('success'))
        <div class="alert alert-success" id="flashAlert">
            {{ session('success') }}
            <script>
                setTimeout(() => {
                    const el = document.getElementById('flashAlert');
                    if(el) { el.style.opacity = '0'; }
                }, 3000);
            </script>
        </div>
    @endif

    {{--  Page Header --}}
    <div class="page-header">
        <div>
            <h2>Salary Requests</h2>
            <p>Approve or deny employee salary disbursements</p>
        </div>
        @if($pendingCount)
            <span class="badge badge-danger" style="font-size:13px">
            {{ $pendingCount }} pending
        </span>
        @endif
    </div>

    {{--  Pending Requests --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Pending Requests</span>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                <tr>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Amount</th>
                    <th>Month</th>
                    <th>Requested</th>
                    <th>Note</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($pending as $r)
                    <tr>
                        <td>
                            <div class="avatar-row">
                                <div class="avatar av-blue">
                                    {{ strtoupper(substr($r->employee->name, 0, 2)) }}
                                </div>
                                <div class="info">
                                    <div class="name">{{ $r->employee->name }}</div>
                                    <div class="sub">{{ $r->employee->designation }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                        <span class="badge badge-primary">
                            {{ $r->employee->department }}
                        </span>
                        </td>
                        <td class="fw-600">৳{{ number_format($r->amount) }}</td>
                        <td>{{ $r->month }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($r->requested_at)->format('d M Y') }}
                        </td>
                        <td class="text-muted">{{ $r->note ?: '—' }}</td>
                        <td>
                            <div style="display:flex;gap:4px">
                                {{--  Approve --}}
                                <form method="POST"
                                      action="{{ route('admin.salary.approve', $r->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        Approve
                                    </button>
                                </form>

                                {{--  Deny --}}
                                <form method="POST"
                                      action="{{ route('admin.salary.deny', $r->id) }}"
                                      onsubmit="return confirm('Deny this request?')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Deny
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">No pending salary requests</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{--  History --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">History</span>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                <tr>
                    <th>Employee</th>
                    <th>Amount</th>
                    <th>Month</th>
                    <th>Status</th>
                    <th>Actioned</th>
                </tr>
                </thead>
                <tbody>
                @forelse($history as $r)
                    <tr>
                        <td>
                            <div class="avatar-row">
                                <div class="avatar av-teal">
                                    {{ strtoupper(substr($r->employee->name, 0, 2)) }}
                                </div>
                                <div class="info">
                                    <div class="name">{{ $r->employee->name }}</div>
                                    <div class="sub">{{ $r->employee->designation }}</div>
                                </div>
                            </div>
                        </td>
                        <td>৳{{ number_format($r->amount) }}</td>
                        <td>{{ $r->month }}</td>
                        <td>
                            @if($r->status === 'approved')
                                <span class="badge badge-success">Approved</span>
                            @else
                                <span class="badge badge-danger">Denied</span>
                            @endif
                        </td>
                        <td>
                            {{ $r->actioned_at
                                ? \Carbon\Carbon::parse($r->actioned_at)->format('d M Y')
                                : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">No history found.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
