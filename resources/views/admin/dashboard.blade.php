
@extends('admin/header')
@section('content')
<div class="page-header">
  <div><h2>Dashboard</h2>
      <p>Welcome back, {{Auth::user()->name}}. Here's your overview.</p></div>
</div>

<div class="metrics-grid">
  <div class="metric-card">
    <div class="metric-label">Active Employees</div>
    <div class="metric-val">
        {{$totalUser}}
    </div>
    <div class="metric-sub">Total staff</div>
  </div>
  <div class="metric-card">
    <div class="metric-label">Active Projects</div>
    <div class="metric-val">
        {{$activeProjectCount}}
    </div>
    <div class="metric-sub">Running now</div>
  </div>
  <div class="metric-card">
    <div class="metric-label">Month Revenue</div>
    <div class="metric-val">৳
        {{ number_format($monthRev, 0) }}
    </div>
    <div class="metric-sub up">This month</div>
  </div>
  <div class="metric-card">
    <div class="metric-label">Month Expenses</div>
    <div class="metric-val">৳
        {{number_format($monthEXP,0)}}
    </div>
    <div class="metric-sub">This month</div>
  </div>
</div>

<div class="grid-2">

    {{-- ✅ Revenue vs Expenses Chart --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Revenue vs Expenses ({{ $year }})</span>
        </div>
        <div class="card-body">
            <canvas id="revChart" height="220"
                    role="img" aria-label="Revenue vs expenses chart">
            </canvas>
        </div>
    </div>

    {{--  Pending Salary Requests --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Pending Salary Requests</span>
            @if($pendingSal > 0)
                <span class="badge badge-danger">{{ $pendingSal }} pending</span>
            @endif
        </div>
        <div class="card-body" style="padding:0">
            @forelse($salReqs as $r)
                <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border)">

                    {{--  Avatar --}}
                    <div class="avatar av-blue">
                        {{ strtoupper(substr($r->employee->name, 0, 2)) }}
                    </div>

                    {{--  Info --}}
                    <div style="flex:1">
                        <div class="fw-600">{{ $r->employee->name }}</div>
                        <div class="text-muted" style="font-size:11px">
                            {{ $r->employee->designation }} · ৳{{ number_format($r->amount) }}
                        </div>
                    </div>

                    {{--  Approve/Deny buttons --}}
                    <div style="display:flex;gap:4px">
                        <form method="POST"
                              action="{{ route('admin.salary.approve', $r->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-success btn-xs">
                                Approve
                            </button>
                        </form>

                        <form method="POST"
                              action="{{ route('admin.salary.deny', $r->id) }}"
                              onsubmit="return confirm('Deny this request?')">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-xs">
                                Deny
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state" style="padding:24px">
                    No pending requests
                </div>
            @endforelse
        </div>
    </div>

</div>

<div class="card">
  <div class="card-header"><span class="card-title">Recent Projects</span><a href="{{route('admin.project')}}" class="btn btn-outline btn-sm">View All</a></div>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>Project</th><th>Client</th><th>Type</th><th>Budget</th><th>Received</th><th>Progress</th><th>Status</th></tr></thead>
      <tbody>
      @foreach($recentProjects as $p)
      <tr>
        <td class="fw-600"><a href="{{ route('admin.projects.show', $p->id) }}" style="color:var(--primary)">{{$p->name}}</a></td>
        <td>
            {{$p->client}}

        </td>
        <td><span class="badge badge-primary">{{$p->type}}</span></td>
        <td>৳{{number_format($p['budget'])}}</td>
        <td>৳{{number_format($p['received'])}}</td>
        <td style="width:120px">
          <div style="font-size:11px;color:var(--text-muted);margin-bottom:3px">{{$p['progress']}}%</div>
          <div class="progress"><div class="progress-bar" style="width:{{ $p->progress }}%"></div></div>
        </td>
        <td>
            @php
                $badges = ['new'=>'gray', 'active'=>'info', 'on_hold'=>'warning', 'completed'=>'success', 'cancelled'=>'danger'];
                $statusClass = $badges[$p->status] ?? 'gray';
            @endphp
            <span class="badge badge-{{ $statusClass }}">
                    {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                </span>
        </td>
      </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>


{{--  Chart.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
    const monthlyData = @json($monthlyData);
    const labels      = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    new Chart(document.getElementById('revChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label:           'Revenue',
                    data:            monthlyData.map(d => d.rev),
                    backgroundColor: '#2563eb',
                    borderRadius:    4,
                },
                {
                    label:           'Expenses',
                    data:            monthlyData.map(d => d.exp),
                    backgroundColor: '#ef4444',
                    borderRadius:    4,
                }
            ]
        },
        options: {
            responsive:          true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { boxWidth: 10, font: { size: 11 } }
                }
            },
            scales: {
                y: {
                    ticks: { callback: v => '৳' + v }
                }
            }
        }
    });
</script>

@endsection

