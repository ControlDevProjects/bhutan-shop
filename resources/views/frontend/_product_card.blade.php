@php
  /* ─── Smart variant selection ─── */
  $isVariant = $p->type === 'variant';
  $isSimple  = $p->type === 'simple';

  // Find cheapest available variant
  $cheapestVariant = null;
  $availableVariants = collect();
  if ($isVariant) {
      $availableVariants = $p->variants->filter(fn($v) => $v->stock_type === 'unlimited' || $v->stock > 0)->sortBy('price');
      $cheapestVariant   = $availableVariants->first();
  }

  $isInStock = $p->in_stock;

  // Display price: cheapest available variant, or product price
  $displayPrice = $isVariant
      ? ($cheapestVariant ? 'BTN ' . number_format($cheapestVariant->price, 2) : $p->price_display)
      : $p->price_display;

  $priceRange = '';
  if ($isVariant && $p->variants->count() > 0) {
      $min = $p->variants->min('price');
      $max = $p->variants->max('price');
      $priceRange = $min != $max ? 'BTN '.number_format($min,2).' – BTN '.number_format($max,2) : '';
  }

  // Badge label based on context
  $contextBadge = match($context ?? 'default') {
    'new'        => ['🆕 New', '#1565c0', '#e3f2fd'],
    'bestseller' => ['🏆 Best Seller', '#4a148c', '#f3e5f5'],
    'featured'   => ['⭐ Featured', '#e65100', '#fff3e0'],
    'deals'      => ['🔥 Low Stock', '#c62828', '#ffebee'],
    default      => null,
  };
