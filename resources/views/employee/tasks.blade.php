@extends('employee/header')

@section('content')
{{-- ✅ Success Message --}}
@if(session('success'))
    <div class="alert alert-success" id="successAlert">
        {{ session('success') }}
        <script>
            setTimeout(() => {
                const el = document.getElementById('successAlert');
                if(el) el.style.opacity = '0';
            }, 3000);
        </script>
    </div>
@endif

{{-- ✅ Page Header --}}
<div class="page-header">
    <div>
        <h2>My Tasks</h2>
        <p>All tasks assigned to you across projects</p>
    </div>
</div>

{{-- ✅ Metrics --}}
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Total</div>
        <div class="metric-val">{{ $total }}</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">In Progress</div>
        <div class="metric-val" style="color:var(--info)">{{ $inprog }}</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Pending</div>
        <div class="metric-val" style="color:var(--warning)">{{ $pending }}</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Done</div>
        <div class="metric-val" style="color:var(--success)">{{ $done }}</div>
    </div>
</div>

{{-- ✅ Filter Buttons --}}
<div style="display:flex;gap:8px;margin-bottom:16px">
    @foreach(['all' => 'All Tasks', 'pending' => 'Pending', 'active' => 'In Progress', 'done' => 'Done'] as $k => $label)
        <a href="{{ route('employee.tasks.index', ['filter' => $k]) }}"
           class="btn {{ $filter === $k ? 'btn-primary' : 'btn-outline' }} btn-sm">
            {{ $label }}
        </a>
    @endforeach
</div>

{{-- ✅ Tasks Table --}}
<div class="card">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
            <tr>
                <th>Task</th>
                <th>Project</th>
                <th>Due Date</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Update</th>
            </tr>
            </thead>
            <tbody>
            @forelse($tasks as $t)
                @php
                    $pb        = ['low' => 'gray', 'medium' => 'warning', 'high' => 'danger'];
                    $sb        = ['pending' => 'gray', 'in_progress' => 'info', 'done' => 'success'];
                    $isOverdue = $t->due_date
                        && $t->status !== 'done'
                        && $t->due_date->isPast();
                @endphp
                <tr>
                    <td>
                        <div class="fw-600">{{ $t->title }}</div>
                        @if($t->description)
                            <div class="text-muted" style="font-size:11px;margin-top:2px">
                                {{ Str::limit($t->description, 60) }}
                            </div>
                        @endif
                    </td>
                    <td>{{ $t->project->name ?? '—' }}</td>
                    <td class="{{ $isOverdue ? 'text-danger fw-600' : '' }}">
                        {{ $t->due_date ? $t->due_date->format('d M Y') : '—' }}
                        @if($isOverdue)
                            <div style="font-size:10px">Overdue!</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $pb[$t->priority] ?? 'gray' }}">
                            {{ ucfirst($t->priority) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $sb[$t->status] ?? 'gray' }}">
                            {{ Str::headline($t->status) }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:4px;flex-wrap:wrap">

                            {{-- ✅ Start button --}}
                            @if($t->status === 'pending')
                                <form method="POST"
                                      action="{{ route('employee.tasks.status', $t->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="btn btn-outline btn-xs">
                                        Start
                                    </button>
                                </form>
                            @endif

                            {{-- ✅ Mark Done button --}}
                            @if($t->status === 'in_progress')
                                <form method="POST"
                                      action="{{ route('employee.tasks.status', $t->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="done">
                                    <button type="submit" class="btn btn-success btn-xs">
                                        Mark Done
                                    </button>
                                </form>
                            @endif

                            {{-- ✅ Completed label --}}
                            @if($t->status === 'done')
                                <span class="text-muted" style="font-size:11px">
                                    Completed ✓
                                </span>
                            @endif

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">No tasks found.</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
