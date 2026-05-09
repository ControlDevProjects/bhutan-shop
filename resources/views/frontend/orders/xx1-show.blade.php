@extends('frontend.layouts.app')
@section('title', 'Order '.$order->order_number)
@section('content')
<div class="page-wrap">
<div class="breadcrumb">
  <a href="{{ route('home') }}">Home</a><span class="sep">/</span>
  <a href="{{ route('orders.index') }}">My Orders</a><span class="sep">/</span>
  <span>{{ $order->order_number }}</span>
</div>

@php
  $statuses = ['pending','confirmed','processing','packed','shipped','out_for_delivery','delivered'];
  $currentIdx = array_search($order->status, $statuses);
  $isCancelled = in_array($order->status, ['cancelled','returned']);
  $statusColors = ['pending'=>'#e65100','confirmed'=>'#1565c0','processing'=>'#6a1b9a','packed'=>'#4a148c','shipped'=>'#0d47a1','out_for_delivery'=>'#0277bd','delivered'=>'#2e7d32','cancelled'=>'#c62828','returned'=>'#c62828'];
  $sc = $statusColors[$order->status] ?? '#888';
@endphp

<div style="display:grid;grid-template-columns:1fr 340px;gap:16px;align-items:start;">
<div>

{{-- Order header --}}
<div class="card mb16">
  <div class="card-bd" style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;align-items:center;">
    <div>
      <div style="font-size:13px;color:var(--mut);margin-bottom:3px;">Order Number</div>
      <div style="font-size:20px;font-weight:800;color:var(--pr);font-family:monospace;">{{ $order->order_number }}</div>
      <div style="font-size:12px;color:var(--mut);margin-top:3px;"><i class="fas fa-clock"></i> Placed on {{ $order->created_at->format('d M Y, h:i A') }}</div>
    </div>
    <div style="text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:6px;">
      <span style="background:{{ $sc }}15;color:{{ $sc }};border:1.5px solid {{ $sc }}40;border-radius:20px;padding:5px 14px;font-size:13px;font-weight:700;">
        {{ ucwords(str_replace('_',' ',$order->status)) }}
      </span>
      <span style="font-size:13px;font-weight:700;color:{{ $order->payment_status==='paid'?'var(--ok)':'var(--warn)' }};">
        <i class="fas fa-{{ $order->payment_status==='paid'?'check-circle':'clock' }}"></i>
        {{ ucfirst($order->payment_status) }} · {{ strtoupper(str_replace('_',' ',$order->payment_method)) }}
      </span>
    </div>
  </div>
</div>

{{-- Progress tracker --}}
@if(!$isCancelled)
<div class="card mb16">
  <div class="card-hd"><h2><i class="fas fa-shipping-fast" style="color:var(--pr);"></i> Order Progress</h2></div>
  <div class="card-bd">
    <div style="display:flex;align-items:flex-start;gap:0;overflow-x:auto;padding:8px 0;">
      @foreach($statuses as $i => $st)
      @php
        $done = $currentIdx !== false && $i <= $currentIdx;
        $current = $currentIdx !== false && $i === $currentIdx;
        $icons = ['clock','check-circle','cog','box','truck','route','check-double'];
        $labels = ['Pending','Confirmed','Processing','Packed','Shipped','Out for Delivery','Delivered'];
      @endphp
      <div style="display:flex;flex-direction:column;align-items:center;flex:1;min-width:80px;position:relative;">
        @if($i > 0)
        <div style="position:absolute;top:18px;right:50%;width:100%;height:3px;background:{{ $done?'var(--ok)':'var(--bdr)' }};z-index:0;"></div>
        @endif
        <div style="width:38px;height:38px;border-radius:50%;background:{{ $current?'var(--pr)':($done?'var(--ok)':'var(--bdr)') }};color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;z-index:1;position:relative;box-shadow:{{ $current?'0 0 0 4px rgba(192,57,43,.2)':'' }};transition:.3s;">
          <i class="fas fa-{{ $icons[$i] ?? 'circle' }}"></i>
        </div>
        <div style="font-size:10.5px;font-weight:{{ $current?'800':($done?'700':'400') }};color:{{ $current?'var(--pr)':($done?'var(--ok)':'var(--mut)') }};text-align:center;margin-top:6px;line-height:1.3;">
          {{ $labels[$i] }}
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@else
<div class="alert alert-err mb16"><i class="fas fa-times-circle"></i> This order has been {{ $order->status }}.</div>
@endif

