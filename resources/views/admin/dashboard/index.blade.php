@extends('admin.layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('content')
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;">
    @foreach([
        ['label'=>'Total Orders','value'=>$stats['total_orders'],'icon'=>'fa-shopping-bag','color'=>'#c0392b','sub'=>$stats['pending_orders'].' pending'],
        ['label'=>'Revenue (BTN)','value'=>number_format($stats['total_revenue'],0),'icon'=>'fa-coins','color'=>'#27ae60','sub'=>'All time'],
        ['label'=>'Products','value'=>$stats['total_products'],'icon'=>'fa-box','color'=>'#2980b9','sub'=>$stats['low_stock'].' low stock'],
        ['label'=>'Customers','value'=>$stats['total_customers'],'icon'=>'fa-users','color'=>'#8e44ad','sub'=>'Registered'],
    ] as $s)
    <div class="card">
    <div class="card-bd" style="display:flex;align-items:center;gap:14px;">
        <div style="width:50px;height:50px;border-radius:10px;background:{{ $s['color'] }}1a;color:{{ $s['color'] }};display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">
            <i class="fas {{ $s['icon'] }}"></i>
        </div>
        <div>
            <div style="font-size:22px;font-weight:700;color:var(--txt);">{{ $s['value'] }}</div>
            <div style="font-size:12px;font-weight:500;color:#555;">{{ $s['label'] }}</div>
            <div style="font-size:11px;color:var(--mut);">{{ $s['sub'] }}</div>
        </div>
    </div>
    </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;">
{{-- Recent Orders --}}
<div class="card">
<div class="card-hd"><h2><i class="fas fa-shopping-bag"></i> Recent Orders</h2><a href="{{ route('admin.orders.index') }}" class="btn btn-sc btn-xs">View All</a></div>
@php $statusColors=['pending'=>'#f39c12','confirmed'=>'#2980b9','processing'=>'#8e44ad','shipped'=>'#1a73e8','delivered'=>'#27ae60','cancelled'=>'#e74c3c']; @endphp
<div>
@forelse($recentOrders as $o)
<div style="padding:11px 16px;border-bottom:1px solid var(--bdr);display:flex;align-items:center;justify-content:space-between;gap:10px;">
    <div>
        <a href="{{ route('admin.orders.show',$o) }}" style="font-size:12px;font-weight:700;color:var(--pr);font-family:monospace;text-decoration:none;">{{ $o->order_number }}</a>
        <div style="font-size:11px;color:var(--mut);">{{ $o->user->name }} · {{ $o->created_at->diffForHumans() }}</div>
    </div>
    <div style="text-align:right;">
        <div style="font-weight:700;font-size:13px;color:var(--pr);">BTN {{ number_format($o->total,2) }}</div>
        <span style="font-size:10px;font-weight:600;background:{{ ($statusColors[$o->status]??'#888') }}1a;color:{{ $statusColors[$o->status]??'#888' }};border:1px solid {{ ($statusColors[$o->status]??'#888') }}55;border-radius:10px;padding:1px 7px;">{{ ucwords(str_replace('_',' ',$o->status)) }}</span>
    </div>
</div>
@empty
<div style="padding:30px;text-align:center;color:var(--mut);">No orders yet.</div>
@endforelse
</div>
</div>

{{-- Low Stock Alert --}}
<div>
<div class="card mb16">
<div class="card-hd"><h2><i class="fas fa-exclamation-triangle" style="color:var(--warn);"></i> Low Stock Alert</h2><a href="{{ route('admin.products.index') }}" class="btn btn-sc btn-xs">All Products</a></div>
@if($lowStockProducts->isNotEmpty())
<div>
@foreach($lowStockProducts as $p)
<div style="padding:10px 16px;border-bottom:1px solid var(--bdr);display:flex;align-items:center;justify-content:space-between;gap:10px;">
    <div>
        <a href="{{ route('admin.products.edit',$p) }}" style="font-size:13px;font-weight:600;color:var(--txt);text-decoration:none;">{{ $p->name }}</a>
        <div style="font-size:11px;color:var(--mut);">{{ ucfirst($p->type) }}</div>
    </div>
    @if($p->type==='simple')
    <span style="color:var(--err);font-size:13px;font-weight:700;">{{ $p->stock }} units</span>
    @else
    <div>
        @foreach($p->variants->where('stock_type','limited')->filter(fn($v)=>$v->stock<5&&$v->stock>0) as $v)
        <div style="font-size:11px;color:var(--err);">{{ $v->name }}: {{ $v->stock }}</div>
        @endforeach
    </div>
    @endif
</div>
@endforeach
</div>
@else
<div style="padding:24px;text-align:center;color:var(--ok);"><i class="fas fa-check-circle" style="font-size:28px;display:block;margin-bottom:8px;"></i>All products well stocked!</div>
@endif
</div>

<div class="card">
<div class="card-hd"><h2><i class="fas fa-chart-bar"></i> Order Stats</h2></div>
<div class="card-bd">
@php $statusList=['pending','confirmed','processing','packed','shipped','out_for_delivery','delivered','cancelled','returned']; @endphp
@foreach($statusList as $s)
@php $count = $stats['by_status'][$s] ?? 0; $pct = $stats['total_orders'] > 0 ? ($count/$stats['total_orders']*100) : 0; @endphp
<div style="margin-bottom:10px;">
    <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px;">
        <span style="font-weight:500;">{{ ucwords(str_replace('_',' ',$s)) }}</span>
        <span style="color:var(--mut);">{{ $count }}</span>
    </div>
    <div style="height:6px;background:#f0f0f0;border-radius:3px;overflow:hidden;">
        <div style="height:100%;width:{{ $pct }}%;background:var(--pr);border-radius:3px;"></div>
    </div>
</div>
@endforeach
</div>
</div>
</div>
</div>
@endsection
