@extends('admin.layouts.app')
@section('title','Create Product')
@section('page-title','Create Product')
@section('topbar-actions')<a href="{{ route('admin.products.index') }}" class="btn btn-sc btn-sm"><i class="fas fa-arrow-left"></i> Back</a>@endsection
@section('content')
<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
@csrf
<div class="g2" style="align-items:start;grid-template-columns:2fr 1fr;">
<div>
<div class="card mb16">
<div class="card-hd"><h2>Basic Information</h2></div>
<div class="card-bd">
    <div class="fg"><label class="lbl">Name <span class="req">*</span></label><input name="name" class="fc" value="{{ old('name') }}" required></div>
    <div class="fg"><label class="lbl">Description</label><textarea name="description" class="fc" rows="4">{{ old('description') }}</textarea></div>
    <div class="g2">
        <div class="fg"><label class="lbl">Product Type <span class="req">*</span></label>
            <select name="type" id="ptype" class="fc" required>
                <option value="simple" {{ old('type')!=='variant'?'selected':'' }}>Simple</option>
                <option value="variant" {{ old('type')==='variant'?'selected':'' }}>Variant</option>
            </select>
        </div>
        <div class="fg"><label class="lbl">Category</label>
            <select name="category_id" class="fc"><option value="">— None —</option>@foreach($categories as $c)<option value="{{ $c->id }}" {{ old('category_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach</select>
        </div>
    </div>
    <div class="g2">
        <div class="fg"><label class="lbl">Status</label><select name="status" class="fc"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
        <div class="fg" style="padding-top:22px;"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;"><input type="checkbox" name="is_featured" value="1" {{ old('is_featured')?'checked':'' }}> <span class="lbl" style="margin:0;">Featured Product ⭐</span></label></div>
    </div>
    <div class="g3">
        <div class="fg"><label class="lbl">Shipping Type</label>
            <select name="shipping_type" id="shipping_type_create" class="fc" onchange="toggleFlatRate('create')">
                <option value="standard">Standard (BTN 150 / Free above 5000)</option>
                <option value="free">Always Free</option>
                <option value="express">Express (BTN 300)</option>
                <option value="flat_rate">Flat Rate</option>
            </select>
        </div>
        <div class="fg" id="flat_rate_create" style="display:none;"><label class="lbl">Flat Rate Amount (BTN)</label><input type="number" name="shipping_flat_rate" class="fc" step="0.01" min="0" value="{{ old('shipping_flat_rate',0) }}" placeholder="150.00"></div>
        <div class="fg"><label class="lbl">Processing Days</label><input type="number" name="processing_days" class="fc" min="0" max="30" value="{{ old('processing_days',1) }}" placeholder="1"></div>
    </div>
</div>
</div>

<div class="card mb16" id="simpleSection">
<div class="card-hd"><h2>Pricing & Stock</h2></div>
<div class="card-bd">
    <div class="g3">
        <div class="fg"><label class="lbl">Price (BTN) <span class="req">*</span></label><input type="number" name="price" class="fc" step="0.01" min="0" value="{{ old('price') }}" placeholder="0.00"></div>
        <div class="fg"><label class="lbl">Stock Type</label><select name="stock_type" id="stype" class="fc"><option value="unlimited">Unlimited</option><option value="limited" {{ old('stock_type')==='limited'?'selected':'' }}>Limited</option></select></div>
        <div class="fg" id="sqty" style="{{ old('stock_type')==='limited'?'':'display:none;' }}"><label class="lbl">Quantity</label><input type="number" name="stock" class="fc" min="0" value="{{ old('stock',0) }}"></div>
    </div>
</div>
</div>

<div class="card mb16" id="variantSection" style="display:none;">
<div class="card-hd"><h2>Attributes <span style="font-size:12px;color:var(--mut);">(select for variants)</span></h2></div>
<div class="card-bd">
@if($attributes->count())
<div class="g3">@foreach($attributes as $attr)
<label style="display:flex;align-items:flex-start;gap:8px;padding:10px;border:1px solid var(--bdr);border-radius:6px;cursor:pointer;">
    <input type="checkbox" name="attributes[]" value="{{ $attr->id }}" {{ in_array($attr->id,old('attributes',[]))?'checked':'' }} style="margin-top:2px;">
    <div><div style="font-weight:600;font-size:13px;">{{ $attr->name }}</div><div style="font-size:11px;color:var(--mut);">{{ $attr->options->pluck('value')->join(', ') }}</div></div>
</label>
@endforeach</div>
@else<p style="color:var(--mut);font-size:13px;">No attributes yet. <a href="{{ route('admin.attributes.index') }}" style="color:var(--pr);">Create first.</a></p>@endif
</div>
</div>
</div>

<div>
<div class="card">
<div class="card-hd"><h2>Product Images</h2></div>
<div class="card-bd">
<p class="text-mut mb16">Up to 3 images</p>
@foreach([1,2,3] as $i)
<div class="fg">
    <label class="lbl">Image {{ $i }}</label>
    <input type="file" name="image_{{ $i }}" class="fc" accept="image/*" onchange="prev(this,'p{{ $i }}')">
    <img id="p{{ $i }}" src="" style="display:none;width:80px;height:80px;object-fit:cover;border-radius:6px;margin-top:6px;border:1px solid var(--bdr);">
</div>
@endforeach
</div>
</div>
</div>
</div>

<div style="margin-top:16px;display:flex;justify-content:flex-end;gap:10px;">
    <a href="{{ route('admin.products.index') }}" class="btn btn-sc">Cancel</a>
    <button type="submit" class="btn btn-pr"><i class="fas fa-save"></i> Create Product</button>
</div>
</form>
@endsection
@push('scripts')
<script>
const pt=document.getElementById('ptype'),ss=document.getElementById('simpleSection'),vs=document.getElementById('variantSection'),st=document.getElementById('stype'),sq=document.getElementById('sqty');
function toggleType(){pt.value==='variant'?(ss.style.display='none',vs.style.display=''):(ss.style.display='',vs.style.display='none');}
pt.addEventListener('change',toggleType);toggleType();
st.addEventListener('change',()=>{sq.style.display=st.value==='limited'?'':'none';});
function prev(i,id){const e=document.getElementById(id);if(i.files&&i.files[0]){e.src=URL.createObjectURL(i.files[0]);e.style.display='';}}
function toggleFlatRate(suffix){const v=document.getElementById('shipping_type_'+suffix).value;document.getElementById('flat_rate_'+suffix).style.display=v==='flat_rate'?'':'none';}
</script>
@endpush
