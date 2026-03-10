@extends('frontend.layouts.app')
@section('title', request('search') ? 'Search: "'.request('search').'"' : (request('category') ? ($categories->firstWhere('id',request('category'))?->name ?? 'Shop') : 'Shop All Products'))
@section('content')
<div class="page-wrap">
<div class="breadcrumb">
  <a href="{{ route('home') }}">Home</a><span class="sep">/</span>
  @if(request('category') && $cat = $categories->firstWhere('id',request('category')))
    <span>{{ $cat->name }}</span>
  @elseif(request('search'))
    <span>Search: "{{ request('search') }}"</span>
  @else
    <span>All Products</span>
  @endif
</div>

<div style="display:grid;grid-template-columns:220px 1fr;gap:14px;align-items:start;">

{{-- ═══ SIDEBAR ═══ --}}
<aside>
  <div class="sidebar-card mb16">
    <div class="sb-hd"><h3>🔍 Filters</h3></div>
    <form method="GET" id="filterForm">
      @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
      @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

      {{-- Category --}}
      <div class="sb-body" style="border-bottom:1px solid var(--bdr2);">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--txt2);margin-bottom:8px;">Category</div>
        <div class="f-opt"><input type="radio" name="category" value="" id="cat-all" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()"><label for="cat-all">All Categories</label></div>
        @foreach($categories as $cat)
        <div class="f-opt"><input type="radio" name="category" value="{{ $cat->id }}" id="cat-{{ $cat->id }}" {{ request('category')==$cat->id ? 'checked' : '' }} onchange="this.form.submit()"><label for="cat-{{ $cat->id }}">{{ $cat->name }}</label></div>
        @endforeach
      </div>

      {{-- Availability --}}
      <div class="sb-body" style="border-bottom:1px solid var(--bdr2);">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--txt2);margin-bottom:8px;">Availability</div>
        <div class="f-opt"><input type="radio" name="availability" value="" id="av-all" {{ !request('availability') ? 'checked' : '' }} onchange="this.form.submit()"><label for="av-all">All</label></div>
        <div class="f-opt"><input type="radio" name="availability" value="in_stock" id="av-stock" {{ request('availability')==='in_stock' ? 'checked' : '' }} onchange="this.form.submit()"><label for="av-stock">In Stock Only</label></div>
      </div>

      {{-- Type --}}
      <div class="sb-body">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--txt2);margin-bottom:8px;">Product Type</div>
        <div class="f-opt"><input type="radio" name="type" value="" id="t-all" {{ !request('type') ? 'checked' : '' }} onchange="this.form.submit()"><label for="t-all">All Types</label></div>
        <div class="f-opt"><input type="radio" name="type" value="simple" id="t-simple" {{ request('type')==='simple' ? 'checked' : '' }} onchange="this.form.submit()"><label for="t-simple">Simple Products</label></div>
        <div class="f-opt"><input type="radio" name="type" value="variant" id="t-variant" {{ request('type')==='variant' ? 'checked' : '' }} onchange="this.form.submit()"><label for="t-variant">Variant Products</label></div>
      </div>
    </form>
  </div>
  @if(request()->hasAny(['search','category','type','availability','sort']))
  <a href="{{ route('products.index') }}" class="btn btn-sc btn-sm btn-full"><i class="fas fa-times"></i> Clear Filters</a>
  @endif
</aside>

{{-- ═══ PRODUCTS AREA ═══ --}}
<div>
  {{-- Toolbar --}}
  <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--card);border:1px solid var(--bdr);border-radius:var(--r2) var(--r2) 0 0;border-bottom:none;font-size:13px;flex-wrap:wrap;gap:8px;">
    <div style="color:var(--txt2);">
      @if(request('search'))Results for <strong>"{{ request('search') }}"</strong> — @endif
      <strong>{{ $products->total() }}</strong> products found
    </div>
    <div style="display:flex;align-items:center;gap:8px;">
      <span style="font-size:12px;color:var(--mut);">Sort:</span>
      <select class="sort-sel" onchange="window.location.href=this.value">
        @php $base = request()->except('sort'); @endphp
        <option value="{{ route('products.index',array_merge($base,['sort'=>''])) }}" {{ !request('sort') ? 'selected' : '' }}>Relevance</option>
        <option value="{{ route('products.index',array_merge($base,['sort'=>'newest'])) }}" {{ request('sort')==='newest' ? 'selected' : '' }}>Newest First</option>
        <option value="{{ route('products.index',array_merge($base,['sort'=>'price_asc'])) }}" {{ request('sort')==='price_asc' ? 'selected' : '' }}>Price: Low → High</option>
        <option value="{{ route('products.index',array_merge($base,['sort'=>'price_desc'])) }}" {{ request('sort')==='price_desc' ? 'selected' : '' }}>Price: High → Low</option>
        <option value="{{ route('products.index',array_merge($base,['sort'=>'name_asc'])) }}" {{ request('sort')==='name_asc' ? 'selected' : '' }}>Name: A → Z</option>
      </select>
    </div>
  </div>

  @if($products->count())
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:0;background:var(--card);border:1px solid var(--bdr);border-radius:0 0 var(--r2) var(--r2);overflow:hidden;">
    @foreach($products as $p)
      @include('frontend._product_card', ['p' => $p, 'context' => 'default'])
    @endforeach
  </div>

  @if($products->hasPages())
  <div style="background:var(--card);border:1px solid var(--bdr);border-top:none;border-radius:0 0 var(--r2) var(--r2);padding:14px;display:flex;justify-content:center;">
    {{ $products->links() }}
  </div>
  @endif

  @else
  <div style="background:var(--card);border:1px solid var(--bdr);border-top:none;border-radius:0 0 var(--r2) var(--r2);padding:60px 20px;text-align:center;">
    <div style="font-size:56px;margin-bottom:14px;">🔍</div>
    <h2 style="font-size:18px;font-weight:700;margin-bottom:8px;">No products found</h2>
    <p style="color:var(--mut);font-size:14px;">Try adjusting your search or clearing filters.</p>
    <a href="{{ route('products.index') }}" class="btn btn-pr" style="margin-top:16px;">Browse All</a>
  </div>
  @endif
</div>
</div>
</div>
@endsection
