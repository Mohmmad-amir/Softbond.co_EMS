@extends('employee.header')

@section('title', 'My Attendance')

@section('content')

    {{--  Page Header --}}
    <div class="page-header">
        <div>
            <h2>My Attendance</h2>
            <p>Your attendance record for {{ $monthLabel }}</p>
        </div>
        <form method="GET" action="{{ route('employee.attendance.index') }}"
              style="display:flex;gap:8px;align-items:center">
            <input type="month" name="month" class="form-control"
                   style="width:auto" value="{{ $month }}">
            <button type="submit" class="btn btn-outline btn-sm">Go</button>
        </form>
    </div>

    {{--  Metrics --}}
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-label">Present</div>
            <div class="metric-val" style="color:var(--success)">{{ $present }}</div>
            <div class="metric-sub">days</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Absent</div>
            <div class="metric-val" style="color:var(--danger)">{{ $absent }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">On Leave</div>
            <div class="metric-val" style="color:var(--warning)">{{ $onLeave }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Half Day</div>
            <div class="metric-val" style="color:var(--info)">{{ $halfDay }}</div>
        </div>
    </div>

    {{--  Calendar View --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Calendar — {{ $monthLabel }}</span>
        </div>
        <div class="card-body">

            {{-- Day Labels --}}
            <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:6px;text-align:center;margin-bottom:8px">
                @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
                    <div style="font-size:11px;font-weight:600;color:var(--text-muted);padding:4px">
                        {{ $d }}
                    </div>
                @endforeach
            </div>

            {{-- Calendar Grid --}}
            <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:6px;text-align:center">

                {{-- Empty cells before first day --}}
                @for($i = 0; $i < $firstDay; $i++)
                    <div></div>
                @endfor

                {{-- Days --}}
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateStr  = sprintf('%04d-%02d-%02d', $y, $m, $day);
                        $att      = $attMap[$dateStr] ?? null;
                        $dow      = \Carbon\Carbon::parse($dateStr)->dayOfWeek;
                        $isWeekend = ($dow === 5 || $dow === 6); // Fri/Sat BD weekend
                        $isToday  = $dateStr === now()->format('Y-m-d');

                        //  Background and text colors
                        $bg = '#f8fafc';
                        $tc = 'var(--text-muted)';

                        if ($att) {
                            $colors = [
                                'present'  => ['#f0fdf4', '#16a34a'],
                                'absent'   => ['#fef2f2', '#dc2626'],
                                'on_leave' => ['#fffbeb', '#d97706'],
                                'half_day' => ['#ecfeff', '#0891b2'],
                            ];
                            [$bg, $tc] = $colors[$att->status] ?? ['#f8fafc', 'var(--text)'];
                        } elseif ($isWeekend) {
                            $bg = '#f1f5f9';
                            $tc = '#94a3b8';
                        }
                    @endphp

                    <div style="padding:6px 2px;border-radius:6px;background:{{ $bg }};color:{{ $tc }};font-size:12px;font-weight:{{ $isToday ? '700' : '500' }};border:{{ $isToday ? '2px solid #2563eb' : '1px solid #e2e8f0' }}">
                        {{ $day }}
                        @if($att)
                            <div style="font-size:9px;margin-top:1px">
                                {{ Str::headline($att->status) }}
                            </div>
                        @endif
                        @if($isWeekend && !$att)
                            <div style="font-size:9px">Off</div>
                        @endif
                    </div>
                @endfor

            </div>

            {{--  Legend --}}
            <div style="display:flex;gap:16px;margin-top:16px;flex-wrap:wrap;font-size:12px">
            <span style="display:flex;align-items:center;gap:4px">
                <span style="width:12px;height:12px;border-radius:3px;background:#f0fdf4;border:1px solid #16a34a;display:inline-block"></span>
                Present
            </span>
                <span style="display:flex;align-items:center;gap:4px">
                <span style="width:12px;height:12px;border-radius:3px;background:#fef2f2;border:1px solid #dc2626;display:inline-block"></span>
                Absent
            </span>
                <span style="display:flex;align-items:center;gap:4px">
                <span style="width:12px;height:12px;border-radius:3px;background:#fffbeb;border:1px solid #d97706;display:inline-block"></span>
                Leave
            </span>
                <span style="display:flex;align-items:center;gap:4px">
                <span style="width:12px;height:12px;border-radius:3px;background:#ecfeff;border:1px solid #0891b2;display:inline-block"></span>
                Half Day
            </span>
                <span style="display:flex;align-items:center;gap:4px">
                <span style="width:12px;height:12px;border-radius:3px;background:#f1f5f9;border:1px solid #e2e8f0;display:inline-block"></span>
                Weekend/Off
            </span>
            </div>
        </div>
    </div>

    {{--  Detailed Log --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Detailed Log</span>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Status</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Hours</th>
                </tr>
                </thead>
                <tbody>
                @forelse($attMap as $dateStr => $a)
                    @php
                        $sb    = ['present' => 'success', 'absent' => 'danger', 'on_leave' => 'warning', 'half_day' => 'info'];
                        $hours = '';
                        if ($a->check_in && $a->check_out) {
                            $diff  = strtotime($a->check_out) - strtotime($a->check_in);
                            $hours = round($diff / 3600, 1) . 'h';
                        }
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($dateStr)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($dateStr)->format('l') }}</td>
                        <td>
                        <span class="badge badge-{{ $sb[$a->status] ?? 'gray' }}">
                            {{ Str::headline($a->status) }}
                        </span>
                        </td>
                        <td>
                            {{ $a->check_in ? \Carbon\Carbon::parse($a->check_in)->format('h:i A') : '—' }}
                        </td>
                        <td>
                            {{ $a->check_out ? \Carbon\Carbon::parse($a->check_out)->format('h:i A') : '—' }}
                        </td>
                        <td>{{ $hours ?: '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">No attendance records found.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
