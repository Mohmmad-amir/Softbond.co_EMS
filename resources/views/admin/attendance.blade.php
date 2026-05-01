
@extends('admin/header')
@section('content')

    {{--  Page Header --}}
    <div class="page-header">
        <div>
            <h2>Attendance</h2>
            <p>Track daily attendance for all employees</p>
        </div>
    </div>

    {{--  Metrics --}}
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-label">Total Employees</div>
            <div class="metric-val">{{ $total }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Present</div>
            <div class="metric-val" style="color:var(--success)">{{ $present }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">On Leave</div>
            <div class="metric-val" style="color:var(--warning)">{{ $onLeave }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Absent</div>
            <div class="metric-val" style="color:var(--danger)">{{ $absent }}</div>
        </div>
    </div>

    {{--  Attendance Card --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Attendance Log</span>

            {{--  Date Filter --}}
            <form method="GET" action="{{ route('admin.attendance.index') }}"
                  style="display:flex;gap:8px;align-items:center">
                <input type="date" name="date" class="form-control"
                       style="width:auto" value="{{ $date }}">
                <button type="submit" class="btn btn-outline btn-sm">Load</button>
            </form>
        </div>

        {{--  Attendance Form --}}
        <form method="POST" action="{{ route('admin.attendance.store') }}">
            @csrf
            <input type="hidden" name="att_date" value="{{ $date }}">

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($employees as $emp)
                        @php
                            //  existing attendance data
                            $a  = $attendances[$emp->id] ?? [];
                            $st = $a['status'] ?? 'absent';
                        @endphp
                        <tr>
                            <td>
                                <div class="avatar-row">
                                    <div class="avatar av-blue">
                                        {{ strtoupper(substr($emp->name, 0, 1)) }}
                                    </div>
                                    <div class="info">
                                        <div class="name">{{ $emp->name }}</div>
                                        <div class="sub">{{ $emp->designation }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $emp->department }}</td>
                            <td>
                                <select name="att[{{ $emp->id }}][status]"
                                        class="form-control" style="width:130px">
                                    @foreach(['present' => 'Present', 'absent' => 'Absent', 'on_leave' => 'On Leave', 'half_day' => 'Half Day'] as $val => $label)
                                        <option value="{{ $val }}" {{ $st === $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="time"
                                       name="att[{{ $emp->id }}][check_in]"
                                       class="form-control"
                                       style="width:120px"
                                       value="{{ $a['check_in'] ?? '' }}">
                            </td>
                            <td>
                                <input type="time"
                                       name="att[{{ $emp->id }}][check_out]"
                                       class="form-control"
                                       style="width:120px"
                                       value="{{ $a['check_out'] ?? '' }}">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center">
                                No active employees found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div style="padding:16px 20px;border-top:1px solid var(--border)">
                <button type="submit" class="btn btn-primary">Save Attendance</button>
            </div>
        </form>
    </div>
@endsection
