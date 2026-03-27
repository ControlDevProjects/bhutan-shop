@extends('admin.layouts.app')
@section('title','Order '.$order->order_number)
@section('page-title','Order Details')
@section('topbar-actions')
    <a href="{{ route('admin.orders.index') }}" class="btn btn-sc btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
@endsection
@section('content')
@php
$sc=['pending'=>'b-warn','confirmed'=>'b-info','processing'=>'b-info','packed'=>'b-info','shipped'=>'b-info','out_for_delivery'=>'b-info','delivered'=>'b-ok','cancelled'=>'b-err','returned'=>'b-err'];
$allStatuses=['pending','confirmed','processing','packed','shipped','out_for_delivery','delivered','cancelled','returned'];
@endphp

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">
<div>

{{-- Header Card --}}
<div class="card mb16">
<div class="card-bd">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div>
            <div style="font-size:22px;font-weight:700;color:var(--pr);font-family:monospace;">{{ $order->order_number }}</div>
            <div style="font-size:12px;color:var(--mut);margin-top:3px;">Placed {{ $order->created_at->format('d M Y, H:i') }} · Customer: <strong>{{ $order->user->name }}</strong></div>
        </div>
        <div style="text-align:right;">
            <span class="badge {{ $sc[$order->status]??'b-sec' }}" style="font-size:13px;padding:5px 14px;">{{ ucwords(str_replace('_',' ',$order->status)) }}</span>
            <div style="margin-top:6px;">
                <span class="badge {{ $order->payment_status==='paid'?'b-ok':($order->payment_status==='failed'?'b-err':'b-warn') }}">{{ ucfirst($order->payment_status) }}</span>
                <span class="badge b-sec" style="margin-left:4px;">{{ strtoupper($order->payment_method) }}</span>
            </div>
            @if($order->paid_at)<div style="font-size:11px;color:var(--ok);margin-top:4px;"><i class="fas fa-check-circle"></i> Paid {{ $order->paid_at->format('d M Y H:i') }}</div>@endif
        </div>
    </div>
</div>
</div>

{{-- Order Items --}}
<div class="card mb16">
<div class="card-hd"><h2><i class="fas fa-box"></i> Order Items</h2></div>
<div class="tw"><table>
<thead><tr><th>Product</th><th>Variant</th><th>SKU</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr></thead>
<tbody>
@foreach($order->items as $item)
<tr>
    <td><div style="font-weight:600;">{{ $item->product_name }}</div></td>
    <td style="font-size:12px;">{{ $item->variant_name ?? '—' }}</td>
    <td><code style="font-size:11px;color:var(--mut);">{{ $item->sku ?? '—' }}</code></td>
    <td>BTN {{ number_format($item->price,2) }}</td>
    <td style="font-weight:600;">{{ $item->quantity }}</td>
    <td style="font-weight:700;color:var(--pr);">BTN {{ number_format($item->subtotal,2) }}</td>
</tr>
@endforeach
</tbody>
</table></div>
<div style="padding:14px 18px;background:#fafafa;border-top:1px solid var(--bdr);">
    <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px;"><span>Subtotal</span><span>BTN {{ number_format($order->subtotal,2) }}</span></div>
    <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:10px;"><span>Shipping</span><span>{{ $order->shipping_cost==0?'FREE':'BTN '.number_format($order->shipping_cost,2) }}</span></div>
    <div style="display:flex;justify-content:space-between;font-size:18px;font-weight:700;border-top:2px solid var(--bdr);padding-top:10px;"><span>Total</span><span style="color:var(--pr);">BTN {{ number_format($order->total,2) }}</span></div>
</div>
</div>

