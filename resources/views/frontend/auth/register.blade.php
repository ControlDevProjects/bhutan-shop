@extends('frontend.layouts.app')
@section('title','Create Account')
@section('content')
<div class="page-wrap" style="max-width:920px;margin:0 auto;">
<div style="display:grid;grid-template-columns:1fr 1fr;border-radius:var(--r2);overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.1);">
  <div style="background:linear-gradient(145deg,var(--pr),#8B1a14);padding:48px 40px;display:flex;flex-direction:column;justify-content:center;">
    <div style="font-size:48px;margin-bottom:16px;">🐉</div>
    <h1 style="color:#fff;font-size:26px;font-weight:800;line-height:1.2;margin-bottom:12px;">Join BhutanShop</h1>
    <p style="color:rgba(255,255,255,.8);font-size:14px;line-height:1.8;">Create your account and start shopping authentic Bhutanese products today.</p>
    <div style="margin-top:28px;padding:16px;background:rgba(255,255,255,.1);border-radius:var(--r2);">
      <div style="color:#fff;font-size:12px;line-height:1.8;opacity:.85;">Free registration · No spam · Secure checkout</div>
    </div>
  </div>
  <div style="background:#fff;padding:40px;">
    <h2 style="font-size:22px;font-weight:800;margin-bottom:6px;">Create Account</h2>
    <p style="color:var(--mut);font-size:13.5px;margin-bottom:24px;">Already have an account? <a href="{{ route('login') }}" style="color:var(--pr);font-weight:600;">Login</a></p>
    <form method="POST" action="{{ route('register.post') }}">
    @csrf
    <div class="fg"><label class="lbl">Full Name <span class="req">*</span></label><input name="name" class="fc" value="{{ old('name') }}" required placeholder="Your full name">@error('name')<div style="color:var(--err);font-size:12px;margin-top:3px;">{{ $message }}</div>@enderror</div>
    <div class="fg"><label class="lbl">Email <span class="req">*</span></label><input name="email" type="email" class="fc" value="{{ old('email') }}" required placeholder="you@example.com">@error('email')<div style="color:var(--err);font-size:12px;margin-top:3px;">{{ $message }}</div>@enderror</div>
    <div class="fg"><label class="lbl">Phone</label><input name="phone" class="fc" value="{{ old('phone') }}" placeholder="+975 XXXXXXXX"></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
      <div class="fg"><label class="lbl">Password <span class="req">*</span></label><input name="password" type="password" class="fc" required placeholder="Min 6 chars"></div>
      <div class="fg"><label class="lbl">Confirm <span class="req">*</span></label><input name="password_confirmation" type="password" class="fc" required placeholder="Repeat"></div>
    </div>
    <button type="submit" class="btn btn-pr btn-full" style="padding:12px;font-size:15px;"><i class="fas fa-user-plus"></i> Create Account</button>
    </form>
  </div>
</div>
</div>
@endsection
