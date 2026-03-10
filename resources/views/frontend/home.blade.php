@extends('frontend.layouts.app')
@section('title', 'BhutanShop — Kingdom Store')
@section('content')

{{-- ═══════════════════════════════════════════════
     HERO BANNER
═══════════════════════════════════════════════ --}}
<div style="background:linear-gradient(135deg,#c0392b 0%,#6c1a14 60%,#1a1a2e 100%);padding:48px 0;margin-bottom:0;">
  <div style="max-width:1300px;margin:0 auto;padding:0 20px;display:flex;align-items:center;gap:40px;flex-wrap:wrap;">
    <div style="flex:1;min-width:280px;">
      <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.12);color:rgba(255,255,255,.9);padding:5px 14px;border-radius:20px;font-size:12px;font-weight:600;letter-spacing:.5px;margin-bottom:16px;text-transform:uppercase;">
        🐉 Kingdom of Bhutan's Finest
      </div>
      <h1 style="color:#fff;font-size:40px;font-weight:800;line-height:1.15;margin-bottom:14px;letter-spacing:-.5px;">
        Authentic Bhutanese<br><span style="color:#ffd54f;">Products</span> Delivered
      </h1>
      <p style="color:rgba(255,255,255,.75);font-size:15px;line-height:1.8;margin-bottom:24px;max-width:480px;">
        Handcrafted textiles, traditional goods, and modern essentials — all from the Land of the Thunder Dragon.
      </p>
      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('products.index') }}" style="display:inline-flex;align-items:center;gap:8px;padding:13px 26px;background:#ffd54f;color:#1a1a2e;border-radius:4px;font-size:15px;font-weight:800;text-decoration:none;transition:.2s;" onmouseover="this.style.background='#ffca28'" onmouseout="this.style.background='#ffd54f'">
          <i class="fas fa-store"></i> Shop Now
        </a>
        <a href="{{ route('products.index',['sort'=>'newest']) }}" style="display:inline-flex;align-items:center;gap:8px;padding:13px 26px;background:rgba(255,255,255,.12);color:#fff;border-radius:4px;font-size:15px;font-weight:600;text-decoration:none;border:1.5px solid rgba(255,255,255,.25);transition:.2s;" onmouseover="this.style.background='rgba(255,255,255,.2)'" onmouseout="this.style.background='rgba(255,255,255,.12)'">
          New Arrivals →
        </a>
      </div>
      <div style="display:flex;gap:24px;margin-top:28px;flex-wrap:wrap;">
        @foreach(['Free Shipping on ₹5000+','Easy 7-Day Returns','100% Authentic'] as $p)
        <div style="display:flex;align-items:center;gap:6px;color:rgba(255,255,255,.7);font-size:12.5px;">
          <i class="fas fa-check-circle" style="color:#69f0ae;"></i> {{ $p }}
        </div>
        @endforeach
      </div>
    </div>
    {{-- Category quick-nav --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;flex-shrink:0;">
      @foreach($categories->take(6) as $cat)
      <a href="{{ route('products.index',['category'=>$cat->id]) }}"
         style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:14px 10px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:8px;text-decoration:none;transition:.2s;min-width:90px;"
         onmouseover="this.style.background='rgba(255,255,255,.2)';this.style.transform='translateY(-2px)'"
         onmouseout="this.style.background='rgba(255,255,255,.1)';this.style.transform=''">
        <div style="font-size:22px;margin-bottom:5px;">
          {{ ['🏺','👘','🌿','💎','🧵','🎨'][$loop->index % 6] }}
        </div>
        <div style="color:#fff;font-size:11px;font-weight:600;text-align:center;line-height:1.2;">{{ $cat->name }}</div>
        <div style="color:rgba(255,255,255,.5);font-size:10px;margin-top:2px;">{{ $cat->products_count }} items</div>
      </a>
      @endforeach
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════
     PERKS BAR
═══════════════════════════════════════════════ --}}
<div style="background:#fff;border-bottom:1px solid var(--bdr);padding:12px 0;margin-bottom:20px;">
  <div style="max-width:1300px;margin:0 auto;padding:0 20px;display:flex;justify-content:space-around;flex-wrap:wrap;gap:12px;">
    @foreach([
      ['fas fa-truck','#2e7d32','Free Delivery','On orders over BTN 5,000'],
      ['fas fa-shield-alt','#1565c0','Secure Payment','SSL encrypted checkout'],
      ['fas fa-undo','#e65100','Easy Returns','7-day hassle-free returns'],
      ['fas fa-headset','#6a1b9a','24/7 Support','Always here to help'],
    ] as [$icon,$color,$title,$sub])
    <div style="display:flex;align-items:center;gap:10px;">
      <div style="width:36px;height:36px;background:{{ $color }}15;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="{{ $icon }}" style="color:{{ $color }};font-size:15px;"></i>
      </div>
      <div>
        <div style="font-size:13px;font-weight:700;color:var(--txt);">{{ $title }}</div>
        <div style="font-size:11.5px;color:var(--mut);">{{ $sub }}</div>
      </div>
    </div>
    @endforeach
  </div>
