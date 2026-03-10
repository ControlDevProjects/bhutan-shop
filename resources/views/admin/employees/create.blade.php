@extends('admin.layouts.app')
@section('title','New Employee')
@section('page-title','Add Employee')
@section('topbar-actions')<a href="{{ route('admin.employees.index') }}" class="btn btn-sc btn-sm"><i class="fas fa-arrow-left"></i> Back</a>@endsection
@section('content')
<div class="card" style="max-width:560px;">
<div class="card-hd"><h2>New Employee / Admin Account</h2></div>
<div class="card-bd">
<form method="POST" action="{{ route('admin.employees.store') }}">
@csrf
<div class="fg"><label class="lbl">Full Name <span class="req">*</span></label><input name="name" class="fc" required value="{{ old('name') }}"></div>
<div class="g2">
    <div class="fg"><label class="lbl">Email <span class="req">*</span></label><input type="email" name="email" class="fc" required value="{{ old('email') }}"></div>
    <div class="fg"><label class="lbl">Phone</label><input name="phone" class="fc" value="{{ old('phone') }}"></div>
</div>
<div class="g2">
    <div class="fg"><label class="lbl">Role <span class="req">*</span></label><select name="role" class="fc" required><option value="employee" {{ old('role')==='employee'?'selected':'' }}>Employee</option><option value="admin" {{ old('role')==='admin'?'selected':'' }}>Admin</option></select></div>
    <div class="fg"><label class="lbl">Status</label><select name="is_active" class="fc"><option value="1">Active</option><option value="0">Inactive</option></select></div>
</div>
<div class="g2">
    <div class="fg"><label class="lbl">Password <span class="req">*</span></label><input type="password" name="password" class="fc" required minlength="6"></div>
    <div class="fg"><label class="lbl">Confirm Password <span class="req">*</span></label><input type="password" name="password_confirmation" class="fc" required></div>
</div>
<button type="submit" class="btn btn-pr"><i class="fas fa-save"></i> Create Account</button>
</form>
</div>
</div>
@endsection
