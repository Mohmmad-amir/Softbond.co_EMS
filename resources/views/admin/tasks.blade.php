@extends('admin/header')
@section('content')
    {{-- ✅ Success Message --}}
    @if(session('success'))
        <div class="alert alert-success" id="successAlert">
            {{ session('success') }}
            <script>
                setTimeout(() => {
                    document.getElementById('successAlert').style.opacity = '0';
                }, 3000);
            </script>
        </div>
    @endif

    {{-- ✅ Page Header --}}
    <div class="page-header">
        <div><h2>Tasks</h2><p>Assign and manage project tasks</p></div>
        <button class="btn btn-primary"
                onclick="document.getElementById('addModal').classList.add('open')">
            + Assign Task
        </button>
    </div>

    {{-- ✅ Filter Form --}}
    <form method="GET" action="{{ route('admin.tasks.index') }}"
          style="display:flex;gap:10px;margin-bottom:20px">

        <select name="project" class="form-control" style="max-width:200px">
            <option value="">All Projects</option>
            @foreach($pArr as $id => $name)
                <option value="{{ $id }}" {{ $filterProj == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>

        <select name="employee" class="form-control" style="max-width:200px">
            <option value="">All Employees</option>
            @foreach($eArr as $id => $name)
                <option value="{{ $id }}" {{ $filterEmp == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-outline">Filter</button>
        <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline">Reset</a>
    </form>

    {{-- ✅ Tasks Table --}}
    <div class="card">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                <tr>
                    <th>Task</th>
                    <th>Project</th>
                    <th>Assigned To</th>
                    <th>Due Date</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($tasks as $t)
                    @php
                        $pb = ['low' => 'gray', 'medium' => 'warning', 'high' => 'danger'];
                    @endphp
                    <tr>
                        <td class="fw-600">{{ $t->title }}</td>
                        <td>{{ $t->project->name ?? '—' }}</td>
                        <td>{{ $t->employee->name ?? '—' }}</td>
                        <td>{{ $t->due_date ? $t->due_date->format('d M Y') : '—' }}</td>
                        <td>
                        <span class="badge badge-{{ $pb[$t->priority] ?? 'gray' }}">
                            {{ ucfirst($t->priority) }}
                        </span>
                        </td>
                        <td>
                            {{-- ✅ Quick status update --}}
                            <form method="POST"
                                  action="{{ route('admin.tasks.status', $t->id) }}">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-control"
                                        style="width:120px;font-size:12px"
                                        onchange="this.form.submit()">
                                    <option value="pending"     {{ $t->status === 'pending'     ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $t->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="done"        {{ $t->status === 'done'        ? 'selected' : '' }}>Done</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <div style="display:flex;gap:4px">
                                {{-- ✅ Edit --}}
                                <a href="?edit={{ $t->id }}" class="btn btn-outline btn-xs">Edit</a>

                                {{-- ✅ Delete --}}
                                <form method="POST"
                                      action="{{ route('admin.tasks.destroy', $t->id) }}"
                                      onsubmit="return confirm('Delete this task?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs">Del</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center">No tasks found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ✅ Add Modal --}}
    <div class="modal-overlay" id="addModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Assign New Task</h3>
                <button class="modal-close"
                        onclick="document.getElementById('addModal').classList.remove('open')">×</button>
            </div>
            <form method="POST" action="{{ route('admin.tasks.store') }}" class="modal-body">
                @csrf

                <div class="form-group">
                    <label class="form-label">Task Title *</label>
                    <input name="title" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Project *</label>
                        <select name="project_id" class="form-control" required>
                            <option value="">Select Project</option>
                            @foreach($pArr as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Assign To *</label>
                        <select name="assigned_to" class="form-control" required>
                            <option value="">Select Employee</option>
                            @foreach($eArr as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-control">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="done">Done</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline"
                            onclick="document.getElementById('addModal').classList.remove('open')">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Assign Task</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ✅ Edit Modal --}}
    @if($editTask)
        <div class="modal-overlay open" id="editModal">
            <div class="modal">
                <div class="modal-header">
                    <h3>Edit Task</h3>
                    <a class="modal-close" href="{{ route('admin.tasks.index') }}">×</a>
                </div>
                <form method="POST" action="{{ route('admin.tasks.update', $editTask->id) }}" class="modal-body">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label">Title</label>
                        <input name="title" class="form-control" value="{{ $editTask->title }}" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Project</label>
                            <select name="project_id" class="form-control">
                                @foreach($pArr as $id => $name)
                                    <option value="{{ $id }}" {{ $editTask->project_id == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Assigned To</label>
                            <select name="assigned_to" class="form-control">
                                @foreach($eArr as $id => $name)
                                    <option value="{{ $id }}" {{ $editTask->assigned_to == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control"
                                   value="{{ $editTask->due_date ? $editTask->due_date->format('Y-m-d') : '' }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-control">
                                @foreach(['low', 'medium', 'high'] as $p)
                                    <option value="{{ $p }}" {{ $editTask->priority === $p ? 'selected' : '' }}>
                                        {{ ucfirst($p) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            @foreach(['pending', 'in_progress', 'done'] as $s)
                                <option value="{{ $s }}" {{ $editTask->status === $s ? 'selected' : '' }}>
                                    {{ Str::headline($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control">{{ $editTask->description }}</textarea>
                    </div>

                    <div class="modal-footer">
                        <a class="btn btn-outline" href="{{ route('admin.tasks.index') }}">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection
