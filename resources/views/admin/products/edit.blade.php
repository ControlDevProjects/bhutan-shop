@extends('admin.layouts.app')
@section('title','Edit: '.$product->name)
@section('page-title','Edit: '.$product->name)
@section('topbar-actions')
    <a href="{{ route('admin.products.stock-logs',$product) }}" class="btn btn-sc btn-sm"><i class="fas fa-history"></i> Logs</a>
    <a href="{{ route('products.show',$product->slug) }}" target="_blank" class="btn btn-sc btn-sm"><i class="fas fa-eye"></i> View</a>
    <a href="{{ route('admin.products.index') }}" class="btn btn-sc btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
@endsection
@section('content')

{{-- Tab Nav --}}
<div style="display:flex;gap:0;border-bottom:2px solid var(--bdr);margin-bottom:18px;" id="tabNav">
    <button class="tbtn active" data-tab="info" type="button">📋 Basic Info</button>
    <button class="tbtn" data-tab="images" type="button">🖼️ Images</button>
    @if($product->type==='variant')<button class="tbtn" data-tab="variants" type="button">🧩 Variants</button>@endif
    <button class="tbtn" data-tab="logs" type="button">📊 Logs</button>
</div>

{{-- TAB: Info --}}
<div class="tab-panel" id="tab-info">
<form method="POST" action="{{ route('admin.products.update',$product) }}" enctype="multipart/form-data" id="basicInfoForm">
@csrf @method('PUT')
{{-- IMPORTANT: type must always be submitted to pass server validation --}}
<input type="hidden" name="type" value="{{ $product->type }}">
<div class="g2" style="grid-template-columns:2fr 1fr;align-items:start;">
<div>
<div class="card mb16">
<div class="card-hd"><h2>Basic Info</h2></div>
<div class="card-bd">
    <div class="fg"><label class="lbl">Name <span class="req">*</span></label><input name="name" class="fc" value="{{ old('name',$product->name) }}" required></div>
    <div class="fg"><label class="lbl">Description</label><textarea name="description" class="fc" rows="4">{{ old('description',$product->description) }}</textarea></div>
    <div class="g2">
        <div class="fg"><label class="lbl">Category</label><select name="category_id" class="fc"><option value="">— None —</option>@foreach($categories as $c)<option value="{{ $c->id }}" {{ old('category_id',$product->category_id)==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach</select></div>
        <div class="fg"><label class="lbl">Status</label><select name="status" class="fc"><option value="active" {{ $product->status==='active'?'selected':'' }}>Active</option><option value="inactive" {{ $product->status==='inactive'?'selected':'' }}>Inactive</option></select></div>
    </div>
    <div class="fg"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;"><input type="checkbox" name="is_featured" value="1" {{ $product->is_featured?'checked':'' }}> <span class="lbl" style="margin:0;">Featured Product ⭐</span></label></div>
    <div class="g3" style="margin-top:10px;">
        <div class="fg"><label class="lbl">Shipping Type</label>
            <select name="shipping_type" id="shipping_type_edit" class="fc" onchange="toggleFlatRate('edit')">
                <option value="standard" {{ ($product->shipping_type??'standard')==='standard'?'selected':'' }}>Standard (BTN 150 / Free above 5000)</option>
                <option value="free" {{ ($product->shipping_type??'')==='free'?'selected':'' }}>Always Free</option>
                <option value="express" {{ ($product->shipping_type??'')==='express'?'selected':'' }}>Express (BTN 300)</option>
                <option value="flat_rate" {{ ($product->shipping_type??'')==='flat_rate'?'selected':'' }}>Flat Rate</option>
            </select>
        </div>
        <div class="fg" id="flat_rate_edit" style="{{ ($product->shipping_type??'')==='flat_rate'?'':'display:none' }}"><label class="lbl">Flat Rate (BTN)</label><input type="number" name="shipping_flat_rate" class="fc" step="0.01" min="0" value="{{ old('shipping_flat_rate',$product->shipping_flat_rate??150) }}"></div>
        <div class="fg"><label class="lbl">Processing Days</label><input type="number" name="processing_days" class="fc" min="0" max="30" value="{{ old('processing_days',$product->processing_days??1) }}"></div>
    </div>
</div>
</div>

@if($product->type==='simple')
<div class="card mb16">
<div class="card-hd"><h2>Pricing & Stock</h2></div>
<div class="card-bd">
    <div class="g3">
        <div class="fg"><label class="lbl">Price (BTN) <span class="req">*</span></label><input type="number" name="price" class="fc" step="0.01" min="0" value="{{ old('price',$product->price) }}" required></div>
        <div class="fg"><label class="lbl">Stock Type</label><select name="stock_type" id="esttype" class="fc"><option value="unlimited" {{ $product->stock_type==='unlimited'?'selected':'' }}>Unlimited</option><option value="limited" {{ $product->stock_type==='limited'?'selected':'' }}>Limited</option></select></div>
        <div class="fg" id="estqty" style="{{ $product->stock_type==='limited'?'':'display:none;' }}"><label class="lbl">Quantity</label><input type="number" name="stock" class="fc" min="0" value="{{ old('stock',$product->stock) }}"></div>
    </div>
</div>
</div>
@else
<div class="card mb16">
<div class="card-hd"><h2>Attributes</h2></div>
<div class="card-bd">
    <div class="g3">@foreach($attributes as $attr)
    <label style="display:flex;align-items:flex-start;gap:8px;padding:10px;border:1px solid var(--bdr);border-radius:6px;cursor:pointer;">
        <input type="checkbox" name="attributes[]" value="{{ $attr->id }}" {{ $product->attributes->contains($attr->id)?'checked':'' }}>
        <div><div style="font-weight:600;font-size:13px;">{{ $attr->name }}</div><div style="font-size:11px;color:var(--mut);">{{ $attr->options->pluck('value')->join(', ') }}</div></div>
    </label>
    @endforeach</div>
</div>
</div>
@endif
</div>

<div>
<div class="card mb16">
<div class="card-hd"><h2>Product Info</h2></div>
<div class="card-bd" style="font-size:12.5px;line-height:2.2;">
    <div><span style="color:var(--mut);">Type:</span> <strong>{{ ucfirst($product->type) }}</strong></div>
    <div><span style="color:var(--mut);">Slug:</span> <code style="font-size:11px;">{{ $product->slug }}</code></div>
    @if($product->type==='simple')
    <div><span style="color:var(--mut);">Stock:</span> <strong>{{ $product->stock_type==='unlimited' ? '∞ Unlimited' : $product->stock.' units' }}</strong></div>
    @else
    <div><span style="color:var(--mut);">Variants:</span> <strong>{{ $product->variants->count() }}</strong></div>
    @endif
    <div><span style="color:var(--mut);">Price:</span> <strong>{{ $product->price_display }}</strong></div>
    <div><span style="color:var(--mut);">Created:</span> {{ $product->created_at->format('d M Y') }}</div>
</div>
</div>
@if(auth()->user()->isAdmin())
<div class="card" style="border:1px solid #f5b7b1;">
<div class="card-hd"><h2 style="color:var(--err);">Danger Zone</h2></div>
<div class="card-bd">
<form method="POST" action="{{ route('admin.products.destroy',$product) }}">@csrf @method('DELETE')
<button type="submit" class="btn btn-err" style="width:100%;justify-content:center;" data-confirm="Permanently delete '{{ $product->name }}'? This cannot be undone."><i class="fas fa-trash"></i> Delete Product</button>
</form>
</div>
</div>
@endif
</div>
</div>
<div style="margin-top:16px;display:flex;justify-content:flex-end;">
    <button type="submit" class="btn btn-pr"><i class="fas fa-save"></i> Save Changes</button>
</div>
</form>
</div>

{{-- TAB: Images --}}
<div class="tab-panel" id="tab-images" style="display:none;">
<form method="POST" action="{{ route('admin.products.update',$product) }}" enctype="multipart/form-data">
@csrf @method('PUT')
<input type="hidden" name="type" value="{{ $product->type }}">
<input type="hidden" name="name" value="{{ $product->name }}">
<input type="hidden" name="description" value="{{ $product->description ?? '' }}">
<input type="hidden" name="category_id" value="{{ $product->category_id ?? '' }}">
<input type="hidden" name="status" value="{{ $product->status }}">
<input type="hidden" name="is_featured" value="{{ $product->is_featured ? 1 : 0 }}">
<input type="hidden" name="shipping_type" value="{{ $product->shipping_type ?? 'standard' }}">
<input type="hidden" name="shipping_flat_rate" value="{{ $product->shipping_flat_rate ?? '' }}">
<input type="hidden" name="processing_days" value="{{ $product->processing_days ?? 1 }}">
@if($product->type==='simple')
<input type="hidden" name="price" value="{{ $product->price }}">
<input type="hidden" name="stock_type" value="{{ $product->stock_type }}">
<input type="hidden" name="stock" value="{{ $product->stock }}">
@else
@foreach($product->attributes as $a)<input type="hidden" name="attributes[]" value="{{ $a->id }}">@endforeach
@endif
<div class="card">
<div class="card-hd"><h2>Product Images <span style="font-size:12px;color:var(--mut);">(up to 3)</span></h2></div>
<div class="card-bd">
<div class="g3">
@foreach([1,2,3] as $i)
@php $k="image_{$i}"; @endphp
<div>
    <label class="lbl">Image {{ $i }}</label>
    <div style="width:100%;height:150px;border:2px dashed var(--bdr);border-radius:8px;overflow:hidden;background:#fafafa;display:flex;align-items:center;justify-content:center;margin-bottom:8px;">
        @if($product->$k)<img id="imgP{{ $i }}" src="{{ asset('storage/'.$product->$k) }}" style="width:100%;height:100%;object-fit:cover;">
        @else<span id="imgP{{ $i }}" style="color:#ccc;font-size:36px;"><i class="fas fa-image"></i></span>@endif
    </div>
    <input type="file" name="image_{{ $i }}" class="fc" accept="image/*" onchange="lp(this,{{ $i }})">
    @if($product->$k)<label style="font-size:12px;display:flex;align-items:center;gap:4px;color:var(--err);margin-top:4px;cursor:pointer;"><input type="checkbox" name="remove_image_{{ $i }}" value="1"> Remove image</label>@endif
</div>
@endforeach
</div>
<div style="margin-top:16px;display:flex;justify-content:flex-end;">
    <button type="submit" class="btn btn-pr"><i class="fas fa-save"></i> Save Images</button>
</div>
</div>
</div>
</form>
</div>

{{-- TAB: Variants --}}
@if($product->type==='variant')
<div class="tab-panel" id="tab-variants" style="display:none;">
<div class="card mb16">
<div class="card-hd"><h2><i class="fas fa-plus"></i> Add New Variant</h2></div>
<div class="card-bd">
@php $pattrs = $product->attributes->load('options'); @endphp
@if($pattrs->isEmpty())
    <div class="alert alert-warn"><i class="fas fa-info-circle"></i> No attributes assigned. Go to Basic Info tab and check attributes first.</div>
@else
<form method="POST" action="{{ route('admin.products.variants.store',$product) }}" enctype="multipart/form-data">
@csrf
<div class="g2 mb16">
@foreach($pattrs as $attr)
<div class="fg">
    <label class="lbl">{{ $attr->name }} <span class="req">*</span></label>
    <select name="attribute_options[]" class="fc" required>
        <option value="">Select {{ $attr->name }}...</option>
        @foreach($attr->options as $opt)<option value="{{ $opt->id }}">{{ $opt->value }}</option>@endforeach
    </select>
</div>
@endforeach
</div>
<div class="g3">
    <div class="fg"><label class="lbl">Price (BTN) <span class="req">*</span></label><input type="number" name="price" class="fc" step="0.01" min="0" placeholder="0.00" required></div>
    <div class="fg"><label class="lbl">Stock Type</label><select name="stock_type" id="nvst" class="fc"><option value="unlimited">Unlimited</option><option value="limited">Limited</option></select></div>
    <div class="fg" id="nvqty" style="display:none;"><label class="lbl">Quantity</label><input type="number" name="stock" class="fc" min="0" value="0"></div>
</div>
<div class="g3 mb16">
    @foreach([1,2,3] as $i)<div class="fg"><label class="lbl">Image {{ $i }}</label><input type="file" name="v_image_{{ $i }}" class="fc" accept="image/*" onchange="vp(this,'vp{{ $i }}')"><img id="vp{{ $i }}" src="" style="display:none;width:60px;height:60px;object-fit:cover;border-radius:6px;margin-top:4px;border:1px solid var(--bdr);"></div>@endforeach
</div>
<button type="submit" class="btn btn-ok"><i class="fas fa-plus"></i> Add Variant</button>
</form>
@endif
</div>
</div>

<div class="card">
<div class="card-hd">
    <h2>Existing Variants ({{ $product->variants->count() }})</h2>
    <div style="font-size:12px;color:var(--mut);">
        @php
            $limitedTotal = $product->variants->where('stock_type','limited')->sum('stock');
            $unlimCount = $product->variants->where('stock_type','unlimited')->count();
        @endphp
        @if($unlimCount) {{ $unlimCount }} Unlimited · @endif {{ $limitedTotal }} Limited units total
    </div>
</div>
<div class="card-bd">
@forelse($product->variants as $v)
<div class="vc" style="border:1px solid var(--bdr);border-radius:var(--r);padding:14px;margin-bottom:10px;background:#fafafa;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:10px;">
        <div>
            <div style="font-weight:700;font-size:14px;">{{ $v->name }}</div>
            <div style="font-size:11px;color:var(--mut);font-family:monospace;">SKU: {{ $v->sku }}</div>
            <div style="margin-top:5px;display:flex;gap:5px;flex-wrap:wrap;">
                @foreach($v->attributeOptions as $o)<span class="chip chip-pr">{{ $o->attribute->name }}: {{ $o->value }}</span>@endforeach
            </div>
        </div>
        <div style="display:flex;gap:6px;">
            <button type="button" class="btn btn-sc btn-sm" onclick="toggleEdit('ve{{ $v->id }}')"><i class="fas fa-edit"></i> Edit</button>
            <form method="POST" action="{{ route('admin.products.variants.destroy',[$product,$v]) }}" style="display:inline;">@csrf @method('DELETE')
                <button class="btn btn-err btn-sm" data-confirm="Delete variant '{{ $v->name }}'?"><i class="fas fa-trash"></i></button>
            </form>
        </div>
    </div>
    <div style="display:flex;gap:20px;font-size:13px;flex-wrap:wrap;align-items:center;">
        <div><span style="color:var(--mut);">Price:</span> <strong>BTN {{ number_format($v->price,2) }}</strong></div>
        <div><span style="color:var(--mut);">Stock:</span>
            @if($v->stock_type==='unlimited')<span style="color:var(--info);"><i class="fas fa-infinity"></i> Unlimited</span>
            @elseif($v->stock==0)<span style="color:var(--err);font-weight:700;">Out of Stock</span>
            @elseif($v->stock<5)<span style="color:var(--err);font-weight:700;">⚠ {{ $v->stock }} units</span>
            @else<span style="color:var(--ok);">{{ $v->stock }} units</span>@endif
        </div>
        @if($v->image_1||$v->image_2||$v->image_3)
        <div style="display:flex;gap:4px;">
            @foreach(['image_1','image_2','image_3'] as $img)
            @if($v->$img)
            <img src="{{ asset('storage/'.$v->$img) }}" style="width:34px;height:34px;object-fit:cover;border-radius:4px;border:1px solid var(--bdr);">
            @endif
            @endforeach
        </div>
        @endif
    </div>
    {{-- Inline Edit --}}
    <div id="ve{{ $v->id }}" style="display:none;margin-top:14px;border-top:1px solid var(--bdr);padding-top:14px;">
    <form method="POST" action="{{ route('admin.products.variants.update',[$product,$v]) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="g3">
        <div class="fg"><label class="lbl">Price (BTN)</label><input type="number" name="price" class="fc" step="0.01" value="{{ $v->price }}" required></div>
        <div class="fg"><label class="lbl">Stock Type</label>
            <select name="stock_type" class="fc ev-st" data-tgt="evq{{ $v->id }}">
                <option value="unlimited" {{ $v->stock_type==='unlimited'?'selected':'' }}>Unlimited</option>
                <option value="limited" {{ $v->stock_type==='limited'?'selected':'' }}>Limited</option>
            </select>
        </div>
        <div class="fg" id="evq{{ $v->id }}" style="{{ $v->stock_type==='limited'?'':'display:none;' }}"><label class="lbl">Quantity</label><input type="number" name="stock" class="fc" min="0" value="{{ $v->stock }}"></div>
    </div>
    <div class="g3">
        @foreach([1,2,3] as $i)@php $k="image_{$i}";$pfx="uv{$v->id}_"; @endphp
        <div class="fg">
            <label class="lbl">Image {{ $i }}</label>
            @if($v->$k)<img src="{{ asset('storage/'.$v->$k) }}" style="width:50px;height:50px;object-fit:cover;border-radius:5px;border:1px solid var(--bdr);margin-bottom:5px;display:block;"><label style="font-size:11px;display:flex;align-items:center;gap:4px;color:var(--err);margin-bottom:4px;cursor:pointer;"><input type="checkbox" name="remove_{{ $pfx }}image_{{ $i }}" value="1"> Remove</label>@endif
            <input type="file" name="{{ $pfx }}image_{{ $i }}" class="fc" accept="image/*">
        </div>
        @endforeach
    </div>
    <div style="display:flex;gap:8px;">
        <button type="submit" class="btn btn-pr btn-sm"><i class="fas fa-save"></i> Save</button>
        <button type="button" class="btn btn-sc btn-sm" onclick="toggleEdit('ve{{ $v->id }}')">Cancel</button>
    </div>
    </form>
    </div>
</div>
@empty
<div style="text-align:center;padding:30px;color:var(--mut);"><i class="fas fa-layer-group" style="font-size:28px;display:block;margin-bottom:8px;"></i>No variants yet. Add one above.</div>
@endforelse
</div>
</div>
</div>
@endif

{{-- TAB: Logs --}}
<div class="tab-panel" id="tab-logs" style="display:none;">
<div class="card">
<div class="card-hd"><h2>Recent Stock & Price Changes</h2><a href="{{ route('admin.products.stock-logs',$product) }}" class="btn btn-sc btn-xs">View All</a></div>
<div class="tw"><table>
<thead><tr><th>Date</th><th>Target</th><th>Old Stock</th><th>New Stock</th><th>Old Price</th><th>New Price</th><th>By</th></tr></thead>
<tbody>
@forelse($product->stockLogs as $log)
<tr>
    <td style="white-space:nowrap;font-size:12px;">{{ $log->created_at->format('d M Y H:i') }}</td>
    <td>{{ $log->variant?->name ?? 'Main Product' }}</td>
    <td>{{ $log->old_stock ?? '—' }}</td>
    <td>@if($log->new_stock!==null && $log->old_stock!==$log->new_stock)<strong style="color:{{ $log->new_stock>($log->old_stock??0)?'var(--ok)':'var(--err)' }};">{{ $log->new_stock }}</strong>@else{{ $log->new_stock ?? '—' }}@endif</td>
    <td>{{ $log->old_price ? 'BTN '.number_format($log->old_price,2) : '—' }}</td>
    <td>{{ $log->new_price ? 'BTN '.number_format($log->new_price,2) : '—' }}</td>
    <td style="font-size:12px;">{{ $log->changed_by }}</td>
</tr>
@empty
<tr><td colspan="7" style="text-align:center;color:var(--mut);padding:20px;">No logs yet.</td></tr>
@endforelse
</tbody>
</table></div>
</div>
</div>

@endsection
@push('scripts')
<script>
document.querySelectorAll('.tbtn').forEach(b=>b.addEventListener('click',()=>{
    document.querySelectorAll('.tbtn').forEach(x=>{x.classList.remove('active');x.style.color='';x.style.borderBottomColor='';});
    document.querySelectorAll('.tab-panel').forEach(p=>p.style.display='none');
    b.classList.add('active');
    const p=document.getElementById('tab-'+b.dataset.tab);
    if(p)p.style.display='';
}));
const est=document.getElementById('esttype'),esq=document.getElementById('estqty');
if(est)est.addEventListener('change',()=>{if(esq)esq.style.display=est.value==='limited'?'':'none';});
const nvst=document.getElementById('nvst'),nvqty=document.getElementById('nvqty');
if(nvst)nvst.addEventListener('change',()=>{if(nvqty)nvqty.style.display=nvst.value==='limited'?'':'none';});
document.querySelectorAll('.ev-st').forEach(el=>el.addEventListener('change',()=>{
    const t=document.getElementById(el.dataset.tgt);
    if(t)t.style.display=el.value==='limited'?'':'none';
}));
function toggleEdit(id){const e=document.getElementById(id);e.style.display=e.style.display==='none'?'':'none';}
function lp(i,n){const e=document.getElementById('imgP'+n);if(i.files&&i.files[0]){const u=URL.createObjectURL(i.files[0]);if(e.tagName==='IMG'){e.src=u;}else{const img=document.createElement('img');img.src=u;img.style='width:100%;height:100%;object-fit:cover;';e.replaceWith(img);}}}
function toggleFlatRate(suffix){const v=document.getElementById('shipping_type_'+suffix).value;const fr=document.getElementById('flat_rate_'+suffix);if(fr)fr.style.display=v==='flat_rate'?'':'none';}
</script>
<style>
.tbtn{padding:11px 18px;border:none;background:none;cursor:pointer;font-size:13.5px;font-weight:500;color:var(--mut);border-bottom:2px solid transparent;margin-bottom:-2px;transition:all .2s;}
.tbtn.active{color:var(--pr);border-bottom-color:var(--pr);}
.tbtn:hover{color:var(--txt);}
</style>
@endpush