{{-- Update Status --}}
<div class="card mb16">
<div class="card-hd"><h2><i class="fas fa-edit"></i> Update Status</h2></div>
<div class="card-bd">
<form method="POST" action="{{ route('admin.orders.status',$order) }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
@csrf
<div class="fg" style="min-width:200px;flex:1;">
    <label class="lbl">New Status</label>
    <select name="status" class="fc" required>
        @foreach($allStatuses as $s)
        <option value="{{ $s }}" {{ $order->status===$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
        @endforeach
    </select>
</div>
<div class="fg" style="flex:2;">
    <label class="lbl">Note (optional)</label>
    <input name="note" class="fc" placeholder="e.g. Dispatched via DHL, tracking #XYZ...">
</div>
<button type="submit" class="btn btn-pr"><i class="fas fa-check"></i> Update</button>
</form>

@if(auth()->user()->isAdmin() && $order->payment_status !== 'paid')
<div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--bdr);">
<form method="POST" action="{{ route('admin.orders.pay',$order) }}" style="display:inline;">@csrf
    <button type="submit" class="btn btn-ok" data-confirm="Mark this order as PAID?"><i class="fas fa-dollar-sign"></i> Mark as PAID</button>
</form>
<span style="font-size:12px;color:var(--mut);margin-left:10px;">Use for COD orders after delivery confirmation</span>
</div>
@endif
</div>
</div>

{{-- Assign Employee --}}
@if(auth()->user()->isAdmin())
<div class="card mb16">
<div class="card-hd"><h2><i class="fas fa-user-tag"></i> Assign Employee</h2></div>
<div class="card-bd">
<form method="POST" action="{{ route('admin.orders.assign',$order) }}" style="display:flex;gap:10px;align-items:flex-end;">
@csrf
<div class="fg" style="flex:1;">
    <label class="lbl">Assign to</label>
    <select name="assigned_to" class="fc">
        <option value="">— Unassigned —</option>
        @foreach($employees as $emp)
        <option value="{{ $emp->id }}" {{ $order->assigned_to===$emp->id?'selected':'' }}>
            {{ $emp->name }} ({{ ucfirst($emp->role) }})
        </option>
        @endforeach
    </select>
</div>
<button type="submit" class="btn btn-sc"><i class="fas fa-save"></i> Assign</button>
</form>
</div>
</div>
@endif

{{-- Timeline --}}
<div class="card">
<div class="card-hd"><h2><i class="fas fa-history"></i> Order Timeline</h2></div>
<div class="card-bd">
    @foreach($order->statusLogs as $log)
    <div style="display:flex;gap:12px;margin-bottom:14px;align-items:flex-start;">
        <div style="width:10px;height:10px;border-radius:50%;background:var(--pr);margin-top:5px;flex-shrink:0;"></div>
        <div>
            <div style="font-size:13px;font-weight:600;">{{ ucwords(str_replace('_',' ',$log->new_status)) }}</div>
            @if($log->note)<div style="font-size:12px;color:var(--mut);">{{ $log->note }}</div>@endif
            <div style="font-size:11px;color:var(--mut);">{{ $log->created_at->format('d M Y H:i') }} @if($log->changedBy)· by {{ $log->changedBy->name }}@endif</div>
        </div>
    </div>
    @endforeach
</div>
</div>
</div>

{{-- Right sidebar --}}
<div>
<div class="card mb16">
<div class="card-hd"><h2><i class="fas fa-user"></i> Customer</h2></div>
<div class="card-bd" style="font-size:13px;line-height:2;">
    <div style="font-weight:600;font-size:15px;">{{ $order->user->name }}</div>
    <div><i class="fas fa-envelope" style="color:var(--mut);width:16px;"></i> {{ $order->user->email }}</div>
    @if($order->user->phone)<div><i class="fas fa-phone" style="color:var(--mut);width:16px;"></i> {{ $order->user->phone }}</div>@endif
    <div style="margin-top:10px;"><a href="mailto:{{ $order->user->email }}" class="btn btn-sc btn-sm" style="width:100%;justify-content:center;"><i class="fas fa-envelope"></i> Email Customer</a></div>
</div>
</div>

<div class="card mb16">
<div class="card-hd"><h2><i class="fas fa-map-marker-alt"></i> Delivery Address</h2></div>
<div class="card-bd" style="font-size:13px;line-height:1.9;">
    <div style="font-weight:600;">{{ $order->shipping_name }}</div>
    <div>{{ $order->shipping_address }}</div>
    <div>{{ $order->shipping_city }}, {{ $order->shipping_dzongkhag }}</div>
    <div><i class="fas fa-phone" style="color:var(--mut);width:16px;"></i> {{ $order->shipping_phone }}</div>
    @if($order->notes)<div style="margin-top:8px;padding:8px;background:#fafafa;border-radius:6px;font-size:12px;border:1px solid var(--bdr);"><strong>Note:</strong> {{ $order->notes }}</div>@endif
</div>
</div>

@if($order->assignedEmployee)
<div class="card mb16">
<div class="card-hd"><h2><i class="fas fa-user-tag"></i> Assigned To</h2></div>
<div class="card-bd" style="font-size:13px;">
    <div style="font-weight:600;">{{ $order->assignedEmployee->name }}</div>
    <div style="color:var(--mut);">{{ ucfirst($order->assignedEmployee->role) }}</div>
    <div>{{ $order->assignedEmployee->email }}</div>
</div>
</div>
@endif

<div class="card">
<div class="card-hd"><h2>Payment Details</h2></div>
<div class="card-bd" style="font-size:13px;line-height:2.2;">
    <div><span style="color:var(--mut);">Method:</span> <strong>{{ strtoupper($order->payment_method) }}</strong></div>
    <div><span style="color:var(--mut);">Status:</span> <span class="badge {{ $order->payment_status==='paid'?'b-ok':($order->payment_status==='failed'?'b-err':'b-warn') }}">{{ ucfirst($order->payment_status) }}</span></div>
    @if($order->paid_at)<div><span style="color:var(--mut);">Paid at:</span> {{ $order->paid_at->format('d M Y H:i') }}</div>@endif
    @if($order->razorpay_payment_id)<div><span style="color:var(--mut);">RZP ID:</span> <code style="font-size:11px;">{{ $order->razorpay_payment_id }}</code></div>@endif
    @if($order->shipped_at)<div><span style="color:var(--mut);">Shipped:</span> {{ $order->shipped_at->format('d M Y') }}</div>@endif
    @if($order->delivered_at)<div><span style="color:var(--mut);">Delivered:</span> {{ $order->delivered_at->format('d M Y') }}</div>@endif
</div>
</div>
</div>
</div>
@endsection
