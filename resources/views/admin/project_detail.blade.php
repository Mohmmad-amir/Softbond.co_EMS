@extends('admin/header')
@section('content')

    <!-- Back -->
    <div style="margin-bottom:16px">
        <a href="{{route('admin.project')}}" class="btn btn-outline btn-sm">← Back to Projects</a>
    </div>

    <!-- Project Hero -->
    <div class="card" style="border-left:4px solid #2563eb;margin-bottom:24px">
        <div class="card-body">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
                <div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                        <h2 style="font-size:22px;font-weight:800">{{$project->name}}</h2>
                        <span class="badge badge-{{ $bsMap[$project->status] ?? 'gray' }}"
                              style="font-size:12px">{{ Str::headline($project->status) }}</span>
                        <span class="badge badge-primary">{{$project->type}}</span>
                    </div>
                    @if($project->client)
                        <div class="text-muted" style="font-size:13px">Client: <strong>{{$project->client}}</strong>
                        </div>
                    @endif
                    @if($project->description)
                        <div
                            style="font-size:13px;color:var(--text-muted);margin-top:6px;max-width:600px">{{$project->description}}</div>
                    @endif

                </div>
                <a
                    class="btn btn-outline btn-sm"
                    href="#"
                    onclick="event.preventDefault(); window.history.pushState({}, '', '?edit='); document.getElementById('editModal').classList.add('open');"
                >
                    Edit Project
                </a>
            </div>

            <!-- Timeline bar -->
            @if($project->start_date && $project->end_date)
                @php
                    $startDate = \Carbon\Carbon::parse($project->start_date);
                    $endDate = \Carbon\Carbon::parse($project->end_date);
                    $today = \Carbon\Carbon::today();
                    $daysLeft = $today->diffInDays($endDate, false);
                    $totalDays = $startDate->diffInDays($endDate);
                  $passedDays = $startDate->diffInDays($today);
                  $elapsed = $totalDays > 0 ? min(100, max(0, ($passedDays / $totalDays) * 100)) : 0;
                @endphp

                <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border)">
                    <div
                        style="display:flex;justify-content:space-between;font-size:12px;color:var(--text-muted);margin-bottom:6px">
                        <span>Started: {{ $startDate->format('d M Y') }}</span>
                        <span
                            style="font-weight:600; color: {{ $daysLeft < 0 ? 'var(--danger)' : ($daysLeft <= 7 ? 'var(--warning)' : 'var(--success)') }}">
                @if($daysLeft < 0)
                                Overdue by {{ abs($daysLeft) }} days
                            @elseif($project->status === 'completed')
                                Completed
                            @else
                                {{ $daysLeft }} days remaining
                            @endif
            </span>
                        <span>Deadline: {{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}</span>
                        @endif

                    </div>
                    <div style="height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden;position:relative">
                        <div
                            style="height:100%;width:{{$project->progress}}%;background:#2563eb;border-radius:4px;position:absolute;top:0;left:0;z-index:2"></div>
                        <div
                            style="height:100%;width:{{ $elapsed }}%;background:rgba(0,0,0,.08);border-radius:4px;position:absolute;top:0;left:0;z-index:1"></div>
                    </div>
                    <div
                        style="display:flex;justify-content:space-between;font-size:11px;margin-top:4px;color:var(--text-muted)">
                        <span>Work progress: <strong>{{$project->progress}}%</strong></span>
                        <span>Time elapsed: <strong>{{ $elapsed }}%</strong></span>
                        <span>Total: <strong>{{ $totalDays }} days</strong></span>
                    </div>
                </div>
        </div>
    </div>

    {{--<!-- Financial Summary -->--}}
    <div class="metrics-grid" style="margin-bottom:24px">
        <div class="metric-card">
            <div class="metric-label">Total Budget</div>
            <div class="metric-val">৳{{number_format($project->budget, 0)}}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Total Received</div>
            <div class="metric-val" style="color:var(--success)">৳{{ number_format($totalPaid, 0) }}</div>
            <div class="metric-sub">৳{{ number_format($remainingBudget, 0) }} remaining</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Total Expenses</div>
            <div class="metric-val" style="color:var(--danger)">৳ {{number_format($totalExp, 0)}} </div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Net Profit</div>
            <div class="metric-val {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                ৳{{ number_format($netProfit, 0) }}</div>
            @if($totalPaid > 0)
                <div class="metric-sub">
                    {{ round(($profit / $totalPaid) * 100) }}% margin
                </div>
            @endif
        </div>
    </div>

    <div class="grid-2">
        {{--  <!-- ── PAYMENT LEDGER ────────────────────────────────────────────── -->--}}
        <div>
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Payment Received Ledger</span>
                    <button class="btn btn-primary btn-sm"
                            onclick="document.getElementById('payModal').classList.add('open')">+ Add Payment
                    </button>
                </div>
                @if($totalPaid > 0)
                    <div
                        style="padding:10px 20px; background:var(--success-light); border-bottom:1px solid #bbf7d0; font-size:13px; color:var(--success); font-weight:600">
                        Total Received: ৳{{ number_format($totalPaid, 0) }} / ৳{{ number_format($project->budget, 0) }}
                        budget
                    </div>
                @endif
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Note</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($payments as $p)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($p->payment_date)->format('d M Y') }}</td>
                                <td class="fw-600 text-success">৳{{ number_format($p->amount) }}</td>
                                <td class="text-muted">{{ $p->note ?? '—' }}</td>
                                <td>
                                    <form action="{{ route('admin.payment.destroy', $p->id) }}" method="POST"
                                          style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs"
                                                onclick="return confirm('Delete?')">
                                            Del
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No payments recorded yet.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{--    <!-- Tasks -->--}}
            <div class="card">
                <div class="card-header"><span class="card-title">Project Tasks</span><span
                        class="badge badge-gray">{{ $tasks->count() }}</span></div>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                        <tr>
                            <th>Task</th>
                            <th>Assigned To</th>
                            <th>Due</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $statusBadge = ['pending' => 'gray', 'in_progress' => 'info', 'done' => 'success'];
                            $priorityBadge = ['low' => 'gray', 'medium' => 'warning', 'high' => 'danger'];
                        @endphp
                        @forelse($tasks as $t)
                            <tr>
                                <td>
                                    <div class="fw-600" style="font-size:13px">{{ $t->title }}</div>
                                    <span class="badge badge-{{ $priorityBadge[$t->priority] ?? 'gray' }}"
                                          style="font-size:10px">
                                {{ ucfirst($t->priority) }}</span>
                                </td>
                                <td>{{ $t->emp_name }}</td>
                                <td>{{ $t->due_date ? \Carbon\Carbon::parse($t->due_date)->format('d M') : '—' }}</td>
                                <td>
                            <span class="badge badge-{{ $statusBadge[$t->status] ?? 'gray' }}">
                                {{ ucfirst(str_replace('_', ' ', $t->status)) }}
                            </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">No tasks assigned.</div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{--  <!-- ── EXPENSE LEDGER ─────────────────────────────────────────────── -->--}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Project Expenses</span>
                <button class="btn btn-danger btn-sm"
                        onclick="document.getElementById('expModal').classList.add('open')">+ Add Expense
                </button>
            </div>
            @if($totalExp > 0)
                <div
                    style="padding:10px 20px;background:var(--danger-light);border-bottom:1px solid #fecaca;font-size:13px;color:var(--danger);font-weight:600">
                    Total Spent: ৳<?= number_format($totalExp) ?>
                </div>
            @endif
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($expenses as $ex)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($ex->expense_date)->format('d M Y') }}</td>

                            <td class="fw-600">{{ $ex->description }}</td>

                            <td><span class="badge badge-gray">{{ $ex->category }}</span></td>

                            <td class="fw-600 text-danger">৳{{ number_format($ex->amount, 0) }}</td>

                            <td>
                                <form action="{{ route('admin.expenses.destroy', $ex->id) }}" method="POST"
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs"
                                            onclick="return confirm('Delete?')">
                                        Del
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">No expenses recorded yet.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{--<!-- ── ADD PAYMENT MODAL ──────────────────────────────────────────────────── -->--}}
    <div class="modal-overlay" id="payModal">
        <div class="modal" style="max-width:440px">
            <div class="modal-header">
                <h3>Record Payment Received</h3>
                <button class="modal-close" onclick="document.getElementById('payModal').classList.remove('open')">×
                </button>
            </div>
            <form action="{{route('admin.payments.store')}}" method="POST" class="modal-body">
                @csrf
                @method('post')
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <input type="hidden" name="add_payment" value="1">
                <div
                    style="background:var(--info-light);border:1px solid #a5f3fc;border-radius:var(--radius);padding:10px 14px;margin-bottom:16px;font-size:12px;color:var(--info)">
                    Current received: <strong>৳ {{number_format($totalPaid,0)}} </strong> · Budget:
                    <strong>৳ {{$project->budget}} </strong>
                </div>
                <div class="form-group">
                    <label class="form-label">Amount Received (৳) *</label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="1" required
                           placeholder="e.g. 50000">
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Date *</label>
                    <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Note (optional)</label>
                    <textarea name="note" class="form-control"
                              placeholder="e.g. 1st installment, milestone payment..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline"
                            onclick="document.getElementById('payModal').classList.remove('open')">Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Save Payment</button>
                </div>
            </form>

        </div>
    </div>

    {{--<!-- ── ADD EXPENSE MODAL ──────────────────────────────────────────────────── -->--}}
    <div class="modal-overlay" id="expModal">
        <div class="modal" style="max-width:440px">
            <div class="modal-header">
                <h3>Add Project Expense</h3>
                <button class="modal-close" onclick="document.getElementById('expModal').classList.remove('open')">×
                </button>
            </div>
            <form action="{{ route('admin.expense.store') }}" method="POST" class="modal-body">
                @csrf
                @method('post')
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <input type="hidden" name="add_expense" value="1">
                <div class="form-group">
                    <label class="form-label">Description *</label>
                    <input name="description" class="form-control" required
                           placeholder="e.g. AWS hosting, Figma license...">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-control">
                            @php $expCategories = ['Software','Hosting','Tools','Operations','Marketing','Salary','Other']; @endphp
                            @foreach($expCategories as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Amount (৳) *</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="1" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Date *</label>
                    <input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Note</label>
                    <textarea name="note" class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline"
                            onclick="document.getElementById('expModal').classList.remove('open')">Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">Add Expense</button>
                </div>
            </form>
        </div>
    </div>
    @php
        $types = ['Web Dev','App Dev','Game Dev','Marketing','Design','Other'];
        $statuses = $statuses ?? ['active', 'inactive', 'pending_approval'];
    @endphp


{{--    edit project modal--}}
    <div class="modal-overlay {{ request()->has('edit') ? 'open' : '' }}" id="editModal">
        <div class="modal">
            <div class="modal-header"><h3>Edit Project</h3><button type="button"  onclick="
        document.getElementById('editModal').classList.remove('open');
        window.history.replaceState({}, document.title, window.location.pathname);
    " class="modal-close">×</button></div>

            <form method="POST" action="{{route('admin.projects.update',$project->id)}}" class="modal-body">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="edit"><input type="hidden" name="id"
                                                                       value="{{$project->id}}">
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Project Name</label><input name="name"
                                                                                                 class="form-control"
                                                                                                 value="{{$project->name}}"
                                                                                                 required></div>
                    <div class="form-group"><label class="form-label">Client</label><input name="client"
                                                                                           class="form-control"
                                                                                           value="{{$project->client}}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Type</label><select name="type"
                                                                                          class="form-control">                  @foreach ($types as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach</select></div>
                    <div class="form-group"><label class="form-label">Status</label><select name="status"
                                                                                            class="form-control">                  @foreach ($statuses as $s)
                                <option value="{{ $s }}">
                                    {{ Str::headline($s) }}
                                </option>
                            @endforeach</select></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Budget (৳)</label><input type="number"
                                                                                               name="budget"
                                                                                               class="form-control"
                                                                                               value="{{$project->budget}}">
                    </div>
                    <div class="form-group"><label class="form-label">Received (৳)</label><input type="number"
                                                                                                 name="received"
                                                                                                 class="form-control"
                                                                                                 value="{{$project->received}}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Start Date</label><input type="date"
                                                                                               name="start_date"
                                                                                               class="form-control"
                                                                                               value="{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '' }}">
                    </div>
                    <div class="form-group"><label class="form-label">End Date</label><input type="date" name="end_date"
                                                                                             class="form-control"
                                                                                             value="{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') : '' }}">
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Progress (%)</label><input type="number"
                                                                                             name="progress"
                                                                                             class="form-control"
                                                                                             value="{{$project->progress}}"
                                                                                             min="0" max="100"></div>
                <div class="form-group"><label class="form-label">Description</label><textarea name="description"
                                                                                               class="form-control"> {{$project->description}}</textarea>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-outline model-close"
                        onclick="document.getElementById('editModal').classList.remove('open');
                                window.history.replaceState({}, document.title, window.location.pathname);">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection

