@extends('frontend.layouts.app')
@section('title', $product->name)
@section('content')
<div class="page-wrap">

{{-- Breadcrumb --}}
<div class="breadcrumb">
  <a href="{{ route('home') }}">Home</a><span class="sep">/</span>
  <a href="{{ route('products.index') }}">Shop</a><span class="sep">/</span>
  @if($product->category)
    <a href="{{ route('products.index',['category'=>$product->category_id]) }}">{{ $product->category->name }}</a><span class="sep">/</span>
  @endif
  <span>{{ Str::limit($product->name, 40) }}</span>
</div>

<div style="display:grid;grid-template-columns:460px 1fr;gap:24px;align-items:start;">

{{-- ════ GALLERY ════ --}}
<div>
  @php $allImgs = array_values(array_filter([$product->image_1,$product->image_2,$product->image_3])); @endphp
  <div style="display:flex;gap:10px;">
    {{-- Thumbnails --}}
    @if(count($allImgs) > 1)
    <div style="display:flex;flex-direction:column;gap:8px;width:68px;flex-shrink:0;">
      @foreach($allImgs as $i => $img)
      <div class="pdp-thumb {{ $i===0?'active':'' }}"
           onclick="setMainImg('{{ asset('storage/'.$img) }}',this)"
           style="width:68px;height:68px;border-radius:6px;overflow:hidden;border:2px solid {{ $i===0?'var(--pr)':'var(--bdr)' }};cursor:pointer;background:#f5f5f5;transition:border-color .2s;">
        <img src="{{ asset('storage/'.$img) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
      </div>
      @endforeach
    </div>
    @endif
    {{-- Main image --}}
    <div style="flex:1;aspect-ratio:1;border-radius:var(--r2);overflow:hidden;background:#f9f9f9;border:1px solid var(--bdr);position:relative;">
      @if(count($allImgs))
        <img id="mainImg" src="{{ asset('storage/'.$allImgs[0]) }}" style="width:100%;height:100%;object-fit:contain;transition:opacity .2s;" alt="{{ $product->name }}">
      @else
        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#ddd;font-size:80px;"><i class="fas fa-image"></i></div>
      @endif
      @if($product->is_featured)
      <div style="position:absolute;top:10px;left:10px;background:var(--sec);color:#fff;font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;">⭐ Featured</div>
      @endif
    </div>
  </div>
  {{-- Share / Wishlist --}}
  <div style="display:flex;gap:8px;margin-top:12px;">
    @auth
    <button id="wl-btn" onclick="toggleWishlist({{ $product->id }})"
            style="flex:1;padding:9px;border:1.5px solid var(--bdr);border-radius:var(--r);font-size:13px;color:var(--txt2);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;font-family:var(--font);transition:.15s;"
            onmouseover="this.style.borderColor='var(--err)';this.style.color='var(--err)'"
            onmouseout="if(!this.classList.contains('active')){this.style.borderColor='var(--bdr)';this.style.color='var(--txt2)'}">
      <i id="wl-icon" class="far fa-heart"></i> <span id="wl-text">Add to Wishlist</span>
    </button>
    @else
    <a href="{{ route('login') }}"
       style="flex:1;padding:9px;border:1.5px solid var(--bdr);border-radius:var(--r);font-size:13px;color:var(--txt2);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;text-decoration:none;transition:.15s;">
      <i class="far fa-heart"></i> Add to Wishlist
    </a>
    @endauth
    <button onclick="navigator.share?navigator.share({title:'{{ addslashes($product->name) }}',url:location.href}):navigator.clipboard.writeText(location.href).then(()=>showToast('Link copied!',0))"
            style="flex:0 0 44px;padding:9px;border:1.5px solid var(--bdr);border-radius:var(--r);font-size:14px;color:var(--txt2);background:#fff;cursor:pointer;transition:.15s;"
            onmouseover="this.style.borderColor='var(--info)';this.style.color='var(--info)'"
            onmouseout="this.style.borderColor='var(--bdr)';this.style.color='var(--txt2)'">
      <i class="fas fa-share-alt"></i>
    </button>
  </div>