@endphp
<div class="prod-card" style="border-right:1px solid var(--bdr2);border-bottom:1px solid var(--bdr2);">

  {{-- ── Image area ── --}}
  <div class="prod-img-wrap">
    <a href="{{ route('products.show', $p->slug) }}" style="display:block;height:100%;">
      @if($p->primary_image)
        <img src="{{ asset('storage/'.$p->primary_image) }}" alt="{{ $p->name }}" loading="lazy"
             style="width:100%;height:100%;object-fit:cover;transition:transform .35s ease;">
      @else
        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#e0e0e0;font-size:48px;background:#fafafa;">
          <i class="fas fa-image"></i>
        </div>
      @endif
    </a>

    {{-- Top-left badges --}}
    <div style="position:absolute;top:8px;left:8px;display:flex;flex-direction:column;gap:4px;z-index:1;">
      @if($contextBadge)
        <span style="font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;background:{{ $contextBadge[2] }};color:{{ $contextBadge[1] }};border:1px solid {{ $contextBadge[1] }}25;line-height:1.5;">
          {{ $contextBadge[0] }}
        </span>
      @elseif($p->is_featured)
        <span style="font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;background:#fff3e0;color:#e65100;line-height:1.5;">⭐ Featured</span>
      @endif
      @if(!$isInStock)
        <span style="font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;background:#ffebee;color:#c62828;line-height:1.5;">Out of Stock</span>
      @elseif($p->is_low_stock)
        <span style="font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;background:#fff3e0;color:#e65100;line-height:1.5;">⚠ Low Stock</span>
      @endif
    </div>

    {{-- Wishlist btn --}}
    @auth
    <button class="card-wishlist wl-card-btn" id="wlcard_{{ $p->id }}" title="Add to Wishlist"
            onclick="event.stopPropagation();cardToggleWishlist({{ $p->id }},this)">
      <i class="far fa-heart" id="wlcardicon_{{ $p->id }}"></i>
    </button>
    @else
    <a href="{{ route('login') }}" class="card-wishlist" title="Login to add to Wishlist" onclick="event.stopPropagation()">
      <i class="far fa-heart"></i>
    </a>
    @endauth

    {{-- ── Hover action bar ── --}}
    <div class="card-actions">
      @if($isInStock)
        <button class="btn-atc"
          @if($isSimple)
            onclick="directAddToCart({{ $p->id }}, null, this)"
          @else
            onclick="openQAP({{ $p->id }})"
          @endif
          style="flex:1;">
          <i class="fas fa-shopping-cart"></i>
          {{ $isSimple ? 'Add to Cart' : 'Select Options' }}
        </button>
      @else
        <button class="btn-atc" disabled style="flex:1;background:#bdbdbd;cursor:not-allowed;">
          <i class="fas fa-times-circle"></i> Out of Stock
        </button>
      @endif
      <a href="{{ route('products.show',$p->slug) }}" class="btn-atc btn-view" title="View Details">
        <i class="fas fa-eye"></i>
      </a>
    </div>
  </div>

  {{-- ── Card body ── --}}
  <div class="card-body">
    @if($p->category)
      <div style="font-size:10.5px;color:var(--mut);font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">
        {{ $p->category->name }}
      </div>
    @endif

    <div style="font-size:13px;font-weight:600;color:var(--txt);line-height:1.3;margin-bottom:6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
      <a href="{{ route('products.show',$p->slug) }}" style="color:inherit;text-decoration:none;"
         onmouseover="this.style.color='var(--pr)'" onmouseout="this.style.color='inherit'">
        {{ $p->name }}
      </a>
    </div>

    {{-- Ratings (decorative) --}}
    <div style="display:flex;align-items:center;gap:5px;margin-bottom:6px;">
      <span style="background:var(--ok);color:#fff;font-size:10.5px;font-weight:700;padding:1px 5px;border-radius:3px;display:inline-flex;align-items:center;gap:2px;">
        4.{{ ($p->id * 7) % 5 }} <i class="fas fa-star" style="font-size:9px;"></i>
      </span>
      <span style="font-size:11px;color:var(--mut);">({{ (($p->id * 31) % 190) + 10 }})</span>
    </div>

    {{-- Price --}}
    <div style="display:flex;align-items:baseline;gap:7px;flex-wrap:wrap;margin-bottom:5px;">
      <span style="font-size:16px;font-weight:800;color:var(--txt);">
        {{ $displayPrice }}
      </span>
      @if($isVariant && $priceRange)
        <span style="font-size:11px;color:var(--mut);">{{ $priceRange }}</span>
      @endif
    </div>

    {{-- Stock info --}}
    @if(!$isInStock)
      <div style="font-size:11.5px;color:var(--err);font-weight:600;margin-bottom:6px;">
        <i class="fas fa-times-circle"></i> Out of Stock
      </div>
    @elseif($isSimple && $p->stock_type === 'limited' && $p->stock > 0 && $p->stock <= 5)
      <div style="font-size:11.5px;color:var(--warn);font-weight:700;margin-bottom:6px;">
        <i class="fas fa-exclamation-triangle"></i> Only {{ $p->stock }} left!
      </div>
    @else
      <div style="font-size:11.5px;color:var(--ok);font-weight:600;margin-bottom:6px;">
        <i class="fas fa-check-circle"></i> In Stock
      </div>
    @endif

    {{-- Variant chips: show available options with color coding --}}
    @if($isVariant && $p->variants->count() > 0)
      <div style="display:flex;gap:4px;flex-wrap:wrap;margin-bottom:8px;min-height:20px;">
        @foreach($p->variants->take(5) as $v)
          @php $vAvail = $v->stock_type === 'unlimited' || $v->stock > 0; @endphp
          <span style="padding:2px 7px;border-radius:3px;font-size:10.5px;font-weight:600;
                border:1px solid {{ $vAvail ? '#c8e6c9' : '#e0e0e0' }};
                background:{{ $vAvail ? '#e8f5e9' : '#fafafa' }};
                color:{{ $vAvail ? '#2e7d32' : '#bdbdbd' }};
                {{ !$vAvail ? 'text-decoration:line-through;' : '' }}">
            {{ $v->name }}
          </span>
        @endforeach
        @if($p->variants->count() > 5)
          <span style="font-size:10.5px;color:var(--mut);padding:2px 5px;">+{{ $p->variants->count()-5 }}</span>
        @endif
      </div>
    @endif

    {{-- ── Bottom CTA button ── --}}
    <div style="margin-top:auto;">
      @if($isInStock)
        @if($isSimple)
          <button
            onclick="directAddToCart({{ $p->id }}, null, this)"
            style="width:100%;padding:8px;background:var(--pr-lt);color:var(--pr);border:1.5px solid #ffcdd2;
                   border-radius:3px;font-size:12.5px;font-weight:700;cursor:pointer;
                   font-family:var(--font);transition:.15s;display:flex;align-items:center;justify-content:center;gap:5px;"
            onmouseover="this.style.background='var(--pr)';this.style.color='#fff';this.style.borderColor='var(--pr)'"
            onmouseout="this.style.background='var(--pr-lt)';this.style.color='var(--pr)';this.style.borderColor='#ffcdd2'">
            <i class="fas fa-shopping-cart"></i> Add to Cart
          </button>
        @else
          {{-- Variant product: show cheapest available + open QAP --}}
          <button
            onclick="openQAP({{ $p->id }})"
            style="width:100%;padding:8px;background:#fff8e1;color:#e65100;border:1.5px solid #ffcc02;
                   border-radius:3px;font-size:12.5px;font-weight:700;cursor:pointer;
                   font-family:var(--font);transition:.15s;display:flex;align-items:center;justify-content:center;gap:5px;"
            onmouseover="this.style.background='var(--sec)';this.style.color='#fff';this.style.borderColor='var(--sec)'"
            onmouseout="this.style.background='#fff8e1';this.style.color='#e65100';this.style.borderColor='#ffcc02'">
            <i class="fas fa-layer-group"></i>
            @if($cheapestVariant)
              Add from BTN {{ number_format($cheapestVariant->price,2) }}
            @else
              Select Options
            @endif
          </button>
        @endif
      @else
        <button disabled
          style="width:100%;padding:8px;background:#f5f5f5;color:#bdbdbd;border:1.5px solid #e0e0e0;
                 border-radius:3px;font-size:12.5px;font-weight:600;cursor:not-allowed;
                 font-family:var(--font);display:flex;align-items:center;justify-content:center;gap:5px;">
          <i class="fas fa-bell"></i> Out of Stock
        </button>
      @endif
    </div>
  </div>
</div>
