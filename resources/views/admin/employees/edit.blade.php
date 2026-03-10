@extends('admin.layouts.app')
@section('title','Edit Employee')
@section('page-title','Edit Employee')
@section('topbar-actions')<a href="{{ route('admin.employees.index') }}" class="btn btn-sc btn-sm"><i class="fas fa-arrow-left"></i> Back</a>@endsection
@section('content')
<div class="card" style="max-width:560px;">
<div class="card-hd"><h2>Edit: {{ $employee->name }}</h2></div>
<div class="card-bd">
<form method="POST" action="{{ route('admin.employees.update',$employee) }}">
@csrf @method('PUT')
<div class="fg"><label class="lbl">Full Name <span class="req">*</span></label><input name="name" class="fc" required value="{{ old('name',$employee->name) }}"></div>
<div class="g2">
    <div class="fg"><label class="lbl">Email <span class="req">*</span></label><input type="email" name="email" class="fc" required value="{{ old('email',$employee->email) }}"></div>
    <div class="fg"><label class="lbl">Phone</label><input name="phone" class="fc" value="{{ old('phone',$employee->phone) }}"></div>
</div>
<div class="g2">
    <div class="fg"><label class="lbl">Role</label><select name="role" class="fc"><option value="employee" {{ $employee->role==='employee'?'selected':'' }}>Employee</option><option value="admin" {{ $employee->role==='admin'?'selected':'' }}>Admin</option></select></div>
    <div class="fg"><label class="lbl">Status</label><select name="is_active" class="fc"><option value="1" {{ $employee->is_active?'selected':'' }}>Active</option><option value="0" {{ !$employee->is_active?'selected':'' }}>Inactive</option></select></div>
</div>
<div style="margin-bottom:14px;padding:12px;background:#fafafa;border-radius:6px;border:1px solid var(--bdr);font-size:13px;color:var(--mut);">Leave password blank to keep unchanged</div>
<div class="g2">
    <div class="fg"><label class="lbl">New Password</label><input type="password" name="password" class="fc" minlength="6"></div>
    <div class="fg"><label class="lbl">Confirm Password</label><input type="password" name="password_confirmation" class="fc"></div>
</div>
<button type="submit" class="btn btn-pr"><i class="fas fa-save"></i> Update</button>
</form>
</div>
</div>
@endsection
