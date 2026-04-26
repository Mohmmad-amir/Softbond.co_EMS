
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
  <div class="card">
    <div class="card-header"><span class="card-title">Revenue vs Expenses ({{date('Y')}})</span></div>
    <div class="card-body">
      <canvas id="revChart" height="220" role="img" aria-label="Revenue vs expenses chart">Revenue and expenses data.</canvas>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">Pending Salary Requests</span>
{{--      <?php //if ($pendingSal): ?>--}}

        <span class="badge badge-danger">

{{--            <?php //= $pendingSal ?>--}}

            pending</span>

{{--        <?php //endif; ?>--}}
    <div class="card-body" style="padding:0">
{{--      <?php--}}
{{--//      $salReqs->data_seek(0);--}}
{{--//      $hasSal = false--}}
{{--//      while ($r = $salReqs->fetch_assoc()): $hasSal = true ?>--}}
      <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border)">
        <div class="avatar av-blue">{{--<?php //= strtoupper(substr($r['name'],0,2)) ?><!---->--}}</div>
        <div style="flex:1"><div class="fw-600">{{--<?php //= e($r['name']) ?><!---->--}}</div><div class="text-muted" style="font-size:11px">{{--<?php = e($r['designation']) ?>--}}<!----> · ৳{{--<?php = number_format($r['amount']) ?>--}}<!----></div></div>
        <div style="display:flex;gap:4px">
          <a href="<!---->/admin/salary.php?action=approve&id=

{{--<?php //= $r['id'] ?><!---->--}}
" class="btn btn-success btn-xs">Approve</a>
          <a href="<!---->/admin/salary.php?action=deny&id=

{{--<?php //= $r['id'] ?><!---->--}}
" class="btn btn-danger btn-xs">Deny</a>
        </div>
      </div>
{{--      <?php endwhile ?>--}}
{{--      <?php //if (!$hasSal): ?>--}}

        <div class="empty-state">No pending requests</div>

{{--        <?php //endif ?>--}}
    </div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const mData = <?= json_encode($monthlyData) ?>;
new Chart(document.getElementById('revChart'), {
  type: 'bar',
  data: {
    labels: months,
    datasets: [
      { label: 'Revenue', data: mData.map(d=>d.rev), backgroundColor: '#2563eb', borderRadius: 4 },
      { label: 'Expenses', data: mData.map(d=>d.exp), backgroundColor: '#10b981', borderRadius: 4 }
    ]
  },
  options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { boxWidth: 10, font: { size: 11 } } } }, scales: { y: { ticks: { callback: v => '৳'+v } } } }
});
</script>
<?php //require 'footer.blade.php'; ?>

@endsection

