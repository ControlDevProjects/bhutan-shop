@extends('frontend.layouts.app')
@section('title','Shopping Cart')
@section('content')
<div class="page-wrap">
<div class="breadcrumb"><a href="{{ route('home') }}">Home</a><span class="sep">/</span><span>Cart</span></div>
<h1 style="font-size:20px;font-weight:800;margin-bottom:16px;">Shopping Cart ({{ !empty($items) ? array_sum(array_column($items,'qty')).' items' : 'Empty' }})</h1>

@if(empty($items))
<div class="empty-state">
  <div class="icon">🛒</div>
  <h2>Your cart is empty</h2>
  <p>Add items to your cart to continue shopping.</p>
  <a href="{{ route('products.index') }}" class="btn btn-pr" style="margin-top:18px;"><i class="fas fa-store"></i> Browse Products</a>
</div>
@else
<div style="display:grid;grid-template-columns:1fr 340px;gap:16px;align-items:start;">

{{-- Items --}}
<div>
@foreach($items as $key => $item)
<div style="display:flex;gap:14px;padding:16px;background:var(--card);border:1px solid var(--bdr);border-radius:var(--r2);margin-bottom:10px;align-items:flex-start;">
  <a href="#" style="width:80px;height:80px;border-radius:var(--r);overflow:hidden;border:1px solid var(--bdr);flex-shrink:0;background:#f5f5f5;display:flex;align-items:center;justify-content:center;">
    @if($item['image'])<img src="{{ asset('storage/'.$item['image']) }}" style="width:100%;height:100%;object-fit:cover;">
    @else<i class="fas fa-image" style="color:#ddd;font-size:24px;"></i>@endif
  </a>
  <div style="flex:1;">
    <div style="font-weight:700;font-size:14px;margin-bottom:3px;">{{ $item['name'] }}</div>
    @if($item['variant_name'])<div style="font-size:12px;color:var(--mut);margin-bottom:3px;"><span class="chip chip-pr">{{ $item['variant_name'] }}</span></div>@endif
    @if($item['sku'])<div style="font-size:11px;color:var(--mut);font-family:monospace;">SKU: {{ $item['sku'] }}</div>@endif
    <div style="font-size:16px;font-weight:800;color:var(--pr);margin-top:6px;">BTN {{ number_format($item['price'],2) }}</div>
    <div style="margin-top:10px;display:flex;align-items:center;gap:12px;">
      <form method="POST" action="{{ route('cart.update',$key) }}" style="display:flex;align-items:center;">
        @csrf @method('PUT')
        <div class="qty-ctrl">
          <button type="submit" name="qty" value="{{ max(1,$item['qty']-1) }}" style="width:32px;height:34px;border:none;background:#f5f5f5;font-size:16px;font-weight:700;cursor:pointer;" {{ $item['qty']<=1?'disabled':'' }}>−</button>
          <span style="width:40px;text-align:center;font-size:14px;font-weight:700;display:inline-block;padding:6px 0;border-left:1.5px solid var(--bdr);border-right:1.5px solid var(--bdr);">{{ $item['qty'] }}</span>
          <button type="submit" name="qty" value="{{ $item['qty']+1 }}" style="width:32px;height:34px;border:none;background:#f5f5f5;font-size:16px;font-weight:700;cursor:pointer;">+</button>
        </div>
      </form>
      <form method="POST" action="{{ route('cart.remove',$key) }}">
        @csrf @method('DELETE')
        <button type="submit" style="background:none;border:none;color:var(--err);cursor:pointer;font-size:12.5px;font-weight:600;display:flex;align-items:center;gap:5px;padding:5px 8px;border-radius:var(--r);transition:.15s;" onmouseover="this.style.background='var(--err-lt)'" onmouseout="this.style.background='none'">
          <i class="fas fa-trash-alt"></i> Remove
        </button>
      </form>
    </div>
  </div>
  <div style="font-size:16px;font-weight:800;color:var(--txt);white-space:nowrap;margin-top:4px;">BTN {{ number_format($item['price']*$item['qty'],2) }}</div>
</div>
@endforeach
<a href="{{ route('products.index') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--mut);font-size:13px;text-decoration:none;margin-top:4px;"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
</div>

{{-- Summary --}}
<div>
<div class="card">
  <div class="card-hd"><h2>Price Details</h2></div>
  <div class="card-bd">
    <div style="display:flex;justify-content:space-between;font-size:13.5px;margin-bottom:10px;">
      <span>Subtotal ({{ array_sum(array_column($items,'qty')) }} items)</span>
      <span>BTN {{ number_format($subtotal,2) }}</span>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:13.5px;margin-bottom:14px;">
      <span>Delivery Charges</span>
      @if($shipping==0)
        <span style="color:var(--ok);font-weight:700;">FREE</span>
      @else
        <span>BTN {{ number_format($shipping,2) }}</span>
      @endif
    </div>
    @if($shipping>0)
    <div style="font-size:12px;color:var(--info);background:var(--info-lt);border-radius:var(--r);padding:7px 10px;margin-bottom:14px;">
      <i class="fas fa-info-circle"></i> Add BTN {{ number_format(5000-$subtotal,2) }} more for free delivery
    </div>
    @else
    <div style="font-size:12px;color:var(--ok);background:var(--ok-lt);border-radius:var(--r);padding:7px 10px;margin-bottom:14px;">
      <i class="fas fa-check-circle"></i> You've unlocked free delivery! 🎉
    </div>
    @endif
    <div style="border-top:2px dashed var(--bdr);padding-top:14px;margin-bottom:14px;">
      <div style="display:flex;justify-content:space-between;font-size:17px;font-weight:800;">
        <span>Total Amount</span>
        <span style="color:var(--pr);">BTN {{ number_format($subtotal+$shipping,2) }}</span>
      </div>
      <div style="font-size:11.5px;color:var(--ok);margin-top:5px;font-weight:600;"><i class="fas fa-tag"></i> Inclusive of all taxes</div>
    </div>
    @auth
      <a href="{{ route('checkout.index') }}" class="btn btn-pr btn-full" style="padding:12px;font-size:15px;"><i class="fas fa-lock"></i> Proceed to Checkout</a>
    @else
      <a href="{{ route('login') }}" class="btn btn-pr btn-full" style="padding:12px;font-size:15px;"><i class="fas fa-sign-in-alt"></i> Login to Checkout</a>
      <div style="text-align:center;margin-top:8px;font-size:12.5px;color:var(--mut);">New user? <a href="{{ route('register') }}" style="color:var(--pr);font-weight:600;">Register here</a></div>
    @endauth
  </div>
</div>
<div style="margin-top:12px;background:var(--card);border:1px solid var(--bdr);border-radius:var(--r2);padding:12px 14px;display:flex;flex-direction:column;gap:8px;">
  <div style="font-size:12px;display:flex;align-items:center;gap:8px;color:var(--txt2);"><i class="fas fa-shield-alt" style="color:var(--ok);"></i> Safe and Secure Payments</div>
  <div style="font-size:12px;display:flex;align-items:center;gap:8px;color:var(--txt2);"><i class="fas fa-undo" style="color:var(--info);"></i> Easy Returns & Refunds</div>
</div>
</div>
</div>
@endif
</div>
@endsection