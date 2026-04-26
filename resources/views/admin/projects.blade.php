@extends('admin/header')
@section('content')

    <div class="page-header">
        <div><h2>Projects</h2>
            <p>Manage all client projects and track financials</p></div>
        <button class="btn btn-primary" onclick="document.getElementById('addModal').classList.add('open')">+ New
            Project
        </button>
    </div>

    <div class="card">
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
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>

                @foreach($projectsAll as $p)
                    @php
                        //            $profit = ($p->received ?? 0) - ($p->total_expense ?? 0);
                    @endphp

                    <tr>
                        <td class="fw-600">
                            <a href="{{ route('admin.projects.show', $p->id) }}" style="color:var(--primary)">
                                {{ $p->name }}
                            </a>
                        </td>

                        <td>{{ $p->client }}</td>

                        <td><span class="badge badge-primary">{{ $p->type }}</span></td>

                        <td>৳{{ number_format($p->budget, 0) }}</td>
                        <td>৳{{ number_format($p->received, 0) }}</td>
                        <td>৳{{ number_format($totalExp,0) }}</td>

                        <td class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }} fw-600">
                            ৳{{ number_format($profit, 0) }}
                        </td>

                        <td style="min-width:100px">
                            <div style="font-size:11px; color:var(--text-muted); margin-bottom:3px">
                                {{ $p->progress }}%
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: {{ $p->progress }}%"></div>
                            </div>
                        </td>

                        <td>
            <span class="badge badge-{{ $bs[$p->status] ?? 'gray' }}">
                {{ str($p->status)->headline() }}
            </span>
                        </td>

                        <td>
                            <div style="display:flex; gap:4px">
                                <a href="{{ route('admin.projects.show', $p->id) }}"
                                   class="btn btn-primary btn-xs">View</a>

                                <a href="{{ route('admin.projects.show', $p->id) }}?edit={{ $p->id }}"
                                   class="btn btn-outline btn-xs">Edit</a>
                                <form action="{{ route('admin.project.destroy', $p->id) }}" method="POST"
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs"
                                            onclick="return confirm('Delete?')">
                                        Del
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{--<!-- ADD MODAL -->--}}
    <div class="modal-overlay" id="addModal">
        <div class="modal">
            <div class="modal-header"><h3>New Project</h3>
                <button class="modal-close" onclick="document.getElementById('addModal').classList.remove('open')">×
                </button>
            </div>
            <form method="POST" action="{{route('admin.project.store')}}" class="modal-body">
                @csrf
                <input type="hidden" name="action" value="add">
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Project Name *</label><input name="name"
                                                                                                   class="form-control"
                                                                                                   required></div>
                    <div class="form-group"><label class="form-label">Client Name</label><input name="client"
                                                                                                class="form-control">
                    </div>
                </div>
                @php
                    $types = ['Web Dev','App Dev','Game Dev','Marketing','Design','Other'];
                    $statuses = $statuses ?? ['active', 'inactive', 'pending_approval'];
                @endphp
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Type *</label>
                        <select name="type" class="form-control">
                            @foreach ($types as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            @foreach ($statuses as $s)
                                <option value="{{ $s }}" {{ isset($project) && $project->status == $s ? 'selected' : '' }}>
                                    {{ Str::headline($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Total Budget (৳)</label><input type="number"
                                                                                                     name="budget"
                                                                                                     class="form-control"
                                                                                                     value="0"></div>
                    <div class="form-group"><label class="form-label">Received (৳)</label><input type="number"
                                                                                                 name="received"
                                                                                                 class="form-control"
                                                                                                 value="0"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Start Date</label><input type="date"
                                                                                               name="start_date"
                                                                                               class="form-control">
                    </div>
                    <div class="form-group"><label class="form-label">End Date</label><input type="date" name="end_date"
                                                                                             class="form-control"></div>
                </div>
                <div class="form-group"><label class="form-label">Progress (0-100%)</label><input type="number"
                                                                                                  name="progress"
                                                                                                  class="form-control"
                                                                                                  value="0" min="0"
                                                                                                  max="100"></div>
                <div class="form-group"><label class="form-label">Description</label><textarea name="description"
                                                                                               class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline"
                            onclick="document.getElementById('addModal').classList.remove('open')">Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Create Project</button>
                </div>
            </form>
        </div>
    </div>


@endsection