</div>

<div style="max-width:1300px;margin:0 auto;padding:0 16px 40px;">

{{-- ═══════════════════════════════════════════════
     FEATURED PRODUCTS
═══════════════════════════════════════════════ --}}
@if($featured->count())
<div style="margin-bottom:32px;">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
    <div>
      <h2 style="font-size:20px;font-weight:800;color:var(--txt);">⭐ Featured Products</h2>
      <div style="font-size:13px;color:var(--mut);margin-top:2px;">Hand-picked selections from our catalogue</div>
    </div>
    <a href="{{ route('products.index') }}" style="font-size:13px;color:var(--pr);font-weight:600;text-decoration:none;display:flex;align-items:center;gap:5px;">View All <i class="fas fa-arrow-right"></i></a>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0;background:var(--card);border:1px solid var(--bdr);border-radius:var(--r2);overflow:hidden;">
    @foreach($featured as $p)
      @include('frontend._product_card', ['p' => $p, 'context' => 'featured'])
    @endforeach
  </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════
     CATEGORY ROWS (each category as a row)
═══════════════════════════════════════════════ --}}
@foreach($categories->take(4) as $cat)
@php
  $catProducts = \App\Models\Product::with(['category','variants'])
    ->where('status','active')
    ->where('category_id', $cat->id)
    ->orderByRaw('is_featured DESC, created_at DESC')
    ->take(6)
    ->get();
@endphp
@if($catProducts->count() >= 3)
<div style="margin-bottom:28px;">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
    <div>
      <h2 style="font-size:18px;font-weight:800;color:var(--txt);">{{ $cat->name }}</h2>
      <div style="font-size:12.5px;color:var(--mut);margin-top:1px;">{{ $cat->products_count }} products available</div>
    </div>
    <a href="{{ route('products.index',['category'=>$cat->id]) }}" style="font-size:13px;color:var(--pr);font-weight:600;text-decoration:none;white-space:nowrap;">See All in {{ $cat->name }} →</a>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0;background:var(--card);border:1px solid var(--bdr);border-radius:var(--r2);overflow:hidden;">
    @foreach($catProducts as $p)
      @include('frontend._product_card', ['p' => $p, 'context' => 'category'])
    @endforeach
  </div>
</div>
@endif
@endforeach

{{-- ═══════════════════════════════════════════════
     LIMITED STOCK URGENCY STRIP
═══════════════════════════════════════════════ --}}
@if($deals->count())
<div style="margin-bottom:32px;">
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;padding:14px 18px;background:linear-gradient(135deg,#e65100,#c0392b);border-radius:var(--r2);">
    <div style="font-size:22px;">🔥</div>
    <div>
      <h2 style="font-size:17px;font-weight:800;color:#fff;margin-bottom:2px;">Limited Stock — Grab Them Fast!</h2>
      <div style="font-size:12px;color:rgba(255,255,255,.8);">These items are running low — order before they're gone</div>
    </div>
    <a href="{{ route('products.index') }}" style="margin-left:auto;padding:8px 16px;background:rgba(255,255,255,.15);color:#fff;border-radius:4px;font-size:13px;font-weight:600;text-decoration:none;border:1px solid rgba(255,255,255,.3);">Shop All</a>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0;background:var(--card);border:1px solid var(--bdr);border-radius:var(--r2);overflow:hidden;">
    @foreach($deals as $p)
      @include('frontend._product_card', ['p' => $p, 'context' => 'deals'])
    @endforeach
  </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════
     NEW ARRIVALS
═══════════════════════════════════════════════ --}}
@if($newArrivals->count())
<div style="margin-bottom:32px;">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
    <div>
      <h2 style="font-size:18px;font-weight:800;color:var(--txt);">🆕 New Arrivals</h2>
      <div style="font-size:12.5px;color:var(--mut);">Just added to our catalogue</div>
    </div>
    <a href="{{ route('products.index',['sort'=>'newest']) }}" style="font-size:13px;color:var(--pr);font-weight:600;text-decoration:none;">View All →</a>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0;background:var(--card);border:1px solid var(--bdr);border-radius:var(--r2);overflow:hidden;">
    @foreach($newArrivals as $p)
      @include('frontend._product_card', ['p' => $p, 'context' => 'new'])
    @endforeach
  </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════
     BEST SELLERS
═══════════════════════════════════════════════ --}}
@if($bestSellers->count())
<div style="margin-bottom:32px;">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
    <div>
      <h2 style="font-size:18px;font-weight:800;color:var(--txt);">🏆 Best Sellers</h2>
      <div style="font-size:12.5px;color:var(--mut);">Most ordered by our customers</div>
    </div>
    <a href="{{ route('products.index') }}" style="font-size:13px;color:var(--pr);font-weight:600;text-decoration:none;">View All →</a>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0;background:var(--card);border:1px solid var(--bdr);border-radius:var(--r2);overflow:hidden;">
    @foreach($bestSellers as $p)
      @include('frontend._product_card', ['p' => $p, 'context' => 'bestseller'])
    @endforeach
  </div>
</div>
@endif

</div>
@endsection
