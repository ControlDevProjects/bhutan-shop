@extends('frontend.layouts.app')
@section('title','My Profile')
@section('content')
<div class="breadcrumb"><a href="{{ route('home') }}">Home</a><span class="sep">/</span><span>My Profile</span></div>
<h1 style="font-size:22px;font-weight:700;margin-bottom:20px;">👤 My Profile</h1>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;">
<div class="card">
<div class="card-hd"><h2>Personal Information</h2></div>
<div class="card-bd">
<form method="POST" action="{{ route('profile.update') }}">
@csrf @method('PUT')
<div class="fg"><label class="lbl">Full Name <span class="req">*</span></label><input name="name" class="fc" value="{{ old('name',auth()->user()->name) }}" required></div>
<div class="fg"><label class="lbl">Email</label><input class="fc" value="{{ auth()->user()->email }}" disabled style="background:#f5f5f5;color:var(--mut);"></div>
<div class="fg"><label class="lbl">Phone</label><input name="phone" class="fc" value="{{ old('phone',auth()->user()->phone) }}" placeholder="+975 XXXXXXXX"></div>
<div class="fg"><label class="lbl">Address</label><textarea name="address" class="fc" rows="2">{{ old('address',auth()->user()->address) }}</textarea></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
<div class="fg"><label class="lbl">City</label><input name="city" class="fc" value="{{ old('city',auth()->user()->city) }}"></div>
<div class="fg"><label class="lbl">Dzongkhag</label><select name="dzongkhag" class="fc"><option value="">— Select —</option>@foreach(['Bumthang','Chhukha','Dagana','Gasa','Haa','Lhuntse','Monggar','Paro','Pemagatshel','Punakha','Samdrup Jongkhar','Samtse','Sarpang','Thimphu','Trashigang','Trashiyangtse','Trongsa','Tsirang','Wangdue Phodrang','Zhemgang'] as $dz)<option value="{{ $dz }}" {{ old('dzongkhag',auth()->user()->dzongkhag)===$dz?'selected':'' }}>{{ $dz }}</option>@endforeach</select></div>
</div>
<button type="submit" class="btn btn-pr"><i class="fas fa-save"></i> Save Changes</button>
</form>
</div>
</div>
<div>
<div class="card mb16">
<div class="card-hd"><h2>Change Password</h2></div>
<div class="card-bd">
<form method="POST" action="{{ route('profile.update') }}">
@csrf @method('PUT')
<input type="hidden" name="update_type" value="password">
<div class="fg"><label class="lbl">Current Password <span class="req">*</span></label><input type="password" name="current_password" class="fc" required></div>
<div class="fg"><label class="lbl">New Password <span class="req">*</span></label><input type="password" name="password" class="fc" required></div>
<div class="fg"><label class="lbl">Confirm New Password <span class="req">*</span></label><input type="password" name="password_confirmation" class="fc" required></div>
<button type="submit" class="btn btn-sc"><i class="fas fa-key"></i> Update Password</button>
</form>
</div>
</div>
<div class="card">
<div class="card-hd"><h2>Account</h2></div>
<div class="card-bd" style="font-size:13px;line-height:2.2;">
    <div><span style="color:var(--mut);">Joined:</span> <strong>{{ auth()->user()->created_at->format('d M Y') }}</strong></div>
    <div><span style="color:var(--mut);">Role:</span> <strong>{{ ucfirst(auth()->user()->role) }}</strong></div>
    <div style="margin-top:10px;display:flex;gap:8px;">
        <a href="{{ route('orders.index') }}" class="btn btn-sc btn-sm"><i class="fas fa-box"></i> My Orders</a>
        <a href="{{ route('cart.index') }}" class="btn btn-sc btn-sm"><i class="fas fa-shopping-cart"></i> My Cart</a>
    </div>
</div>
</div>
</div>
</div>
@endsection
