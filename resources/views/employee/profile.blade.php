@extends('employee.header')

@section('title', 'My Profile')

@section('content')

    {{--  Page Header --}}
    <div class="page-header">
        <div>
            <h2>My Profile</h2>
            <p>View your information and documents</p>
        </div>
    </div>

    <div class="grid-2">

        <div>
            {{--  Personal Information --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Personal Information</span>
                </div>
                <div class="card-body">

                    {{--  Avatar / Photo --}}
                    <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border)">
                        @if($emp->photo)
                            <img src="{{ asset('storage/' . $emp->photo) }}"
                                 style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:3px solid var(--border);flex-shrink:0">
                        @else
                            <div class="avatar av-blue"
                                 style="width:60px;height:60px;font-size:20px;flex-shrink:0">
                                {{ strtoupper(substr($emp->name, 0, 2)) }}
                            </div>
                        @endif
                        <div>
                            <div style="font-size:18px;font-weight:700">{{ $emp->name }}</div>
                            <div class="text-muted">{{ $emp->designation }}</div>
                            <span class="badge badge-primary mt-4">{{ $emp->department }}</span>
                        </div>
                    </div>

                    {{--  Info Table --}}
                    <table style="width:100%;font-size:13px">
                        <tr style="border-bottom:1px solid var(--border)">
                            <td style="padding:8px 0;color:var(--text-muted);width:40%">Email</td>
                            <td style="padding:8px 0;font-weight:500">{{ $emp->email }}</td>
                        </tr>
                        <tr style="border-bottom:1px solid var(--border)">
                            <td style="padding:8px 0;color:var(--text-muted)">Phone</td>
                            <td style="padding:8px 0;font-weight:500">{{ $emp->phone ?: '—' }}</td>
                        </tr>
                        <tr style="border-bottom:1px solid var(--border)">
                            <td style="padding:8px 0;color:var(--text-muted)">NID</td>
                            <td style="padding:8px 0;font-weight:500">{{ $emp->nid ?: '—' }}</td>
                        </tr>
                        <tr style="border-bottom:1px solid var(--border)">
                            <td style="padding:8px 0;color:var(--text-muted)">Joined</td>
                            <td style="padding:8px 0;font-weight:500">
                                {{ $emp->join_date ? \Carbon\Carbon::parse($emp->join_date)->format('d M Y') : '—' }}
                            </td>
                        </tr>
                        <tr style="border-bottom:1px solid var(--border)">
                            <td style="padding:8px 0;color:var(--text-muted)">Salary</td>
                            <td style="padding:8px 0;font-weight:500">
                                ৳{{ number_format($emp->salary ?? 0) }}
                            </td>
                        </tr>
                        <tr style="border-bottom:1px solid var(--border)">
                            <td style="padding:8px 0;color:var(--text-muted)">Payment Via</td>
                            <td style="padding:8px 0;font-weight:500">
                                @php
                                    $pmL = [
                                        'cash'   => 'Cash',
                                        'bank'   => 'Bank Transfer',
                                        'bkash'  => 'bKash',
                                        'nagad'  => 'Nagad',
                                        'rocket' => 'Rocket',
                                    ];
                                @endphp
                                {{ $pmL[$emp->payment_method] ?? 'Cash' }}

                                @if($emp->payment_method === 'bank' && $emp->bank_name)
                                    <br>
                                    <span style="font-size:11px;color:var(--text-muted)">
                                    {{ $emp->bank_name }} · {{ $emp->bank_account }}
                                </span>
                                @elseif(in_array($emp->payment_method, ['bkash','nagad','rocket']) && $emp->mobile_banking_number)
                                    <br>
                                    <span style="font-size:11px;color:var(--text-muted)">
                                    {{ $emp->mobile_banking_number }}
                                </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:8px 0;color:var(--text-muted)">Address</td>
                            <td style="padding:8px 0;font-weight:500">{{ $emp->address ?: '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{--  Update Profile Form --}}
            <div class="card" style="margin-top:24px">
                <div class="card-header">
                    <span class="card-title">Update Profile</span>
                </div>

                {{--  Update phone & address --}}
                <form method="POST" action="{{ route('employee.profile.update') }}" class="card-body">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input name="phone" class="form-control"
                               value="{{ old('phone', $emp->phone) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control">{{ old('address', $emp->address) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>

                {{--  Update password --}}
                <form method="POST" action="{{ route('employee.profile.password') }}"
                      style="border-top:1px solid var(--border);padding:20px">
                    @csrf

                    <p style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;margin-bottom:12px">
                        Change Password
                    </p>

                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password"
                               class="form-control" placeholder="Leave blank to keep">
                        @error('current_password')
                        <span style="color:red;font-size:12px">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password"
                               class="form-control" placeholder="New password">
                        @error('new_password')
                        <span style="color:red;font-size:12px">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update Password</button>
                </form>
            </div>
        </div>

        {{--  My Documents --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">My Documents</span>
            </div>
            <div class="card-body">
                @forelse($docs as $doc)
                    <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border)">
                        <div style="flex:1">
                            <div class="fw-600" style="font-size:13px">{{ $doc->doc_name }}</div>
                            <div class="text-muted" style="font-size:11px">
                                {{ $doc->doc_type }} ·
                                {{ \Carbon\Carbon::parse($doc->uploaded_at)->format('d M Y') }}
                            </div>
                        </div>
                        <a class="btn btn-outline btn-sm"
                           href="{{ asset('storage/' . $doc->file_path) }}"
                           target="_blank">
                            Download
                        </a>
                    </div>
                @empty
                    <div class="empty-state">
                        No documents uploaded yet.<br>
                        <small>Ask your admin to upload your documents.</small>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

@endsection
