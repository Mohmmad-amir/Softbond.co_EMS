@extends('admin/header')
@section('content')

{{--  Page Header --}}
<div class="page-header">
    <div><h2>Expenses</h2><p>Track all business costs by project and category</p></div>
    <button class="btn btn-primary"
            onclick="document.getElementById('addModal').classList.add('open')">
        + Add Expense
    </button>
</div>

{{--  Metrics --}}
<div class="metrics-grid" style="grid-template-columns:repeat(3,1fr)">
    <div class="metric-card">
        <div class="metric-label">Total This Month</div>
        <div class="metric-val">৳{{ number_format($totalMonth) }}</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Top Category</div>
        <div class="metric-val" style="font-size:18px">
            {{ $catData[0]['category'] ?? '—' }}
        </div>
        <div class="metric-sub">৳{{ number_format($catData[0]['s'] ?? 0) }}</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Month</div>
        <div class="metric-val" style="font-size:18px">
            {{ \Carbon\Carbon::createFromDate($y, $m, 1)->format('M Y') }}
        </div>
    </div>
</div>

{{--  Charts --}}
<div class="grid-2">
    <div class="card">
        <div class="card-header"><span class="card-title">Expenses by Category</span></div>
        <div class="card-body">
            <canvas id="catChart" height="200"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><span class="card-title">Expenses by Project</span></div>
        <div class="card-body">
            <canvas id="projChart" height="200"></canvas>
        </div>
    </div>
</div>

{{--  Expenses Table --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">All Expenses</span>
        <form method="GET" action="{{ route('admin.expenses.index') }}"
              style="display:flex;gap:8px;align-items:center">
            <input type="month" name="month" class="form-control"
                   style="width:auto" value="{{ $month }}">
            <button type="submit" class="btn btn-outline btn-sm">Filter</button>
        </form>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Project</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Note</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($expenses as $e)
                <tr>
                    <td>{{ $e->date->format('d M') }}</td>
                    <td class="fw-600">{{ $e->description }}</td>
                    <td>
                        @if($e->project)
                            {{ $e->project->name }}
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td><span class="badge badge-gray">{{ $e->category }}</span></td>
                    <td class="fw-600">৳{{ number_format($e->amount) }}</td>
                    <td class="text-muted">{{ $e->note ?: '—' }}</td>
                    <td>
                        <div style="display:flex;gap:4px">
                            {{--  Edit --}}
                            <a href="?edit={{ $e->id }}&month={{ $month }}"
                               class="btn btn-outline btn-xs">Edit</a>

                            {{--  Delete --}}
                            <form method="POST"
                                  action="{{ route('admin.expenses.destroy', $e->id) }}"
                                  onsubmit="return confirm('Delete this expense?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-xs">Del</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center">No expenses found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{--  Add Modal --}}
<div class="modal-overlay" id="addModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Add Expense</h3>
            <button class="modal-close"
                    onclick="document.getElementById('addModal').classList.remove('open')">×</button>
        </div>
        <form method="POST" action="{{ route('admin.expenses.store') }}" class="modal-body">
            @csrf

            <div class="form-group">
                <label class="form-label">Description *</label>
                <input name="description" class="form-control" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Category *</label>
                    <select name="category" class="form-control">
                        @foreach($categories as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Amount (৳) *</label>
                    <input type="number" name="amount" class="form-control" step="0.01" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date *</label>
                    <input type="date" name="date" class="form-control"
                           value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Project (optional)</label>
                    <select name="project_id" class="form-control">
                        <option value="">— General —</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Note</label>
                <textarea name="note" class="form-control"></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline"
                        onclick="document.getElementById('addModal').classList.remove('open')">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">Add Expense</button>
            </div>
        </form>
    </div>
</div>

{{--  Edit Modal --}}
@if($editExp)
    <div class="modal-overlay open" id="editModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Edit Expense</h3>
                <a class="modal-close"
                   href="{{ route('admin.expenses.index', ['month' => $month]) }}">×</a>
            </div>
            <form method="POST"
                  action="{{ route('admin.expenses.update', $editExp->id) }}"
                  class="modal-body">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input name="description" class="form-control"
                           value="{{ $editExp->description }}" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-control">
                            @foreach($categories as $c)
                                <option value="{{ $c }}" {{ $editExp->category === $c ? 'selected' : '' }}>
                                    {{ $c }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Amount (৳)</label>
                        <input type="number" name="amount" class="form-control"
                               step="0.01" value="{{ $editExp->amount }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control"
                               value="{{ $editExp->date->format('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Project</label>
                        <select name="project_id" class="form-control">
                            <option value="">— General —</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}"
                                    {{ $editExp->project_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Note</label>
                    <textarea name="note" class="form-control">{{ $editExp->note }}</textarea>
                </div>

                <div class="modal-footer">
                    <a class="btn btn-outline"
                       href="{{ route('admin.expenses.index', ['month' => $month]) }}">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
@endif

{{--  Chart.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
    const catData  = @json($catData);
    const projData = @json($projData);
    const colors   = ['#2563eb','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899'];

    if (catData.length) {
        new Chart(document.getElementById('catChart'), {
            type: 'doughnut',
            data: {
                labels:   catData.map(d => d.category),
                datasets: [{
                    data:            catData.map(d => d.s),
                    backgroundColor: colors,
                    borderWidth:     0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 11 } }
                    }
                }
            }
        });
    }

    if (projData.length) {
        new Chart(document.getElementById('projChart'), {
            type: 'bar',
            data: {
                labels:   projData.map(d => d.name),
                datasets: [{
                    label:           'Expenses',
                    data:            projData.map(d => d.s),
                    backgroundColor: '#2563eb',
                    borderRadius:    4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { callback: v => '৳' + v } },
                    x: { ticks: { font: { size: 10 } } }
                }
            }
        });
    }
</script>

@endsection
