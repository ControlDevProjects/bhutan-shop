@extends('frontend.layouts.app')
@section('title', 'Order '.$order->order_number)
@section('content')

@php
  $currency     = \App\Models\Setting::get('currency_symbol', 'BTN');
  $gstPct       = (float)\App\Models\Setting::get('gst_percentage', 0);
  $gstLabel     = \App\Models\Setting::get('gst_label', 'GST');
  $gstInclusive = \App\Models\Setting::get('gst_inclusive', '1') === '1';
  $gstAmount    = $gstPct > 0
    ? ($gstInclusive
        ? round((float)$order->subtotal * $gstPct / (100 + $gstPct), 2)
        : round((float)$order->subtotal * $gstPct / 100, 2))
    : 0;
  $grandTotal   = (float)$order->subtotal + (float)$order->shipping_cost + ($gstInclusive ? 0 : $gstAmount);

  $statuses    = ['pending','confirmed','processing','packed','shipped','out_for_delivery','delivered'];
  $currentIdx  = array_search($order->status, $statuses);
  $isCancelled = in_array($order->status, ['cancelled','returned']);
  $isDelivered = $order->status === 'delivered';

  $statusColors = [
    'pending'=>'#e65100','confirmed'=>'#1565c0','processing'=>'#6a1b9a',
    'packed'=>'#4a148c','shipped'=>'#0d47a1','out_for_delivery'=>'#0277bd',
    'delivered'=>'#2e7d32','cancelled'=>'#c62828','returned'=>'#c62828',
  ];
  $sc = $statusColors[$order->status] ?? '#888';
@endphp

<div class="page-wrap">
<div class="breadcrumb">
  <a href="{{ route('home') }}">Home</a><span class="sep">/</span>
  <a href="{{ route('orders.index') }}">My Orders</a><span class="sep">/</span>
  <span>{{ $order->order_number }}</span>
</div>

{{-- TOP HEADER --}}
<div style="background:var(--card);border:1px solid var(--bdr);border-radius:var(--r2);padding:20px 24px;margin-bottom:18px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;">
  <div>
    <div style="font-size:11px;color:var(--mut);font-weight:700;text-transform:uppercase;letter-spacing:.6px;margin-bottom:4px;">Order Number</div>
    <div style="font-size:22px;font-weight:800;color:var(--pr);font-family:monospace;">{{ $order->order_number }}</div>
    <div style="font-size:12.5px;color:var(--mut);margin-top:5px;display:flex;gap:14px;flex-wrap:wrap;">
      <span><i class="fas fa-calendar-alt"></i> Placed {{ $order->created_at->format('d M Y, h:i A') }}</span>
      <span><i class="fas fa-box"></i> {{ $order->items->count() }} {{ Str::plural('item',$order->items->count()) }}</span>
      @if($order->delivered_at)
      <span style="color:var(--ok);font-weight:600;"><i class="fas fa-check-circle"></i> Delivered {{ \Carbon\Carbon::parse($order->delivered_at)->format('d M Y') }}</span>
      @endif
    </div>
  </div>
  <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
    <span style="background:{{ $sc }}15;color:{{ $sc }};border:2px solid {{ $sc }}30;border-radius:20px;padding:6px 18px;font-size:13px;font-weight:800;">
      {{ ucwords(str_replace('_',' ',$order->status)) }}
    </span>
    @if($isDelivered)
    <a href="{{ route('orders.invoice',$order) }}" target="_blank"
       style="display:inline-flex;align-items:center;gap:7px;padding:8px 18px;background:var(--pr);color:#fff;border-radius:var(--r);font-size:13px;font-weight:700;text-decoration:none;"
       onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
      <i class="fas fa-file-invoice"></i> Download Invoice
    </a>
    @endif
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:16px;align-items:start;">
<div>

