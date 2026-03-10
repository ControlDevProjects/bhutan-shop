@extends('frontend.layouts.app')
@section('title','My Orders')
@section('content')
<div class="page-wrap">
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
  <h1 style="font-size:20px;font-weight:800;">📦 My Orders</h1>
  <a href="{{ route('profile.edit') }}" style="font-size:13px;color:var(--mut);text-decoration:none;display:flex;align-items:center;gap:6px;"><i class="fas fa-user-cog"></i> My Profile</a>
</div>

@if($orders->isEmpty())
<div class="empty-state">
  <div class="icon">📭</div>
  <h2>No orders yet</h2>
  <p>You haven't placed any orders. Start shopping!</p>
  <a href="{{ route('products.index') }}" class="btn btn-pr" style="margin-top:16px;"><i class="fas fa-store"></i> Shop Now</a>
</div>
@else
<div style="display:flex;flex-direction:column;gap:12px;">
@foreach($orders as $order)
@php $sc=['pending'=>'#e65100','confirmed'=>'#1565c0','processing'=>'#6a1b9a','packed'=>'#4a148c','shipped'=>'#0d47a1','out_for_delivery'=>'#0277bd','delivered'=>'#2e7d32','cancelled'=>'#c62828','returned'=>'#c62828']; @endphp
<a href="{{ route('orders.show',$order) }}" style="text-decoration:none;">
<div class="card" style="transition:.2s;" onmouseover="this.style.borderColor='var(--pr)'" onmouseout="this.style.borderColor='var(--bdr)'">
  <div class="card-bd">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:10px;">
      <div>
        <div style="font-weight:800;font-size:15px;color:var(--pr);font-family:monospace;">{{ $order->order_number }}</div>
        <div style="font-size:12px;color:var(--mut);margin-top:2px;"><i class="fas fa-clock"></i> {{ $order->created_at->format('d M Y, h:i A') }}</div>
        <div style="font-size:13px;color:var(--txt2);margin-top:6px;">{{ $order->items->count() }} item(s) · BTN {{ number_format($order->total,2) }}</div>
      </div>
      <div style="text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:5px;">
        <span style="background:{{ $sc[$order->status]??'#888' }}15;color:{{ $sc[$order->status]??'#888' }};border:1.5px solid {{ $sc[$order->status]??'#888' }}40;border-radius:20px;padding:4px 12px;font-size:12px;font-weight:700;">
          {{ ucwords(str_replace('_',' ',$order->status)) }}
        </span>
        <span style="background:{{ $order->payment_status==='paid'?'#2e7d3215':'#e6510015' }};color:{{ $order->payment_status==='paid'?'#2e7d32':'#e65100' }};border:1px solid {{ $order->payment_status==='paid'?'#2e7d3240':'#e6510040' }};border-radius:20px;padding:2px 10px;font-size:11px;font-weight:600;">
          {{ ucfirst($order->payment_status) }} · {{ strtoupper(str_replace('_',' ',$order->payment_method)) }}
        </span>
      </div>
    </div>
  </div>
</div>
</a>
@endforeach
</div>
@if($orders->hasPages())
<div style="margin-top:16px;">{{ $orders->links() }}</div>
@endif
@endif
</div>
@endsection
