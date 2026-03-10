@extends('admin.layouts.app')
@section('title','Employees')
@section('page-title','Employee Management')
@section('topbar-actions')
    <a href="{{ route('admin.employees.create') }}" class="btn btn-pr btn-sm"><i class="fas fa-plus"></i> Add Employee</a>
@endsection
@section('content')
<div class="card">
<div class="tw"><table>
<thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Assigned Orders</th><th>Actions</th></tr></thead>
<tbody>
@forelse($employees as $emp)
<tr>
    <td>
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:34px;height:34px;border-radius:50%;background:var(--pr);color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($emp->name,0,1)) }}</div>
            <div><div style="font-weight:600;">{{ $emp->name }}</div><div style="font-size:11px;color:var(--mut);">Joined {{ $emp->created_at->format('d M Y') }}</div></div>
        </div>
    </td>
    <td style="font-size:13px;">{{ $emp->email }}</td>
    <td style="font-size:13px;">{{ $emp->phone ?? '—' }}</td>
    <td><span class="badge {{ $emp->role==='admin'?'b-err':'b-info' }}">{{ ucfirst($emp->role) }}</span></td>
    <td><span class="badge {{ $emp->is_active?'b-ok':'b-err' }}">{{ $emp->is_active?'Active':'Inactive' }}</span></td>
    <td>{{ $emp->assignedOrders()->count() }}</td>
    <td>
        <div style="display:flex;gap:4px;">
            <a href="{{ route('admin.employees.edit',$emp) }}" class="btn btn-sc btn-xs"><i class="fas fa-edit"></i></a>
            @if($emp->id !== auth()->id())
            <form method="POST" action="{{ route('admin.employees.destroy',$emp) }}" style="display:inline;">@csrf @method('DELETE')
                <button class="btn btn-err btn-xs" data-confirm="Delete employee '{{ $emp->name }}'?"><i class="fas fa-trash"></i></button>
            </form>
            @endif
        </div>
    </td>
</tr>
@empty
<tr><td colspan="7" style="text-align:center;padding:30px;color:var(--mut);">No employees found.</td></tr>
@endforelse
</tbody>
</table></div>
</div>
@endsection
