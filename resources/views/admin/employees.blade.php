
@extends('admin/header')
@section('content')

    <div class="page-header">
        <div><h2>Employees</h2><p>Manage team members, profiles, payment methods and documents</p></div>
        <button class="btn btn-primary" onclick="document.getElementById('addModal').classList.add('open')">+ Add Employee</button>
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('admin.employees') }}" style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap">
        <input name="search" value="{{$search}}" class="form-control" placeholder="Search name or email..." style="max-width:220px">
        <select name="dept" class="form-control" style="max-width:160px">
            <option value="">All Departments</option>
            @foreach($depts as $d)
                <option value="{{ $d }}" {{ $dept == $d ? 'selected' : '' }}>{{ $d }}</option>
            @endforeach
        </select>
        <select name="status" class="form-control" style="max-width:140px">
            @foreach($statuses as $s)
                <option value="{{ $s }}" {{ $status == $s ? 'selected' : '' }}>{{ Str::headline($s) }}</option>
            @endforeach
        </select>
        <button class="btn btn-outline" type="submit">Filter</button>
        <a class="btn btn-outline" href="{{ route('admin.employees') }}">Reset</a>
    </form>

    <!-- Employee Cards -->
    <div class="emp-cards">
        @foreach( $employees as $employee)
        <div class="emp-card">
            <!-- Photo / Avatar -->
            <div class="emp-card-header">
                <img src="{{ asset('storage/' . $employee->photo) }}" alt="photo" style="width:48px;height:48px;border-radius:50%;object-fit:cover;flex-shrink:0">
                <div class="avatar " style="width:48px;height:48px;font-size:16px"></div>
                <div>
                    <div class="fw-600" style="font-size:14px"> {{$employee-> name}} </div>
                    <div class="text-muted" style="font-size:11px">{{$employee->designation}}</div>
                </div>
            </div>
            <div class="emp-card-info">
                <div class="row"><span class="lbl">Dept</span><span><span class="badge badge-primary" style="font-size:10px"> {{$employee->department}}</span></span></div>
                <div class="row"><span class="lbl">Salary</span><span class="fw-600">৳ {{$employee->salary}}</span></div>
                <div class="row"><span class="lbl">Payment</span><span>{{$employee->payment_method}}
                <div style="font-size:10px;color:var(--text-muted)"> </div>
                <div style="font-size:10px;color:var(--text-muted)"></div>
                 </span></div>
                <div class="row"><span class="lbl">Phone</span><span>{{$employee->phone}}</span></div>
                <div class="row"><span class="lbl">Joined</span><span>{{$employee->join_date}}</span></div>
                @php
                    $bs = ['active' => 'success', 'on_leave' => 'warning', 'inactive' => 'gray'];
                @endphp
                <div class="row"><span class="lbl ">Status</span><span Class="badge-{{ $bs[$employee->status] ?? 'gray' }}"> {{ Str::headline($employee->status) }}</span></div>
            </div>
            <div class="emp-card-actions">
                <a class="btn btn-outline btn-sm" href="?edit={{$employee->id}}">Edit</a>
                <a class="btn btn-outline btn-sm" href="?docs={{$employee->id}}">Docs</a>
                <form method="POST" action="{{ route('admin.employees.destroy', $employee->id) }}"
                      onsubmit="return confirm('Delete this employee?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Del</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    <!-- ── ADD MODAL ─────────────────────────────────────────────────────────── -->
    <div class="modal-overlay" id="addModal">
        <div class="modal" style="max-width:680px">
            <div class="modal-header"><h3>Add New Employee</h3>
                <button class="modal-close" onclick="document.getElementById('addModal').classList.remove('open')">×</button></div>
            <form method="POST" action="{{ route('admin.employees.store') }}" enctype="multipart/form-data" class="modal-body">
                @method('POST')
                @csrf
                <input type="hidden" name="action" value="add">
                <p style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px">Basic Info</p>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Full Name *</label><input name="name" class="form-control" required></div>
                    <div class="form-group"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Password *</label><input type="password" name="password" class="form-control" required></div>
                    <div class="form-group"><label class="form-label">Phone</label><input name="phone" class="form-control"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Department *</label>
                        <select name="department" class="form-control" required>
                            @foreach($depts as $d)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Designation</label><input name="designation" class="form-control"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Salary (৳)</label><input type="number" name="salary" class="form-control" value="0"></div>
                    <div class="form-group"><label class="form-label">Join Date</label><input type="date" name="join_date" class="form-control"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">NID Number</label><input name="nid" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option><option value="on_leave">On Leave</option><option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Address</label><textarea name="address" class="form-control"></textarea></div>

                <!-- Profile Photo -->
                <p style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin:16px 0 12px;border-top:1px solid var(--border);padding-top:16px">Profile Photo</p>
                <div class="form-group"><label class="form-label">Upload Photo (JPG/PNG)</label><input type="file" name="photo" class="form-control" accept="image/*"></div>

                <!-- Payment Method -->
                <p style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin:16px 0 12px;border-top:1px solid var(--border);padding-top:16px">Payment Method</p>
                <div class="form-group">
                    <label class="form-label">Payment Via</label>
                    <select name="payment_method" class="form-control" id="add_pm" onchange="togglePayment(this,'add')">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="bkash">bKash</option>
                        <option value="nagad">Nagad</option>
                        <option value="rocket">Rocket</option>
                    </select>
                </div>
                <div id="add_bank" style="display:none">
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Bank Name</label><input name="bank_name" class="form-control"></div>
                        <div class="form-group"><label class="form-label">Account Number</label><input name="bank_account" class="form-control"></div>
                    </div>
                </div>
                <div id="add_mobile" style="display:none">
                    <div class="form-group"><label class="form-label">Mobile Banking Number</label><input name="mobile_banking_number" class="form-control" placeholder="01XXXXXXXXX"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('addModal').classList.remove('open')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Employee</button>
                </div>
            </form>
        </div></div>



    <!-- ── EDIT MODAL ─────────────────────────────────────────────────────────── -->
    <div class="modal-overlay {{ request()->has('edit') ? 'open' : '' }}" id="editModal">
        <div class="modal" style="max-width:680px">
            <div class="modal-header"><h3>Edit Employee</h3>
            <button class="modal-close" onclick="
                document.getElementById('editModal').classList.remove('open');
                window.history.replaceState({}, document.title, window.location.pathname);
            ">×</button></div>
            @if($editEmp)
            <form method="POST" action="{{route('admin.employees.update',$editEmp->id)}}" enctype="multipart/form-data" class="modal-body">
                @csrf
                @method('put')
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="">
                <p style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px">Basic Info</p>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Full Name</label><input name="name" class="form-control" value="{{ $editEmp->name }}" required></div>
                    <div class="form-group"><label class="form-label">Phone</label><input name="phone" class="form-control" value="{{ $editEmp->phone }}"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Department</label>
                        <select name="department" class="form-control">
                            @foreach($depts as $d)
                                <option value="{{ $d }}" {{ $editEmp->department == $d ? 'selected' : '' }}>
                                    {{ $d }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Designation</label><input name="designation" class="form-control" value="{{$editEmp->designation}}"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Salary (৳)</label><input type="number" name="salary" class="form-control" value="{{$editEmp->salary}}"></div>
                    <div class="form-group"><label class="form-label">Join Date</label><input type="date" name="join_date" class="form-control" value="{{ $editEmp->join_date ? \Carbon\Carbon::parse($editEmp->join_date)->format('Y-m-d') : '' }}"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">NID</label><input name="nid" class="form-control" value="{{$editEmp->nid}}"></div>
                    <div class="form-group"><label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            @foreach($statuses as $s)
                                <option value="{{ $s }}" {{ $editEmp->status == $s ? 'selected' : '' }}>
                                    {{ Str::headline($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Address</label><textarea name="address" class="form-control">{{$editEmp->address}}</textarea></div>

                <!-- Photo -->
                <p style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin:16px 0 12px;border-top:1px solid var(--border);padding-top:16px">Profile Photo</p>
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
                    @if($editEmp->photo)
                    <img src="{{ asset('storage/' . $employee->photo) }}" style="width:56px;height:56px;border-radius:50%;object-fit:cover;border:2px solid var(--border)" alt="">
                    @endif
                    <span style="font-size:12px;color:var(--text-muted)">Current photo. Upload new to replace.</span>
                </div>
                <div class="form-group"><label class="form-label">Upload New Photo (JPG/PNG)</label><input type="file" name="photo" class="form-control" accept="image/*"></div>

                <!-- Payment Method -->
                <p style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin:16px 0 12px;border-top:1px solid var(--border);padding-top:16px">Payment Method</p>
                <div class="form-group">
                    <label class="form-label">Payment Via</label>
                    <select name="payment_method" class="form-control" id="edit_pm" onchange="togglePayment(this,'edit')">
                        @foreach(['bank','bkash','nagad','rocket','cash'] as $pm)
                            <option value="{{ $pm }}" {{ $editEmp->payment_method == $pm ? 'selected' : '' }}>
                                {{ Str::headline($pm) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div id="edit_bank" style="">
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Bank Name</label><input name="bank_name" class="form-control" value=""></div>
                        <div class="form-group"><label class="form-label">Account Number</label><input name="bank_account" class="form-control" value=""></div>
                    </div>
                </div>
                <div id="edit_mobile" style="">
                    <div class="form-group"><label class="form-label">Mobile Banking Number</label><input name="mobile_banking_number" class="form-control" value="" placeholder="01XXXXXXXXX"></div>
                </div>

                <div class="modal-footer">
                    <a href="#" class="btn btn-outline" onclick="
                    document.getElementById('editModal').classList.remove('open');
                    window.history.replaceState({}, document.title, window.location.pathname);
                ">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
            @endif
        </div></div>

    <!-- ── DOCUMENTS MODAL ───────────────────────────────────────────────────── -->
    @if(isset($employee))
    <div class="modal-overlay {{ request()->has('docs') ? 'open' : '' }}">
        <div class="modal">
            <div class="modal-header"><h3>Documents — {{$employee->name}}</h3><a class="modal-close" href="{{route('admin.employees')}}">×</a></div>
            <div class="modal-body">
                <form method="POST" {{ route('admin.documents.store',$employee->id) }} enctype="multipart/form-data" style="margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border)">
                    @csrf
                    @method('put')
                    <input type="hidden" name="upload_doc" value="1">
                    <input type="hidden" name="employee_id" value="">
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Document Name</label><input name="doc_name" class="form-control" required></div>
                        <div class="form-group"><label class="form-label">Type</label>
                            <select name="doc_type" class="form-control"><option>NID</option><option>CV</option><option>Contract</option><option>Certificate</option><option>Other</option></select>
                        </div>
                    </div>
                    <div class="form-group"><label class="form-label">File</label><input type="file" name="doc_file" class="form-control" required></div>
                    <button type="submit" class="btn btn-primary btn-sm">Upload Document</button>
                </form>
                <table class="data-table">
                    <thead><tr><th>Document</th><th>Type</th><th>Uploaded</th><th>Action</th></tr></thead>
                    <tbody>
                    <tr>
                        <td></td>
                        <td><span class="badge badge-gray"></span></td>
                        <td></td>
                        <td><a class="btn btn-outline btn-xs" href="" target="_blank">Download</a></td>
                    </tr>
{{--                    @if(!$hasDocs)--}}
{{--                        <tr>--}}
{{--                            <td colspan="4">--}}
{{--                                <div class="empty-state">No documents yet.</div>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                    @endif--}}

                    </tbody>
                </table>
            </div>
        </div></div>
    @endif
    <script>
        function togglePayment(sel, prefix) {
            const val = sel.value;
            document.getElementById(prefix+'_bank').style.display   = (val === 'bank')   ? 'block' : 'none';
            document.getElementById(prefix+'_mobile').style.display = (['bkash','nagad','rocket'].includes(val)) ? 'block' : 'none';
        }
    </script>
    @if(session('modal_closed'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('editModal').classList.remove('open');
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        </script>
    @endif

@endsection