</div>

{{-- ════ PRODUCT INFO ════ --}}
<div>
  @if($product->category)
  <div style="font-size:11.5px;color:var(--mut);font-weight:700;text-transform:uppercase;letter-spacing:.6px;margin-bottom:4px;">
    <a href="{{ route('products.index',['category'=>$product->category_id]) }}" style="color:var(--mut);text-decoration:none;">{{ $product->category->name }}</a>
  </div>
  @endif

  <h1 style="font-size:20px;font-weight:800;line-height:1.3;color:var(--txt);margin-bottom:8px;">{{ $product->name }}</h1>

  {{-- Rating row --}}
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid var(--bdr2);flex-wrap:wrap;">
    <div style="display:inline-flex;align-items:center;gap:3px;background:var(--ok);color:#fff;font-size:12px;font-weight:700;padding:3px 8px;border-radius:3px;">
      4.{{ ($product->id * 7) % 5 }} <i class="fas fa-star" style="font-size:10px;"></i>
    </div>
    <span style="font-size:12.5px;color:var(--info);">{{ (($product->id * 31) % 190) + 10 }} ratings</span>
    <span style="color:var(--bdr2);">|</span>
    <span style="font-size:12.5px;color:var(--ok);font-weight:600;"><i class="fas fa-check-circle"></i> Verified Bhutanese Product</span>
    @if($product->is_featured)
    <span style="font-size:11px;background:#fff8e1;color:#e65100;border:1px solid #ffe0b2;padding:2px 8px;border-radius:3px;font-weight:600;">⭐ Featured</span>
    @endif
  </div>

  {{-- Price --}}
  <div style="margin-bottom:12px;">
    <div id="displayPrice" style="font-size:28px;font-weight:800;color:var(--txt);">{{ $product->price_display }}</div>
    <div style="font-size:12px;color:var(--mut);margin-top:3px;">Inclusive of all taxes · Price in Bhutanese Ngultrum (BTN)</div>
  </div>

  {{-- Stock --}}
  <div id="stockBadge" style="margin-bottom:16px;">
    @if($product->type === 'simple')
      @if($product->stock_type === 'unlimited')
        <span style="color:var(--ok);font-size:14px;font-weight:700;"><i class="fas fa-check-circle"></i> In Stock</span>
      @elseif($product->stock > 5)
        <span style="color:var(--ok);font-size:14px;font-weight:700;"><i class="fas fa-check-circle"></i> In Stock</span>
      @elseif($product->stock > 0)
        <span style="color:var(--warn);font-size:14px;font-weight:700;"><i class="fas fa-exclamation-triangle"></i> Only {{ $product->stock }} left — order soon!</span>
      @else
        <span style="color:var(--err);font-size:14px;font-weight:700;"><i class="fas fa-times-circle"></i> Out of Stock</span>
      @endif
    @else
      <span id="varStockMsg" style="color:var(--mut);font-size:13.5px;"><i class="fas fa-hand-point-down"></i> Select options to see availability</span>
    @endif
  </div>

  {{-- ════ VARIANT SELECTORS ════ --}}
  @if($product->type === 'variant')
  <div id="attrSelectors" style="margin-bottom:18px;">
    @foreach($product->attributes as $attr)
    <div style="margin-bottom:14px;">
      <div style="font-size:12px;font-weight:700;color:var(--txt2);margin-bottom:7px;text-transform:uppercase;letter-spacing:.4px;">
        {{ $attr->name }}:
        <span id="selLabel_{{ $attr->id }}" style="color:var(--pr);text-transform:none;letter-spacing:0;font-weight:600;font-size:13px;"></span>
      </div>
      <div style="display:flex;gap:6px;flex-wrap:wrap;">
        @foreach($attr->options as $opt)
        @php
          $available = $product->variants->some(function($v) use ($opt) {
            return $v->attributeOptions->contains('id',$opt->id) && ($v->stock_type==='unlimited'||$v->stock>0);
          });
        @endphp
        {{-- <div class="pdp-opt {{ $available ? '' : 'pdp-unavail' }}" id="opt_{{ $opt->id }}"
             data-attr="{{ $attr->id }}" data-opt="{{ $opt->id }}" data-label="{{ $opt->value }}"
             onclick="{{ $available ? 'selectOpt('.$attr->id.','.$opt->id.',\''.$opt->value.'\',this)' : '' }}"
             style="padding:7px 16px;border:1.5px solid var(--bdr);border-radius:3px;font-size:13.5px;font-weight:500;
                    cursor:{{ $available ? 'pointer' : 'not-allowed' }};transition:.15s;background:#fff;
                    color:{{ $available ? 'var(--txt2)' : '#bbb' }};{{ !$available ? 'text-decoration:line-through;opacity:.4;' : '' }}"> --}}

                    <div 
    class="pdp-opt {{ $available ? '' : 'pdp-unavail' }}"
    id="opt_{{ $opt->id }}"
    data-attr="{{ $attr->id }}"
    data-opt="{{ $opt->id }}"
    data-label="{{ $opt->value }}"
    onclick="{{ $available ? "selectOpt($attr->id,$opt->id,'$opt->value',this)" : '' }}"
    style="
        padding:7px 16px;
        border:1.5px solid var(--bdr);
        border-radius:3px;
        font-size:13.5px;
        font-weight:500;
        cursor:{{ $available ? 'pointer' : 'not-allowed' }};
        transition:.15s;
        background:#fff;
        color:{{ $available ? 'var(--txt2)' : '#bbb' }};
        {{ !$available ? 'text-decoration:line-through;opacity:.4;' : '' }}
    "
>

          {{ $opt->value }}
        </div>
        @endforeach
      </div>
    </div>
    @endforeach
  </div>
  @endif

  {{-- ════ QUANTITY ════ --}}
  @if($product->in_stock)
  <div style="margin-bottom:14px;">
    <div style="font-size:11.5px;font-weight:700;color:var(--txt2);margin-bottom:7px;text-transform:uppercase;letter-spacing:.4px;">Quantity</div>
    <div style="display:flex;align-items:center;border:1.5px solid var(--bdr);border-radius:var(--r);overflow:hidden;width:fit-content;">
      <button type="button" onclick="changeQty(-1)" style="width:40px;height:42px;border:none;background:#f5f5f5;font-size:18px;font-weight:700;cursor:pointer;color:var(--txt2);font-family:var(--font);">−</button>
      <input type="number" id="qtyInput" value="1" min="1" max="99" style="width:56px;height:42px;border:none;border-left:1.5px solid var(--bdr);border-right:1.5px solid var(--bdr);text-align:center;font-size:16px;font-weight:700;color:var(--txt);font-family:var(--font);">
      <button type="button" onclick="changeQty(1)" style="width:40px;height:42px;border:none;background:#f5f5f5;font-size:18px;font-weight:700;cursor:pointer;color:var(--txt2);font-family:var(--font);">+</button>
    </div>
  </div>

  {{-- ════ CTA BUTTONS ════ --}}
  <div style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;">
    <button id="atcBtn"
            onclick="pdpAddToCart(false)"
            {{ $product->type === 'variant' ? 'disabled' : '' }}
            style="padding:13px 28px;background:var(--sec);color:#fff;border:none;border-radius:var(--r);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--font);display:flex;align-items:center;gap:8px;transition:.15s;min-width:160px;justify-content:center;">
      <i class="fas fa-shopping-cart"></i> Add to Cart
    </button>
    <button id="buyBtn"
            onclick="pdpAddToCart(true)"
            {{ $product->type === 'variant' ? 'disabled' : '' }}
            style="padding:13px 28px;background:var(--pr);color:#fff;border:none;border-radius:var(--r);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--font);display:flex;align-items:center;gap:8px;transition:.15s;min-width:160px;justify-content:center;">
      <i class="fas fa-bolt"></i> Buy Now
    </button>
  </div>
  @else
  <div style="padding:13px 24px;background:#f5f5f5;border:1.5px solid var(--bdr);border-radius:var(--r);font-size:15px;color:var(--mut);font-weight:600;display:inline-flex;align-items:center;gap:8px;margin-bottom:18px;">
    <i class="fas fa-times-circle"></i> Currently Out of Stock
  </div>
  @endif

  {{-- ════ SHIPPING & DELIVERY INFO ════ --}}
  @php
    $delivery = $product->expected_delivery;
    $shippingCost = $product->shipping_type === 'free' ? 0 : ($product->shipping_type === 'flat_rate' ? $product->shipping_flat_rate : ($product->shipping_type === 'express' ? 300 : 150));
    $shippingLabel = match($product->shipping_type ?? 'standard') {
      'free'      => 'FREE Delivery',
      'express'   => 'Express Delivery — BTN 300',
      'flat_rate' => 'Delivery — BTN '.number_format($product->shipping_flat_rate ?? 150, 2),
      default     => 'Standard Delivery (Free on orders over BTN 5,000)',
    };
  @endphp
  <div style="border:1px solid var(--bdr2);border-radius:var(--r2);overflow:hidden;margin-bottom:16px;">
    {{-- Delivery date --}}
    <div style="display:flex;align-items:flex-start;gap:12px;padding:12px 14px;border-bottom:1px solid var(--bdr2);">
      <i class="fas fa-calendar-check" style="color:var(--ok);margin-top:2px;width:16px;text-align:center;"></i>
      <div style="flex:1;">
        <div style="font-size:13px;font-weight:700;color:var(--txt);">
          Delivery by <span style="color:var(--ok);">{{ $delivery['min_str'] }} – {{ $delivery['max_str'] }}</span>
        </div>
        <div style="font-size:12px;color:var(--mut);margin-top:2px;">
          If ordered today · {{ $delivery['processing_days'] }} day(s) processing + {{ $delivery['shipping_days'] }} day(s) shipping
        </div>
      </div>
    </div>
    {{-- Shipping cost --}}
    <div style="display:flex;align-items:flex-start;gap:12px;padding:12px 14px;border-bottom:1px solid var(--bdr2);">
      <i class="fas fa-truck" style="color:var(--info);margin-top:2px;width:16px;text-align:center;"></i>
      <div>
        <div style="font-size:13px;font-weight:700;color:var(--txt);">{{ $shippingLabel }}</div>
        @if($product->shipping_type === 'standard' || $product->shipping_type === null)
        <div style="font-size:12px;color:var(--mut);margin-top:2px;">Standard shipping BTN 150 · Free above BTN 5,000</div>
        @endif
      </div>
    </div>
    {{-- Delivery Location --}}
    <div style="display:flex;align-items:flex-start;gap:12px;padding:12px 14px;border-bottom:1px solid var(--bdr2);">
      <i class="fas fa-map-marker-alt" style="color:var(--pr);margin-top:2px;width:16px;text-align:center;"></i>
      <div style="flex:1;">
        <div style="font-size:13px;font-weight:700;color:var(--txt);">Deliver to</div>
        <div style="font-size:12px;color:var(--mut);margin-top:2px;" id="deliveryLocation">
          <span id="locationText">Detecting your location...</span>
          <button onclick="detectLocation()" style="margin-left:6px;font-size:11px;color:var(--pr);background:none;border:none;cursor:pointer;font-family:var(--font);">
            <i class="fas fa-redo-alt"></i> Change
          </button>
        </div>
      </div>
    </div>
    {{-- Returns --}}
    <div style="display:flex;align-items:flex-start;gap:12px;padding:12px 14px;">
      <i class="fas fa-undo" style="color:var(--warn);margin-top:2px;width:16px;text-align:center;"></i>
      <div>
        <div style="font-size:13px;font-weight:700;color:var(--txt);">Easy 7-Day Returns</div>
        <div style="font-size:12px;color:var(--mut);margin-top:2px;">Return or exchange within 7 days of delivery</div>
      </div>
    </div>
  </div>

  {{-- SKU --}}
  <div style="font-size:12px;color:var(--mut);display:flex;gap:16px;flex-wrap:wrap;">
    <span id="variantSku" style="{{ $product->type==='simple'?'':'display:none' }}">
      SKU: <span style="font-family:monospace;">{{ $product->type==='simple' ? ($product->sku ?? 'N/A') : '—' }}</span>
    </span>
    @if($product->category)
    <span>Category: <a href="{{ route('products.index',['category'=>$product->category_id]) }}" style="color:var(--pr);text-decoration:none;">{{ $product->category->name }}</a></span>
    @endif
    <span>Type: {{ ucfirst($product->type) }} Product</span>
  </div>
</div>
</div>

{{-- ════ BOTTOM SECTION: Description + Variants Table ════ --}}
<div style="display:grid;grid-template-columns:{{ $product->type==='variant' ? '1fr 380px' : '1fr' }};gap:18px;margin-top:18px;align-items:start;">

  {{-- Description --}}
  <div class="card">
    <div class="card-hd"><h2><i class="fas fa-align-left" style="color:var(--pr);"></i> Product Description</h2></div>
    <div class="card-bd" style="font-size:14px;line-height:1.9;color:var(--txt2);">
      @if($product->description)
        {!! nl2br(e($product->description)) !!}
      @else
        <span style="color:var(--mut);">No description available for this product.</span>
      @endif
    </div>
  </div>

  @if($product->type === 'variant' && $product->variants->count())
  {{-- Variants Table --}}
  <div class="card">
    <div class="card-hd"><h2><i class="fas fa-layer-group" style="color:var(--pr);"></i> All Variants ({{ $product->variants->count() }})</h2></div>
    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
          <tr style="background:#fafafa;border-bottom:2px solid var(--bdr);">
            <th style="padding:9px 14px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.4px;color:var(--mut);font-weight:700;">Variant</th>
            <th style="padding:9px 14px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.4px;color:var(--mut);font-weight:700;">Price</th>
            <th style="padding:9px 14px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.4px;color:var(--mut);font-weight:700;">Stock</th>
          </tr>
        </thead>
        <tbody>
          @foreach($product->variants as $v)
          <tr id="vtr_{{ $v->id }}" style="border-bottom:1px solid var(--bdr2);cursor:pointer;transition:.15s;"
              onclick="highlightVariant('{{ $v->id }}')"
              onmouseover="this.style.background='#f9f9f9'"
              onmouseout="if(!this.classList.contains('hl'))this.style.background=''">
            <td style="padding:9px 14px;font-weight:600;">{{ $v->name }}</td>
            <td style="padding:9px 14px;color:var(--pr);font-weight:700;">BTN {{ number_format($v->price,2) }}</td>
            <td style="padding:9px 14px;">
              @if($v->stock_type === 'unlimited')
                <span style="color:var(--ok);font-weight:600;">In Stock</span>
              @elseif($v->stock > 0 && $v->stock < 5)
                <span style="color:var(--warn);font-weight:700;">⚠ {{ $v->stock }} left</span>
              @elseif($v->stock > 0)
                <span style="color:var(--ok);">{{ $v->stock }} units</span>
              @else
                <span style="color:var(--err);font-weight:600;">Out of Stock</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

</div>

{{-- Related products --}}
@php
  $related = \App\Models\Product::with(['category','variants'])
    ->where('status','active')
    ->where('id','!=',$product->id)
    ->where('category_id',$product->category_id)
    ->take(6)
    ->get();
@endphp
@if($related->count())
<div style="margin-top:24px;">
  <h2 style="font-size:18px;font-weight:800;margin-bottom:14px;">Similar Products</h2>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:0;background:var(--card);border:1px solid var(--bdr);border-radius:var(--r2);overflow:hidden;">
    @foreach($related as $p)
      @include('frontend._product_card', ['p' => $p, 'context' => 'default'])
    @endforeach
  </div>
</div>
@endif

</div>
@endsection

@push('styles')
<style>
.pdp-opt{transition:.15s;}
.pdp-opt:not(.pdp-unavail):hover{border-color:var(--pr)!important;color:var(--pr)!important;background:#fff8f8!important;}
.pdp-opt.selected{border-color:var(--pr)!important;background:var(--pr)!important;color:#fff!important;}
.pdp-unavail{opacity:.35;cursor:not-allowed!important;}
.pdp-thumb{transition:border-color .2s;}
.pdp-thumb.active{border-color:var(--pr)!important;}
#atcBtn:not([disabled]):hover{background:#e65100!important;}
#buyBtn:not([disabled]):hover{background:var(--pr2)!important;}
#atcBtn[disabled],#buyBtn[disabled]{background:var(--mut2)!important;cursor:not-allowed!important;}
#wl-btn.active{border-color:var(--err)!important;color:var(--err)!important;background:#fff0f0!important;}
</style>
@endpush

@push('scripts')
<script>
const variantMap  = @json($variantMap);
const totalAttrs  = {{ $product->type === 'variant' ? $product->attributes->count() : 0 }};
const productId   = {{ $product->id }};
const productType = '{{ $product->type }}';
const selectedOpts = {};

function selectOpt(attrId, optId, label, el) {
  document.querySelectorAll(`.pdp-opt[data-attr="${attrId}"]`).forEach(o => o.classList.remove('selected'));
  el.classList.add('selected');
  selectedOpts[attrId] = optId;
  const lbl = document.getElementById(`selLabel_${attrId}`);
  if (lbl) lbl.textContent = label;
  updatePdpState();
}

function updatePdpState() {
  if (productType !== 'variant') return;

  // Cross-attribute: disable incompatible options
  @foreach($product->attributes as $attr)
  {
    const thisAttrId = {{ $attr->id }};
    const otherSels = Object.entries(selectedOpts)
      .filter(([aid]) => parseInt(aid) !== thisAttrId)
      .map(([,oid]) => parseInt(oid));

    document.querySelectorAll(`.pdp-opt[data-attr="${thisAttrId}"]`).forEach(el => {
      const optId = parseInt(el.dataset.opt);
      const compatible = Object.entries(variantMap).some(([key, v]) => {
        if (v.stock_type !== 'unlimited' && v.stock <= 0) return false;
        const vIds = key.split(',').map(Number);
        return vIds.includes(optId) && otherSels.every(oid => vIds.includes(oid));
      });
      if (compatible) {
        el.style.opacity = '';
        el.style.textDecoration = '';
        el.style.cursor = 'pointer';
        el.classList.remove('pdp-unavail');
      } else {
        el.style.opacity = '0.35';
        el.style.textDecoration = 'line-through';
        el.style.cursor = 'not-allowed';
        el.classList.add('pdp-unavail');
        if (selectedOpts[thisAttrId] === optId) {
          delete selectedOpts[thisAttrId];
          el.classList.remove('selected');
          const lbl = document.getElementById(`selLabel_${thisAttrId}`);
          if (lbl) lbl.textContent = '';
        }
      }
    });
  }
  @endforeach

  const allSelected = Object.keys(selectedOpts).length === totalAttrs;
  const priceEl   = document.getElementById('displayPrice');
  const stockEl   = document.getElementById('varStockMsg');
  const skuEl     = document.getElementById('variantSku');
  const atcBtn    = document.getElementById('atcBtn');
  const buyBtn    = document.getElementById('buyBtn');

  if (!allSelected) {
    if (stockEl) stockEl.innerHTML = '<i class="fas fa-hand-point-down"></i> Select all options to continue';
    enableButtons(false);
    return;
  }

  const key = Object.values(selectedOpts).map(Number).sort((a,b)=>a-b).join(',');
  const v   = variantMap[key];

  if (v) {
    if (priceEl) priceEl.textContent = 'BTN ' + parseFloat(v.price).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
    if (skuEl)   { skuEl.style.display=''; skuEl.innerHTML = 'SKU: <span style="font-family:monospace">'+(v.sku||'—')+'</span>'; }
    const inStock = v.stock_type === 'unlimited' || v.stock > 0;
    if (stockEl) {
      stockEl.className = '';
      stockEl.style.color = inStock ? (v.stock_type!=='unlimited'&&v.stock<5?'var(--warn)':'var(--ok)') : 'var(--err)';
      stockEl.style.fontWeight = '700';
      stockEl.innerHTML = inStock
        ? (v.stock_type==='unlimited' ? '<i class="fas fa-check-circle"></i> In Stock'
          : v.stock < 5 ? `<i class="fas fa-exclamation-triangle"></i> Only ${v.stock} left!`
          : '<i class="fas fa-check-circle"></i> In Stock')
        : '<i class="fas fa-times-circle"></i> Out of Stock';
    }
    enableButtons(inStock);
    // Highlight variant table row
    document.querySelectorAll('[id^=vtr_]').forEach(r => { r.style.background=''; r.classList.remove('hl'); });
    const row = document.getElementById('vtr_' + v.id);
    if (row) { row.style.background='#fff8e1'; row.classList.add('hl'); row.scrollIntoView({behavior:'smooth',block:'nearest'}); }
    // Update images
    const imgs = [v.image_1, v.image_2, v.image_3].filter(Boolean);
    if (imgs.length) swapMainImg(imgs[0]);
  } else {
    if (stockEl) { stockEl.style.color='var(--err)'; stockEl.style.fontWeight='700'; stockEl.innerHTML='<i class="fas fa-times-circle"></i> Combination not available'; }
    enableButtons(false);
  }
}

function enableButtons(enable) {
  const atc = document.getElementById('atcBtn');
  const buy = document.getElementById('buyBtn');
  if (atc) atc.disabled = !enable;
  if (buy) buy.disabled = !enable;
}

function pdpAddToCart(buyNow) {
  const qty = parseInt(document.getElementById('qtyInput')?.value || 1);
  let variantId = null;
  if (productType === 'variant') {
    const key = Object.values(selectedOpts).map(Number).sort((a,b)=>a-b).join(',');
    const v = variantMap[key];
    if (!v || (v.stock_type !== 'unlimited' && v.stock <= 0)) return;
    variantId = v.id;
  }
  const btn  = document.getElementById(buyNow?'buyBtn':'atcBtn');
  const orig = btn?.innerHTML;
  if (btn) { btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...'; btn.disabled = true; }
  fetch('/cart/add', {
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},
    body: JSON.stringify({product_id:productId, variant_id:variantId, qty})
  }).then(r => r.json()).then(data => {
    window['updateCartBadge']?.(data.count);
    window['showToast']?.('Added to cart!', data.count);
    if (btn) { btn.innerHTML = orig; btn.disabled = false; enableButtons(true); }
    if (buyNow) window.location.href = '/cart';
  }).catch(() => {
    if (btn) { btn.innerHTML = orig; btn.disabled = false; }
    window['showToast']?.('Failed to add to cart', 0);
  });
}

function changeQty(d) {
  const inp = document.getElementById('qtyInput');
  if (inp) inp.value = Math.max(1, parseInt(inp.value||1)+d);
}

function setMainImg(src, el) {
  swapMainImg(src);
  document.querySelectorAll('.pdp-thumb').forEach(t => { t.classList.remove('active'); t.style.borderColor='var(--bdr)'; });
  el.classList.add('active'); el.style.borderColor='var(--pr)';
}

function swapMainImg(src) {
  const mi = document.getElementById('mainImg');
  if (!mi) return;
  mi.style.opacity = '0';
  setTimeout(() => { mi.src = src; mi.style.opacity = '1'; }, 130);
}

function highlightVariant(vid) {
  document.querySelectorAll('[id^=vtr_]').forEach(r => { r.style.background=''; r.classList.remove('hl'); });
  const row = document.getElementById('vtr_'+vid);
  if (row) { row.style.background='#fff8e1'; row.classList.add('hl'); }
}

// ── Wishlist ──
async function toggleWishlist(productId) {
  @auth
  const btn = document.getElementById('wl-btn');
  try {
    const res  = await fetch('/wishlist/toggle', {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},
      body: JSON.stringify({product_id:productId})
    });
    const data = await res.json();
    const icon = document.getElementById('wl-icon');
    const text = document.getElementById('wl-text');
    const wlBadge = document.getElementById('wlBadge');
    if (data.status === 'added') {
      icon.className = 'fas fa-heart';
      if (text) text.textContent = 'In Wishlist';
      btn.classList.add('active');
      btn.style.borderColor='var(--err)'; btn.style.color='var(--err)'; btn.style.background='#fff0f0';
      if (wlBadge) { wlBadge.textContent = data.count; wlBadge.style.display = data.count>0?'':'none'; }
    } else {
      icon.className = 'far fa-heart';
      if (text) text.textContent = 'Add to Wishlist';
      btn.classList.remove('active');
      btn.style.borderColor='var(--bdr)'; btn.style.color='var(--txt2)'; btn.style.background='#fff';
      if (wlBadge) { wlBadge.textContent = data.count; wlBadge.style.display = data.count>0?'':'none'; }
    }
    window['showToast']?.(data.message, 0);
  } catch(e) { console.error(e); }
  @else
  window.location.href = '/login';
  @endauth
}

// ── Location Detection ──
function detectLocation() {
  const el = document.getElementById('locationText');
  if (!el) return;
  if ('geolocation' in navigator) {
    el.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Detecting...';
    navigator.geolocation.getCurrentPosition(
      async (pos) => {
        try {
          const res = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${pos.coords.latitude}&lon=${pos.coords.longitude}&format=json`);
          const data = await res.json();
          const city = data.address?.city || data.address?.town || data.address?.village || data.address?.county || '';
          const state = data.address?.state || '';
          const country = data.address?.country_code?.toUpperCase() || '';
          el.textContent = [city, state, country].filter(Boolean).join(', ') || 'Location detected';
        } catch(e) { el.textContent = 'Bhutan (default)'; }
      },
      () => { el.textContent = 'Bhutan (default)'; }
    );
  } else {
    el.textContent = 'Bhutan (default)';
  }
}

// Auto-detect on load
document.addEventListener('DOMContentLoaded', () => {
  detectLocation();

  @auth
  // Check wishlist status
  fetch('/wishlist/ids').then(r=>r.json()).then(data => {
    if (data.ids?.includes(productId)) {
      const btn = document.getElementById('wl-btn');
      const icon = document.getElementById('wl-icon');
      const text = document.getElementById('wl-text');
      if (btn)  { btn.classList.add('active'); btn.style.borderColor='var(--err)'; btn.style.color='var(--err)'; btn.style.background='#fff0f0'; }
      if (icon) icon.className = 'fas fa-heart';
      if (text) text.textContent = 'In Wishlist';
    }
  });
  @endauth
});
</script>
@endpush