{{-- PROGRESS TRACKER --}}
@if(!$isCancelled)
<div class="card mb16">
  <div class="card-hd"><h2><i class="fas fa-shipping-fast" style="color:var(--pr);"></i> Order Progress</h2></div>
  <div class="card-bd" style="padding:20px 16px 14px;">
    @php
      $icons  = ['clock','check-circle','cog','box','truck','route','check-double'];
      $labels = ['Order Placed','Confirmed','Processing','Packed','Shipped','Out for Delivery','Delivered'];
    @endphp
    <div style="display:flex;align-items:flex-start;">
      @foreach($statuses as $i => $st)
      @php
        $done    = $currentIdx !== false && $i <= $currentIdx;
        $current = $currentIdx !== false && $i === $currentIdx;
      @endphp
      <div style="display:flex;flex-direction:column;align-items:center;flex:1;position:relative;min-width:56px;">
        @if($i > 0)
        <div style="position:absolute;top:19px;right:50%;width:100%;height:3px;background:{{ $done?'var(--ok)':'#dee2e6' }};z-index:0;"></div>
        @endif
        <div style="width:40px;height:40px;border-radius:50%;
                    background:{{ $current?'var(--pr)':($done?'var(--ok)':'#dee2e6') }};
                    color:{{ $done||$current?'#fff':'#adb5bd' }};
                    display:flex;align-items:center;justify-content:center;font-size:14px;
                    z-index:1;position:relative;
                    box-shadow:{{ $current?'0 0 0 5px rgba(192,57,43,.15)':'none' }};transition:.3s;">
          <i class="fas fa-{{ $icons[$i] ?? 'circle' }}"></i>
        </div>
        <div style="font-size:10px;font-weight:{{ $current?'800':($done?'600':'400') }};
                    color:{{ $current?'var(--pr)':($done?'var(--ok)':'var(--mut)') }};
                    text-align:center;margin-top:7px;line-height:1.3;padding:0 2px;">
          {{ $labels[$i] }}
        </div>
      </div>
      @endforeach
    </div>
    @if($isDelivered)
    <div style="margin-top:16px;padding:10px 16px;background:#e8f5e9;border:1px solid #a5d6a7;border-radius:var(--r);color:var(--ok);font-weight:600;font-size:13px;text-align:center;">
      <i class="fas fa-check-circle"></i> Delivered on {{ \Carbon\Carbon::parse($order->delivered_at)->format('d M Y') }}. Thank you for your order!
    </div>
    @endif
  </div>
</div>
@else
<div style="padding:14px 18px;background:#ffebee;border:1.5px solid #ffcdd2;border-radius:var(--r2);margin-bottom:16px;display:flex;align-items:center;gap:12px;color:var(--err);font-weight:600;">
  <i class="fas fa-times-circle" style="font-size:22px;flex-shrink:0;"></i>
  <div>
    <div style="font-size:14px;">This order has been <strong>{{ ucfirst($order->status) }}</strong>.</div>
    @php $cancelNote = $order->statusLogs->firstWhere('new_status',$order->status)?->note; @endphp
    @if($cancelNote)<div style="font-size:12px;font-weight:400;color:#b71c1c;margin-top:2px;">{{ $cancelNote }}</div>@endif
  </div>
</div>
@endif

{{-- ORDER ITEMS --}}
<div class="card mb16">
  <div class="card-hd"><h2><i class="fas fa-box-open" style="color:var(--pr);"></i> Items Ordered ({{ $order->items->count() }})</h2></div>
  @foreach($order->items as $item)
  <div style="display:flex;gap:14px;padding:16px 18px;border-bottom:1px solid var(--bdr2);align-items:center;">
    <a href="{{ $item->product ? route('products.show',$item->product->slug) : '#' }}"
       style="width:72px;height:72px;flex-shrink:0;border-radius:var(--r);overflow:hidden;border:1px solid var(--bdr);background:#f8f8f8;display:flex;align-items:center;justify-content:center;text-decoration:none;">
      @if($item->product && $item->product->primary_image)
        <img src="{{ asset('storage/'.$item->product->primary_image) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
      @else
        <i class="fas fa-image" style="color:#ddd;font-size:22px;"></i>
      @endif
    </a>
    <div style="flex:1;min-width:0;">
      <a href="{{ $item->product ? route('products.show',$item->product->slug) : '#' }}"
         style="font-weight:700;font-size:14px;color:var(--txt);text-decoration:none;display:block;"
         onmouseover="this.style.color='var(--pr)'" onmouseout="this.style.color='var(--txt)'">
        {{ $item->product_name }}
      </a>
      @if($item->variant_name)
      <span style="display:inline-block;background:var(--pr-lt);color:var(--pr);font-size:11.5px;font-weight:600;padding:2px 8px;border-radius:3px;margin-top:4px;border:1px solid #ffcdd2;">
        {{ $item->variant_name }}
      </span>
      @endif
      @if($item->sku)
      <div style="font-size:11px;color:var(--mut);font-family:monospace;margin-top:3px;">SKU: {{ $item->sku }}</div>
      @endif
      <div style="font-size:12.5px;color:var(--mut);margin-top:5px;">
        {{ $currency }} {{ number_format($item->price,2) }} &times; {{ $item->quantity }}
      </div>
    </div>
    <div style="text-align:right;flex-shrink:0;">
      <div style="font-weight:800;font-size:16px;color:var(--pr);">{{ $currency }} {{ number_format($item->subtotal,2) }}</div>
      @if($isDelivered && $item->product)
      <a href="{{ route('products.show',$item->product->slug) }}#reviews"
         style="font-size:11.5px;color:var(--info);text-decoration:none;display:block;margin-top:5px;">
        ✍️ Write Review
      </a>
      @endif
    </div>
  </div>
  @endforeach
