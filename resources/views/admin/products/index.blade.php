@extends('admin.layouts.app')
@section('title','Products')
@section('page-title','Products')
@section('topbar-actions')
    <a href="{{ route('admin.products.create') }}" class="btn btn-pr btn-sm"><i class="fas fa-plus"></i> Add Product</a>
@endsection
@section('content')
<div class="card">
<div class="card-bd" style="border-bottom:1px solid var(--bdr);padding:12px 18px;">
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:flex-end;">
    <div><label class="lbl">Search</label><input name="search" class="fc" value="{{ request('search') }}" placeholder="Name..." style="width:180px;"></div>
    <div><label class="lbl">Category</label><select name="category" class="fc" style="width:150px;"><option value="">All</option>@foreach($categories as $c)<option value="{{ $c->id }}" {{ request('category')==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach</select></div>
    <div><label class="lbl">Type</label><select name="type" class="fc" style="width:120px;"><option value="">All</option><option value="simple" {{ request('type')==='simple'?'selected':'' }}>Simple</option><option value="variant" {{ request('type')==='variant'?'selected':'' }}>Variant</option></select></div>
    <div><label class="lbl">Status</label><select name="status" class="fc" style="width:120px;"><option value="">All</option><option value="active" {{ request('status')==='active'?'selected':'' }}>Active</option><option value="inactive" {{ request('status')==='inactive'?'selected':'' }}>Inactive</option></select></div>
    <button type="submit" class="btn btn-sc"><i class="fas fa-search"></i> Filter</button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-sc"><i class="fas fa-times"></i></a>
</form>
</div>
<div class="tw"><table>
<thead><tr><th width="50">Image</th><th>Name</th><th>Type</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th width="130">Actions</th></tr></thead>
<tbody>
@forelse($products as $p)
<tr>
    <td>
        @if($p->primary_image)<img src="{{ asset('storage/'.$p->primary_image) }}" style="width:44px;height:44px;object-fit:cover;border-radius:6px;border:1px solid var(--bdr);">
        @else<div style="width:44px;height:44px;background:#f0f0f0;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:18px;"><i class="fas fa-image"></i></div>@endif
    </td>
    <td>
        <div style="font-weight:600;">{{ $p->name }}</div>
        <div style="font-size:11px;color:var(--mut);">{{ $p->slug }}</div>
        @if($p->is_featured)<span class="badge b-warn" style="margin-top:2px;font-size:10px;"><i class="fas fa-star"></i> Featured</span>@endif
        @if($p->is_low_stock)<span class="badge b-err" style="margin-top:2px;font-size:10px;"><i class="fas fa-exclamation-triangle"></i> Low Stock</span>@endif
    </td>
    <td><span class="badge {{ $p->type==='simple'?'b-info':'b-sec' }}">{{ ucfirst($p->type) }}</span></td>
    <td style="font-size:12px;">{{ $p->category?->name ?? '—' }}</td>
    <td style="font-weight:700;color:var(--pr);">{{ $p->price_display }}</td>
    <td>
        @if($p->type === 'simple')
            @if($p->stock_type==='unlimited')
                <span style="color:var(--info);font-size:12px;"><i class="fas fa-infinity"></i> Unlimited</span>
            @elseif($p->stock == 0)
                <span style="color:var(--err);font-size:12px;font-weight:600;">Out of Stock</span>
            @elseif($p->stock < 5)
                <span style="color:var(--err);font-size:12px;font-weight:600;">⚠ {{ $p->stock }} units</span>
            @else
                <span style="color:var(--ok);font-size:12px;">{{ $p->stock }} units</span>
            @endif
        @else
            {{-- Variant: show each variant's stock individually --}}
            @forelse($p->variants as $v)
            <div style="font-size:11px;line-height:1.9;display:flex;align-items:center;gap:5px;">
                <span style="color:#555;font-weight:500;">{{ $v->name }}:</span>
                @if($v->stock_type==='unlimited')
                    <span style="color:var(--info);">∞</span>
                @elseif($v->stock==0)
                    <span style="color:var(--err);font-weight:600;">0</span>
                @elseif($v->stock<5)
                    <span style="color:var(--err);font-weight:600;">⚠{{ $v->stock }}</span>
                @else
                    <span style="color:var(--ok);">{{ $v->stock }}</span>
                @endif
            </div>
            @empty
            <span style="color:var(--mut);font-size:12px;">No variants</span>
            @endforelse
        @endif
    </td>
    <td><span class="badge {{ $p->status==='active'?'b-ok':'b-err' }}">{{ ucfirst($p->status) }}</span></td>
    <td>
        <div style="display:flex;gap:4px;flex-wrap:wrap;">
            <a href="{{ route('admin.products.edit',$p) }}" class="btn btn-sc btn-xs"><i class="fas fa-edit"></i> Edit</a>
            <a href="{{ route('admin.products.stock-logs',$p) }}" class="btn btn-sc btn-xs" title="Logs"><i class="fas fa-history"></i></a>
            @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route('admin.products.destroy',$p) }}" style="display:inline;">@csrf @method('DELETE')
                <button class="btn btn-err btn-xs" data-confirm="Delete '{{ $p->name }}'?"><i class="fas fa-trash"></i></button>
            </form>
            @endif
        </div>
    </td>
</tr>
@empty
<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--mut);"><i class="fas fa-box-open" style="font-size:32px;display:block;margin-bottom:10px;"></i>No products found. <a href="{{ route('admin.products.create') }}" style="color:var(--pr);">Create one</a></td></tr>
@endforelse
</tbody>
</table></div>
@if($products->hasPages())
<div style="padding:12px 18px;border-top:1px solid var(--bdr);">{{ $products->links() }}</div>
@endif
</div>
@endsection
