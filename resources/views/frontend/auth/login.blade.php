@extends('frontend.layouts.app')
@section('title','Login')
@section('content')
<div class="page-wrap" style="max-width:920px;margin:0 auto;">
<div style="display:grid;grid-template-columns:1fr 1fr;border-radius:var(--r2);overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.1);">
  {{-- Left panel --}}
  <div style="background:var(--pr);padding:48px 40px;display:flex;flex-direction:column;justify-content:center;">
    <div style="font-size:48px;margin-bottom:16px;">🐉</div>
    <h1 style="color:#fff;font-size:28px;font-weight:800;line-height:1.2;margin-bottom:12px;">Welcome back to BhutanShop</h1>
    <p style="color:rgba(255,255,255,.8);font-size:14px;line-height:1.8;">Login to access your orders, wishlist, and exclusive deals from the Kingdom of Bhutan.</p>
    <div style="margin-top:32px;display:flex;flex-direction:column;gap:12px;">
      @foreach(['Track your orders in real-time','Fast checkout with saved addresses','Exclusive member deals'] as $perk)
      <div style="display:flex;align-items:center;gap:10px;color:rgba(255,255,255,.9);font-size:13px;">
        <div style="width:22px;height:22px;background:rgba(255,255,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;"><i class="fas fa-check"></i></div>
        {{ $perk }}
      </div>
      @endforeach
    </div>
  </div>
  {{-- Right: form --}}
  <div style="background:#fff;padding:48px 40px;">
    <h2 style="font-size:22px;font-weight:800;margin-bottom:6px;">Sign In</h2>
    <p style="color:var(--mut);font-size:13.5px;margin-bottom:28px;">Don't have an account? <a href="{{ route('register') }}" style="color:var(--pr);font-weight:600;">Register</a></p>
    <form method="POST" action="{{ route('login.post') }}">
    @csrf
    <div class="fg"><label class="lbl">Email Address</label><input name="email" type="email" class="fc" value="{{ old('email') }}" required autofocus placeholder="you@example.com">@error('email')<div style="color:var(--err);font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror</div>
    <div class="fg"><label class="lbl">Password</label><input name="password" type="password" class="fc" required placeholder="Your password"></div>
    <button type="submit" class="btn btn-pr btn-full" style="padding:12px;font-size:15px;margin-top:4px;"><i class="fas fa-sign-in-alt"></i> Login to BhutanShop</button>
    </form>
    <div style="margin-top:24px;padding-top:24px;border-top:1px solid var(--bdr);text-align:center;">
      <div style="font-size:12px;color:var(--mut);margin-bottom:10px;">Staff Access</div>
      <a href="{{ route('admin.dashboard') }}" style="font-size:13px;color:var(--pr);font-weight:600;text-decoration:none;"><i class="fas fa-cog"></i> Go to Admin Panel</a>
    </div>
  </div>
</div>
</div>
@endsection