</div>

{{-- ACTIVITY LOG --}}
@if($order->statusLogs->count())
<div class="card mb16">
  <div class="card-hd"><h2><i class="fas fa-history" style="color:var(--pr);"></i> Order Activity</h2></div>
  <div class="card-bd" style="padding:0 18px;">
    @foreach($order->statusLogs->sortByDesc('created_at') as $log)
    @php
      $lc = ['delivered'=>'var(--ok)','cancelled'=>'var(--err)','returned'=>'var(--err)','shipped'=>'var(--info)','out_for_delivery'=>'var(--info)'][$log->new_status ?? ''] ?? 'var(--pr)';
    @endphp
    <div style="display:flex;gap:12px;padding:12px 0;border-bottom:1px solid var(--bdr2);">
      <div style="width:34px;height:34px;background:{{ $lc }}18;border:2px solid {{ $lc }}30;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fas fa-circle" style="color:{{ $lc }};font-size:8px;"></i>
      </div>
      <div>
        <div style="font-size:13.5px;font-weight:700;">Status: <span style="color:{{ $lc }};">{{ ucwords(str_replace('_',' ',$log->new_status)) }}</span></div>
        @if($log->note)<div style="font-size:12.5px;color:var(--txt2);margin-top:2px;">{{ $log->note }}</div>@endif
        <div style="font-size:11.5px;color:var(--mut);margin-top:3px;"><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A') }}</div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

{{-- CANCEL --}}
@if($order->canBeCancelled())
<form method="POST" action="{{ route('orders.cancel',$order) }}" onsubmit="return confirm('Cancel this order? This cannot be undone.')">
  @csrf
  <button type="submit" class="btn btn-err"><i class="fas fa-times-circle"></i> Cancel Order</button>
</form>
@endif

</div>

{{-- RIGHT SIDEBAR --}}
<div>

{{-- Order Summary --}}
<div class="card mb16">
  <div class="card-hd"><h2><i class="fas fa-receipt" style="color:var(--pr);"></i> Order Summary</h2></div>
  <div class="card-bd">
    <div style="display:flex;justify-content:space-between;font-size:13.5px;color:var(--txt2);margin-bottom:9px;">
      <span>Subtotal ({{ $order->items->sum('quantity') }} items)</span>
      <span>{{ $currency }} {{ number_format($order->subtotal,2) }}</span>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:13.5px;color:var(--txt2);margin-bottom:9px;">
      <span>Shipping</span>
      <span style="{{ $order->shipping_cost==0?'color:var(--ok);font-weight:700;':'' }}">
        {{ $order->shipping_cost==0 ? 'FREE' : $currency.' '.number_format($order->shipping_cost,2) }}
      </span>
    </div>
    @if($gstPct > 0)
    <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--mut);margin-bottom:9px;">
      <span>{{ $gstLabel }} ({{ $gstPct }}%{{ $gstInclusive?', incl.':'' }})</span>
      <span>{{ $currency }} {{ number_format($gstAmount,2) }}</span>
    </div>
    @endif
    <div style="border-top:2px dashed var(--bdr);padding-top:13px;display:flex;justify-content:space-between;font-size:19px;font-weight:800;">
      <span>Total</span>
      <span style="color:var(--pr);">{{ $currency }} {{ number_format($grandTotal,2) }}</span>
    </div>
    @if($gstPct > 0 && $gstInclusive)
    <div style="font-size:11px;color:var(--mut);text-align:right;margin-top:3px;">Incl. {{ $gstLabel }} of {{ $currency }} {{ number_format($gstAmount,2) }}</div>
    @endif
  </div>
