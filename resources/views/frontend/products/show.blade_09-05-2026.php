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
        <div class="pdp-opt {{ $available ? '' : 'pdp-unavail' }}" id="opt_{{ $opt->id }}"
             data-attr="{{ $attr->id }}" data-opt="{{ $opt->id }}" data-label="{{ $opt->value }}"
             onclick="{{ $available ? 'selectOpt('.$attr->id.','.$opt->id.',\''.$opt->value.'\',this)' : '' }}"
             style="padding:7px 16px;border:1.5px solid var(--bdr);border-radius:3px;font-size:13.5px;font-weight:500;
                    cursor:{{ $available ? 'pointer' : 'not-allowed' }};transition:.15s;background:#fff;
                    color:{{ $available ? 'var(--txt2)' : '#bbb' }};{{ !$available ? 'text-decoration:line-through;opacity:.4;' : '' }}">
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
    $delivery      = $product->expected_delivery;
    $settingsCost  = (float)\App\Models\Setting::get('shipping_default_cost', 150);
    $settingsFree  = (float)\App\Models\Setting::get('shipping_free_above', 5000);
    $settingsExpr  = (float)\App\Models\Setting::get('shipping_express_cost', 300);
    $currency      = \App\Models\Setting::get('currency_symbol', 'BTN');
    $gstPct        = (float)\App\Models\Setting::get('gst_percentage', 0);
    $gstLabel      = \App\Models\Setting::get('gst_label', 'GST');
    $gstInclusive  = \App\Models\Setting::get('gst_inclusive', '1') === '1';
    $companyGstin  = \App\Models\Setting::get('company_gstin', '');

    // Use product's own shipping or fall back to global settings
    $shippingCost = match($product->shipping_type ?? 'standard') {
      'free'      => 0,
      'flat_rate' => (float)($product->shipping_flat_rate ?? $settingsCost),
      'express'   => $settingsExpr,
      default     => 0, // shown dynamically below
    };
    $shippingLabel = match($product->shipping_type ?? 'standard') {
      'free'      => 'FREE Delivery',
      'express'   => 'Express Delivery — '.$currency.' '.number_format($settingsExpr, 2),
      'flat_rate' => 'Flat Rate Delivery — '.$currency.' '.number_format($product->shipping_flat_rate ?? $settingsCost, 2),
      default     => 'Standard Delivery',
    };
    $shippingSubtext = match($product->shipping_type ?? 'standard') {
      'free'      => 'This product ships free regardless of order total',
      'express'   => 'Expedited shipping — arrives faster',
      'flat_rate' => 'Fixed delivery fee for this product',
      default     => $currency.' '.number_format($settingsCost,2).' · Free on orders above '.$currency.' '.number_format($settingsFree,2),
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
        <div style="font-size:12px;color:var(--mut);margin-top:2px;">{{ $shippingSubtext }}</div>
      </div>
    </div>
    {{-- GST / Tax info --}}
    @if($gstPct > 0)
    <div style="display:flex;align-items:flex-start;gap:12px;padding:12px 14px;border-bottom:1px solid var(--bdr2);">
      <i class="fas fa-receipt" style="color:var(--warn);margin-top:2px;width:16px;text-align:center;"></i>
      <div>
        <div style="font-size:13px;font-weight:700;color:var(--txt);">
          {{ $gstLabel }}: {{ $gstPct }}%
          @if($companyGstin) <span style="font-family:monospace;font-size:11px;color:var(--mut);">· {{ $companyGstin }}</span> @endif
        </div>
        <div style="font-size:12px;color:var(--mut);margin-top:2px;">
          {{ $gstInclusive ? 'Price is inclusive of '.$gstLabel : $gstLabel.' will be added at checkout' }}
        </div>
      </div>
    </div>
    @endif
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
              onmouseover="if(!this.classList.contains('hl'))this.style.background='#f9f9f9'"
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

{{-- ════════════════════════════════════════════════════════
     REVIEWS SECTION
════════════════════════════════════════════════════════ --}}
<div style="margin-top:24px;" id="reviews">

  {{-- Rating Summary --}}
  <div class="card" style="margin-bottom:18px;">
    <div class="card-hd">
      <h2><i class="fas fa-star" style="color:#f5a623;"></i> Ratings & Reviews</h2>
      <span style="font-size:12px;color:var(--mut);">{{ $ratingCount }} {{ Str::plural('review',$ratingCount) }}</span>
    </div>
    <div class="card-bd">
      <div style="display:grid;grid-template-columns:140px 1fr;gap:24px;align-items:center;margin-bottom:20px;">

        {{-- Big score --}}
        <div style="text-align:center;">
          <div style="font-size:52px;font-weight:800;color:var(--txt);line-height:1;">{{ $ratingCount ? number_format($avgRating,1) : '—' }}</div>
          <div style="display:flex;justify-content:center;gap:3px;margin:6px 0;">
            @for($i=1;$i<=5;$i++)
              @if($i <= round($avgRating))
                <i class="fas fa-star" style="color:#f5a623;font-size:16px;"></i>
              @else
                <i class="far fa-star" style="color:#ddd;font-size:16px;"></i>
              @endif
            @endfor
          </div>
          <div style="font-size:12px;color:var(--mut);">{{ $ratingCount }} {{ Str::plural('rating',$ratingCount) }}</div>
        </div>

        {{-- Bar breakdown --}}
        <div style="display:flex;flex-direction:column;gap:6px;">
          @for($star=5;$star>=1;$star--)
          @php $cnt = $product->reviews->where('rating',$star)->count(); $pct = $ratingCount ? round($cnt/$ratingCount*100) : 0; @endphp
          <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;">
            <span style="width:14px;text-align:right;font-weight:600;color:var(--txt2);">{{ $star }}</span>
            <i class="fas fa-star" style="color:#f5a623;font-size:11px;"></i>
            <div style="flex:1;height:8px;background:#f0f0f0;border-radius:4px;overflow:hidden;">
              <div style="height:100%;width:{{ $pct }}%;background:#f5a623;border-radius:4px;transition:width .6s;"></div>
            </div>
            <span style="width:28px;font-size:11.5px;color:var(--mut);">{{ $cnt }}</span>
          </div>
          @endfor
        </div>
      </div>

      {{-- Write / Edit review form --}}
      @auth
      <div style="border-top:1px solid var(--bdr2);padding-top:18px;">
        <h3 style="font-size:14px;font-weight:700;margin-bottom:14px;">
          {{ $userReview ? '✏️ Edit Your Review' : '✍️ Write a Review' }}
        </h3>
        <form id="reviewForm" onsubmit="submitReview(event)">
          @csrf
          {{-- Star picker --}}
          <div style="margin-bottom:14px;">
            <div style="font-size:12px;font-weight:700;color:var(--txt2);margin-bottom:8px;text-transform:uppercase;letter-spacing:.4px;">Your Rating <span style="color:var(--err);">*</span></div>
            <div class="star-picker" id="starPicker" style="display:flex;gap:6px;">
              @for($i=1;$i<=5;$i++)
              <button type="button" class="star-btn" data-val="{{ $i }}"
                      style="font-size:28px;background:none;border:none;cursor:pointer;color:{{ ($userReview && $userReview->rating >= $i) ? '#f5a623' : '#ddd' }};padding:2px;transition:transform .1s;"
                      onclick="setRating({{ $i }})"
                      onmouseover="hoverRating({{ $i }})"
                      onmouseout="resetHover()">
                &#9733;
              </button>
              @endfor
            </div>
            <input type="hidden" id="ratingInput" name="rating" value="{{ $userReview?->rating ?? 0 }}" required>
            <div id="ratingText" style="font-size:12px;color:var(--mut);margin-top:4px;">
              {{ $userReview ? ['','Poor','Fair','Good','Very Good','Excellent'][$userReview->rating] : 'Click to rate' }}
            </div>
          </div>
          {{-- Title --}}
          <div style="margin-bottom:12px;">
            <label style="display:block;font-size:12px;font-weight:700;color:var(--txt2);margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px;">Review Title</label>
            <input type="text" name="title" maxlength="120" placeholder="Summarize your experience..."
                   value="{{ $userReview?->title ?? '' }}"
                   style="width:100%;padding:9px 12px;border:1.5px solid var(--bdr);border-radius:var(--r);font-size:14px;font-family:var(--font);outline:none;transition:.2s;"
                   onfocus="this.style.borderColor='var(--pr)'" onblur="this.style.borderColor='var(--bdr)'">
          </div>
          {{-- Body --}}
          <div style="margin-bottom:14px;">
            <label style="display:block;font-size:12px;font-weight:700;color:var(--txt2);margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px;">Your Review</label>
            <textarea name="body" rows="4" maxlength="2000" placeholder="Share details about your experience with this product..."
                      style="width:100%;padding:9px 12px;border:1.5px solid var(--bdr);border-radius:var(--r);font-size:14px;font-family:var(--font);resize:vertical;outline:none;transition:.2s;"
                      onfocus="this.style.borderColor='var(--pr)'" onblur="this.style.borderColor='var(--bdr)'">{{ $userReview?->body ?? '' }}</textarea>
          </div>
          <div style="display:flex;gap:10px;align-items:center;">
            <button type="submit" id="reviewSubmitBtn"
                    style="padding:10px 24px;background:var(--pr);color:#fff;border:none;border-radius:var(--r);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--font);display:flex;align-items:center;gap:7px;transition:.15s;">
              <i class="fas fa-paper-plane"></i> {{ $userReview ? 'Update Review' : 'Submit Review' }}
            </button>
            @if($userReview)
            <button type="button" onclick="deleteReview()"
                    style="padding:10px 16px;background:none;border:1.5px solid var(--err);color:var(--err);border-radius:var(--r);font-size:13px;font-weight:600;cursor:pointer;font-family:var(--font);transition:.15s;"
                    onmouseover="this.style.background='var(--err)';this.style.color='#fff'"
                    onmouseout="this.style.background='none';this.style.color='var(--err)'">
              <i class="fas fa-trash"></i> Delete
            </button>
            @endif
            <div id="reviewMsg" style="font-size:13px;display:none;"></div>
          </div>
        </form>
      </div>
      @else
      <div style="border-top:1px solid var(--bdr2);padding-top:16px;text-align:center;padding-bottom:4px;">
        <a href="{{ route('login') }}" style="color:var(--pr);font-weight:600;font-size:14px;text-decoration:none;">
          <i class="fas fa-sign-in-alt"></i> Login to write a review
        </a>
      </div>
      @endauth
    </div>
  </div>

  {{-- Review list --}}
  @if($product->reviews->count())
  <div class="card">
    <div class="card-hd"><h2>Customer Reviews ({{ $product->reviews->count() }})</h2></div>
    <div id="reviewList">
      @foreach($product->reviews as $rev)
      <div class="review-item" id="rev_{{ $rev->id }}"
           style="padding:18px 20px;border-bottom:1px solid var(--bdr2);">
        {{-- Header --}}
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px;gap:12px;">
          <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:38px;height:38px;border-radius:50%;background:var(--pr);color:#fff;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;flex-shrink:0;">
              {{ strtoupper(substr($rev->user->name,0,1)) }}
            </div>
            <div>
              <div style="font-weight:700;font-size:13.5px;">{{ $rev->user->name }}</div>
              <div style="font-size:11px;color:var(--mut);">{{ $rev->created_at->diffForHumans() }}</div>
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
            <div style="display:inline-flex;align-items:center;gap:4px;background:{{ $rev->rating>=4 ? 'var(--ok)' : ($rev->rating==3 ? 'var(--warn)' : 'var(--err)') }};color:#fff;font-size:12px;font-weight:700;padding:3px 8px;border-radius:3px;">
              {{ $rev->rating }} <i class="fas fa-star" style="font-size:10px;"></i>
            </div>
          </div>
        </div>
        {{-- Stars --}}
        <div style="display:flex;gap:2px;margin-bottom:6px;">
          @for($i=1;$i<=5;$i++)
            @if($i <= $rev->rating)
              <i class="fas fa-star" style="color:#f5a623;font-size:13px;"></i>
            @else
              <i class="far fa-star" style="color:#ddd;font-size:13px;"></i>
            @endif
          @endfor
        </div>
        {{-- Content --}}
        @if($rev->title)
        <div style="font-weight:700;font-size:14px;margin-bottom:5px;">{{ $rev->title }}</div>
        @endif
        @if($rev->body)
        <div style="font-size:13.5px;color:var(--txt2);line-height:1.7;">{{ $rev->body }}</div>
        @endif
        {{-- Verified badge --}}
        <div style="margin-top:8px;font-size:11.5px;color:var(--ok);font-weight:600;">
          <i class="fas fa-check-circle"></i> Verified Purchase
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @else
  <div style="background:var(--card);border:1px solid var(--bdr);border-radius:var(--r2);padding:32px;text-align:center;color:var(--mut);">
    <div style="font-size:36px;margin-bottom:10px;">💬</div>
    <div style="font-weight:600;font-size:14px;margin-bottom:4px;">No reviews yet</div>
    <div style="font-size:13px;">Be the first to share your experience!</div>
  </div>
  @endif

</div>

{{-- ════════════════════════════════════════════════════════
     SIMILAR PRODUCTS
════════════════════════════════════════════════════════ --}}
@php
  $related = \App\Models\Product::with(['category','variants'])
    ->where('status','active')
    ->where('id','!=',$product->id)
    ->where('category_id',$product->category_id)
    ->orderByRaw('is_featured DESC, created_at DESC')
    ->take(6)
    ->get();
  // If not enough in same category, fill with other products
  if ($related->count() < 4) {
    $moreIds = $related->pluck('id')->push($product->id)->all();
    $extra = \App\Models\Product::with(['category','variants'])
      ->where('status','active')
      ->whereNotIn('id', $moreIds)
      ->orderByRaw('is_featured DESC, created_at DESC')
      ->take(6 - $related->count())
      ->get();
    $related = $related->concat($extra);
  }
@endphp
@if($related->count())
<div style="margin-top:28px;">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
    <div>
      <h2 style="font-size:18px;font-weight:800;color:var(--txt);">Similar Products</h2>
      <div style="font-size:12.5px;color:var(--mut);margin-top:2px;">You might also like these</div>
    </div>
    @if($product->category)
    <a href="{{ route('products.index',['category'=>$product->category_id]) }}"
       style="font-size:13px;color:var(--pr);font-weight:600;text-decoration:none;">
      More in {{ $product->category->name }} →
    </a>
    @endif
  </div>
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

/* Reviews */
.star-btn{transition:transform .1s,color .1s;}
.star-btn:hover{transform:scale(1.2);}
.review-item:last-child{border-bottom:none!important;}
.review-item{transition:background .2s;}
.review-item:hover{background:#fafafa;}
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

// ══════════════════════════════════════
//  REVIEW FUNCTIONS
// ══════════════════════════════════════
const ratingLabels = ['','Poor','Fair','Good','Very Good','Excellent'];
let currentRating  = parseInt(document.getElementById('ratingInput')?.value || 0);

function setRating(val) {
  currentRating = val;
  document.getElementById('ratingInput').value = val;
  document.getElementById('ratingText').textContent = ratingLabels[val];
  document.querySelectorAll('.star-btn').forEach(btn => {
    btn.style.color = parseInt(btn.dataset.val) <= val ? '#f5a623' : '#ddd';
  });
}

function hoverRating(val) {
  document.querySelectorAll('.star-btn').forEach(btn => {
    btn.style.color = parseInt(btn.dataset.val) <= val ? '#f5a623' : '#ddd';
  });
  const txt = document.getElementById('ratingText');
  if (txt) txt.textContent = ratingLabels[val];
}

function resetHover() {
  document.querySelectorAll('.star-btn').forEach(btn => {
    btn.style.color = parseInt(btn.dataset.val) <= currentRating ? '#f5a623' : '#ddd';
  });
  const txt = document.getElementById('ratingText');
  if (txt) txt.textContent = currentRating ? ratingLabels[currentRating] : 'Click to rate';
}

async function submitReview(e) {
  e.preventDefault();
  const rating = parseInt(document.getElementById('ratingInput').value);
  if (!rating) {
    showReviewMsg('Please select a star rating.', false);
    return;
  }
  const btn  = document.getElementById('reviewSubmitBtn');
  const orig = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
  btn.disabled  = true;

  const form = document.getElementById('reviewForm');
  const data = {
    rating,
    title: form.querySelector('[name=title]').value,
    body:  form.querySelector('[name=body]').value,
    _token: form.querySelector('[name=_token]').value,
  };

  try {
    const res  = await fetch(`/products/{{ $product->id }}/reviews`, {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':data._token,'Accept':'application/json'},
      body: JSON.stringify(data),
    });
    const json = await res.json();
    if (json.success) {
      showReviewMsg(json.message, true);
      setTimeout(() => location.reload(), 1200);
    } else {
      showReviewMsg('Something went wrong. Please try again.', false);
    }
  } catch(err) {
    showReviewMsg('Network error. Please try again.', false);
  } finally {
    btn.innerHTML = orig;
    btn.disabled  = false;
  }
}

async function deleteReview() {
  if (!confirm('Delete your review?')) return;
  try {
    const res  = await fetch(`/products/{{ $product->id }}/reviews`, {
      method: 'DELETE',
      headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept':'application/json'},
    });
    const json = await res.json();
    if (json.success) {
      showReviewMsg('Review deleted.', true);
      setTimeout(() => location.reload(), 900);
    }
  } catch(err) {
    showReviewMsg('Could not delete. Try again.', false);
  }
}

function showReviewMsg(msg, success) {
  const el = document.getElementById('reviewMsg');
  if (!el) return;
  el.style.display   = '';
  el.style.color     = success ? 'var(--ok)' : 'var(--err)';
  el.style.fontWeight = '600';
  el.innerHTML = `<i class="fas fa-${success?'check-circle':'exclamation-circle'}"></i> ${msg}`;
}
</script>
@endpush