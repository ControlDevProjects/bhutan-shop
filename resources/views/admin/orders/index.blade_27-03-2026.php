@extends('admin.layouts.app')
@section('title','Orders')
@section('page-title','Orders')
@section('content')
<div class="card">
<div class="card-bd" style="border-bottom:1px solid var(--bdr);padding:12px 18px;">
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:flex-end;">
    <div><label class="lbl">Search</label><input name="search" class="fc" value="{{ request('search') }}" placeholder="Order # or customer..." style="width:200px;"></div>
    <div><label class="lbl">Status</label><select name="status" class="fc" style="width:150px;"><option value="">All</option>@foreach(['pending','confirmed','processing','packed','shipped','out_for_delivery','delivered','cancelled','returned'] as $s)<option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>@endforeach</select></div>
    <div><label class="lbl">Payment</label><select name="payment_status" class="fc" style="width:130px;"><option value="">All</option><option value="pending" {{ request('payment_status')==='pending'?'selected':'' }}>Pending</option><option value="paid" {{ request('payment_status')==='paid'?'selected':'' }}>Paid</option><option value="failed" {{ request('payment_status')==='failed'?'selected':'' }}>Failed</option></select></div>
    <div><label class="lbl">Method</label><select name="payment_method" class="fc" style="width:130px;"><option value="">All</option><option value="cod" {{ request('payment_method')==='cod'?'selected':'' }}>COD</option><option value="razorpay" {{ request('payment_method')==='razorpay'?'selected':'' }}>Razorpay</option></select></div>
    <button type="submit" class="btn btn-sc"><i class="fas fa-search"></i> Filter</button>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-sc"><i class="fas fa-times"></i></a>
</form>
</div>
@php
$sc=['pending'=>'b-warn','confirmed'=>'b-info','processing'=>'b-info','packed'=>'b-info','shipped'=>'b-info','out_for_delivery'=>'b-info','delivered'=>'b-ok','cancelled'=>'b-err','returned'=>'b-err'];
@endphp
<div class="tw"><table>
<thead><tr><th>Order #</th><th>Customer</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
<tbody>
@forelse($orders as $order)
<tr>
    <td><a href="{{ route('admin.orders.show',$order) }}" style="color:var(--pr);font-weight:700;font-family:monospace;font-size:12px;">{{ $order->order_number }}</a></td>
    <td>
        <div style="font-weight:600;font-size:13px;">{{ $order->user->name }}</div>
        <div style="font-size:11px;color:var(--mut);">{{ $order->user->email }}</div>
    </td>
    <td style="font-size:13px;">{{ $order->items_count ?? $order->items->count() }} items</td>
    <td style="font-weight:700;color:var(--pr);">BTN {{ number_format($order->total,2) }}</td>
    <td>
        <span class="badge {{ $order->payment_status==='paid'?'b-ok':($order->payment_status==='failed'?'b-err':'b-warn') }}">{{ ucfirst($order->payment_status) }}</span>
        <div style="font-size:10px;color:var(--mut);margin-top:2px;">{{ strtoupper($order->payment_method) }}</div>
        @if($order->payment_status!=='paid' && auth()->user()->isAdmin())
        <form method="POST" action="{{ route('admin.orders.pay',$order) }}" style="display:inline;">@csrf
            <button type="submit" class="btn btn-ok btn-xs" style="margin-top:4px;" data-confirm="Mark order {{ $order->order_number }} as PAID?"><i class="fas fa-check"></i> Mark Paid</button>
        </form>
        @endif
    </td>
    <td>
        <span class="badge {{ $sc[$order->status]??'b-sec' }}">{{ ucwords(str_replace('_',' ',$order->status)) }}</span>
        @if($order->assigned_to)<div style="font-size:10px;color:var(--mut);margin-top:2px;"><i class="fas fa-user"></i> {{ $order->assignedEmployee?->name }}</div>@endif
    </td>
    <td style="font-size:12px;white-space:nowrap;">{{ $order->created_at->format('d M Y') }}<br><span style="color:var(--mut);">{{ $order->created_at->format('h:i A') }}</span></td>
    <td><a href="{{ route('admin.orders.show',$order) }}" class="btn btn-sc btn-xs"><i class="fas fa-eye"></i> View</a></td>
</tr>
@empty
<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--mut);"><i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:10px;"></i>No orders found.</td></tr>
@endforelse
</tbody>
</table></div>
@if($orders->hasPages())<div style="padding:12px 18px;border-top:1px solid var(--bdr);">{{ $orders->links() }}</div>@endif
</div>
@endsection
