@extends('admin/header')
@section('content')
{{--  Page Header --}}
<div class="page-header">
    <div>
        <h2>Profit &amp; Loss</h2>
        <p>Revenue, expenses, and net profit per project and overall</p>
    </div>
</div>

{{--  Metrics --}}
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Total Revenue</div>
        <div class="metric-val">৳{{ number_format($totalRev) }}</div>
        <div class="metric-sub">All time</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Total Expenses</div>
        <div class="metric-val">৳{{ number_format($totalExp) }}</div>
        <div class="metric-sub">All time</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Net Profit</div>
        <div class="metric-val {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
            ৳{{ number_format($netProfit) }}
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Profit Margin</div>
        <div class="metric-val">{{ $profitMargin }}%</div>
    </div>
</div>

{{--  Monthly P&L Chart --}}
<div class="card" style="margin-bottom:24px">
    <div class="card-header">
        <span class="card-title">Monthly P&amp;L Trend ({{ $year }})</span>
    </div>
    <div class="card-body">
        <canvas id="plChart" height="240"></canvas>
    </div>
</div>

{{--  Per-Project Profit Summary --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Per-Project Profit Summary</span>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
            <tr>
                <th>Project</th>
                <th>Client</th>
                <th>Type</th>
                <th>Budget</th>
                <th>Received</th>
                <th>Expenses</th>
                <th>Profit</th>
                <th>Margin</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @forelse($projects as $p)
                @php
                    $profit = $p->received - $p->total_exp;
                    $margin = $p->received > 0 ? round($profit / $p->received * 100) : 0;
                    $bs     = [
                        'new'       => 'gray',
                        'active'    => 'info',
                        'on_hold'   => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    ];
                    $marginBadge = $margin >= 20 ? 'badge-success' : ($margin >= 10 ? 'badge-warning' : 'badge-danger');
                @endphp
                <tr>
                    <td class="fw-600">{{ $p->name }}</td>
                    <td>{{ $p->client }}</td>
                    <td><span class="badge badge-primary">{{ $p->type }}</span></td>
                    <td>৳{{ number_format($p->budget) }}</td>
                    <td>৳{{ number_format($p->received) }}</td>
                    <td>৳{{ number_format($p->total_exp) }}</td>
                    <td class="fw-600 {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                        ৳{{ number_format($profit) }}
                    </td>
                    <td>
                        <span class="badge {{ $marginBadge }}">{{ $margin }}%</span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $bs[$p->status] ?? 'gray' }}">
                            {{ Str::headline($p->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center">No projects found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{--  Chart.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
    const mpl    = @json($monthlyPL);
    const labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    new Chart(document.getElementById('plChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label:           'Revenue',
                    data:            mpl.map(d => d.rev),
                    borderColor:     '#2563eb',
                    backgroundColor: 'rgba(37,99,235,.08)',
                    tension:         .4,
                    fill:            true
                },
                {
                    label:           'Expenses',
                    data:            mpl.map(d => d.exp),
                    borderColor:     '#ef4444',
                    backgroundColor: 'rgba(239,68,68,.05)',
                    tension:         .4,
                    fill:            true
                },
                {
                    label:           'Profit',
                    data:            mpl.map(d => d.profit),
                    borderColor:     '#10b981',
                    backgroundColor: 'rgba(16,185,129,.08)',
                    tension:         .4,
                    fill:            true
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