</div>

{{-- Delivery Address --}}
<div class="card mb16">
  <div class="card-hd"><h2><i class="fas fa-map-marker-alt" style="color:var(--pr);"></i> Delivery Address</h2></div>
  <div class="card-bd" style="font-size:13.5px;line-height:1.9;color:var(--txt2);">
    <div style="font-weight:700;font-size:14px;color:var(--txt);margin-bottom:2px;">{{ $order->shipping_name }}</div>
    <div>{{ $order->shipping_address }}</div>
    <div>{{ $order->shipping_city }}, {{ $order->shipping_dzongkhag }}</div>
    <a href="tel:{{ $order->shipping_phone }}" style="color:var(--ok);font-weight:600;text-decoration:none;font-size:13px;display:block;margin-top:5px;">
      <i class="fas fa-phone"></i> {{ $order->shipping_phone }}
    </a>
    @if($order->notes)
    <div style="margin-top:8px;padding:8px 10px;background:#fff8e1;border-radius:5px;font-size:12.5px;color:var(--txt2);">
      <i class="fas fa-sticky-note" style="color:var(--warn);"></i> {{ $order->notes }}
    </div>
    @endif
  </div>
</div>

{{-- Payment --}}
<div class="card mb16">
  <div class="card-hd"><h2><i class="fas fa-credit-card" style="color:var(--pr);"></i> Payment</h2></div>
  <div class="card-bd" style="font-size:13.5px;line-height:2;color:var(--txt2);">
    <div style="display:flex;justify-content:space-between;">
      <span>Method</span>
      <span style="font-weight:700;">
        @if($order->payment_method==='cod') 💵 Cash on Delivery
        @elseif($order->payment_method==='razorpay') 💳 Razorpay
        @else 🏦 Bank Transfer
        @endif
      </span>
    </div>
    <div style="display:flex;justify-content:space-between;">
      <span>Status</span>
      <span style="font-weight:700;color:{{ $order->payment_status==='paid'?'var(--ok)':($order->payment_status==='failed'?'var(--err)':'var(--warn)') }};">
        @if($order->payment_status==='paid') ✅ Paid
        @elseif($order->payment_status==='failed') ❌ Failed
        @else ⏳ Pending
        @endif
      </span>
    </div>
    @if($order->paid_at)
    <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--mut);">
      <span>Paid on</span><span>{{ \Carbon\Carbon::parse($order->paid_at)->format('d M Y, h:i A') }}</span>
    </div>
    @endif
    @if($order->razorpay_payment_id)
    <div style="margin-top:5px;font-family:monospace;font-size:11px;color:var(--mut);word-break:break-all;">Ref: {{ $order->razorpay_payment_id }}</div>
    @endif
  </div>
</div>

{{-- Invoice card --}}
@if($isDelivered)
<div class="card" style="border:1.5px solid var(--ok);">
  <div class="card-bd" style="text-align:center;padding:22px 18px;">
    <div style="font-size:36px;margin-bottom:8px;">🧾</div>
    <div style="font-weight:700;font-size:14px;color:var(--txt);margin-bottom:5px;">Tax Invoice Ready</div>
    <div style="font-size:12.5px;color:var(--mut);margin-bottom:14px;">Your order has been delivered. Your invoice is ready to download.</div>
    <a href="{{ route('orders.invoice',$order) }}" target="_blank"
       style="display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;background:var(--ok);color:#fff;border-radius:var(--r);font-size:14px;font-weight:700;text-decoration:none;transition:.15s;"
       onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
      <i class="fas fa-file-invoice"></i> View &amp; Print Invoice
    </a>
  </div>
</div>
@else
<div class="card">
  <div class="card-bd" style="text-align:center;padding:18px;color:var(--mut);">
    <i class="fas fa-file-invoice" style="font-size:28px;margin-bottom:8px;display:block;opacity:.4;"></i>
    <div style="font-size:12.5px;">Invoice will be available once your order is delivered.</div>
  </div>
</div>
@endif

</div>
</div>
</div>
@endsection