{{-- Order items --}}
<div class="card mb16">
  <div class="card-hd"><h2><i class="fas fa-box-open" style="color:var(--pr);"></i> Items ({{ $order->items->count() }})</h2></div>
  @foreach($order->items as $item)
  <div style="display:flex;gap:12px;padding:14px 16px;border-bottom:1px solid var(--bdr2);align-items:center;">
    <div style="width:64px;height:64px;border-radius:var(--r);overflow:hidden;border:1px solid var(--bdr);flex-shrink:0;background:#f5f5f5;display:flex;align-items:center;justify-content:center;">
      @if($item->product && $item->product->primary_image)
        <img src="{{ asset('storage/'.$item->product->primary_image) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
      @else
        <i class="fas fa-image" style="color:#ddd;font-size:20px;"></i>
      @endif
    </div>
    <div style="flex:1;">
      <div style="font-weight:700;font-size:13.5px;">{{ $item->product_name }}</div>
      @if($item->variant_name)<div style="font-size:12px;color:var(--mut);"><span class="chip chip-pr">{{ $item->variant_name }}</span></div>@endif
      @if($item->sku)<div style="font-size:11px;color:var(--mut);font-family:monospace;margin-top:2px;">SKU: {{ $item->sku }}</div>@endif
      <div style="font-size:12.5px;color:var(--mut);margin-top:4px;">BTN {{ number_format($item->price,2) }} × {{ $item->quantity }}</div>
    </div>
    <div style="font-weight:800;font-size:15px;color:var(--pr);">BTN {{ number_format($item->subtotal,2) }}</div>
  </div>
  @endforeach
</div>

{{-- Status Timeline --}}
@if($order->statusLogs->count())
<div class="card mb16">
  <div class="card-hd"><h2><i class="fas fa-history" style="color:var(--pr);"></i> Activity Log</h2></div>
  <div class="card-bd">
    @foreach($order->statusLogs->sortByDesc('created_at') as $log)
    <div style="display:flex;gap:12px;padding:8px 0;border-bottom:1px solid var(--bdr2);">
      <div style="width:32px;height:32px;background:var(--pr-lt);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fas fa-circle" style="color:var(--pr);font-size:8px;"></i>
      </div>
      <div style="flex:1;">
        <div style="font-size:13px;font-weight:700;">
          Status changed to <span style="color:var(--pr);">{{ ucwords(str_replace('_',' ',$log->new_status)) }}</span>
        </div>
        @if($log->note)<div style="font-size:12px;color:var(--mut);">{{ $log->note }}</div>@endif
        <div style="font-size:11px;color:var(--mut);margin-top:2px;">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A') }}</div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

{{-- Cancel button --}}
@if($order->canBeCancelled())
<form method="POST" action="{{ route('orders.cancel',$order) }}" onsubmit="return confirm('Are you sure you want to cancel this order?')">
  @csrf
  <button type="submit" class="btn btn-err"><i class="fas fa-times-circle"></i> Cancel Order</button>
</form>
@endif

</div>

{{-- Right: Summary --}}
<div>
<div class="card mb16">
  <div class="card-hd"><h2>Order Summary</h2></div>
  <div class="card-bd">
    <div style="display:flex;justify-content:space-between;font-size:13.5px;margin-bottom:8px;"><span>Subtotal</span><span>BTN {{ number_format($order->subtotal,2) }}</span></div>
    <div style="display:flex;justify-content:space-between;font-size:13.5px;margin-bottom:14px;">
      <span>Shipping</span>
      <span style="{{ $order->shipping_cost==0?'color:var(--ok);font-weight:700;':'' }}">{{ $order->shipping_cost==0 ? 'FREE' : 'BTN '.number_format($order->shipping_cost,2) }}</span>
    </div>
    <div style="border-top:2px dashed var(--bdr);padding-top:14px;display:flex;justify-content:space-between;font-size:18px;font-weight:800;">
      <span>Total</span>
      <span style="color:var(--pr);">BTN {{ number_format($order->total,2) }}</span>
    </div>
  </div>
</div>

<div class="card mb16">
  <div class="card-hd"><h2><i class="fas fa-map-marker-alt" style="color:var(--pr);"></i> Delivery Address</h2></div>
  <div class="card-bd" style="font-size:13.5px;line-height:1.8;color:var(--txt2);">
    <div style="font-weight:700;color:var(--txt);">{{ $order->shipping_name }}</div>
    <div>{{ $order->shipping_address }}</div>
    <div>{{ $order->shipping_city }}, {{ $order->shipping_dzongkhag }}</div>
    <div style="margin-top:4px;"><i class="fas fa-phone" style="color:var(--ok);"></i> {{ $order->shipping_phone }}</div>
  </div>
</div>

<div class="card">
  <div class="card-hd"><h2><i class="fas fa-credit-card" style="color:var(--pr);"></i> Payment Details</h2></div>
  <div class="card-bd" style="font-size:13.5px;line-height:1.9;color:var(--txt2);">
    <div style="display:flex;justify-content:space-between;"><span>Method:</span><span style="font-weight:700;">{{ strtoupper(str_replace('_',' ',$order->payment_method)) }}</span></div>
    <div style="display:flex;justify-content:space-between;"><span>Status:</span><span style="color:{{ $order->payment_status==='paid'?'var(--ok)':'var(--warn)' }};font-weight:700;">{{ ucfirst($order->payment_status) }}</span></div>
    @if($order->paid_at)<div style="display:flex;justify-content:space-between;font-size:12px;"><span>Paid at:</span><span>{{ \Carbon\Carbon::parse($order->paid_at)->format('d M, h:i A') }}</span></div>@endif
    @if($order->razorpay_payment_id)<div style="font-size:11px;color:var(--mut);margin-top:4px;font-family:monospace;">{{ $order->razorpay_payment_id }}</div>@endif
  </div>
</div>
</div>
</div>
</div>
@endsection
