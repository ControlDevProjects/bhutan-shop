@extends('frontend.layouts.app')
@section('title','My Wishlist')
@section('content')
<div class="page-wrap">
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
  <h1 style="font-size:20px;font-weight:800;">❤️ My Wishlist ({{ $items->count() }})</h1>
  @if($items->count())
  <span style="font-size:13px;color:var(--mut);">Items saved for later</span>
  @endif
</div>

@if($items->isEmpty())
<div class="empty-state">
  <div class="icon">💔</div>
  <h2>Your wishlist is empty</h2>
  <p>Save products you love and come back to them anytime.</p>
  <a href="{{ route('products.index') }}" class="btn btn-pr" style="margin-top:18px;"><i class="fas fa-store"></i> Explore Products</a>
</div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0;background:var(--card);border:1px solid var(--bdr);border-radius:var(--r2);overflow:hidden;">
  @foreach($items as $item)
  @php $p = $item->product; @endphp
  @if($p)
  <div class="prod-card" style="position:relative;border-right:1px solid var(--bdr2);border-bottom:1px solid var(--bdr2);">
    <div class="prod-img-wrap">
      <a href="{{ route('products.show',$p->slug) }}" style="display:block;height:100%;">
        @if($p->primary_image)
          <img src="{{ asset('storage/'.$p->primary_image) }}" alt="{{ $p->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;transition:transform .35s;">
        @else
          <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#ddd;font-size:48px;background:#fafafa;"><i class="fas fa-image"></i></div>
        @endif
      </a>
      <button onclick="removeFromWishlist({{ $p->id }}, this)"
              style="position:absolute;top:8px;right:8px;width:30px;height:30px;background:#fff;border-radius:50%;border:1px solid #ffcdd2;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:13px;color:var(--err);"
              title="Remove from wishlist">
        <i class="fas fa-heart"></i>
      </button>
    </div>
    <div class="card-body">
      @if($p->category)<div style="font-size:10.5px;color:var(--mut);font-weight:600;text-transform:uppercase;margin-bottom:3px;">{{ $p->category->name }}</div>@endif
      <div style="font-size:13px;font-weight:600;margin-bottom:6px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
        <a href="{{ route('products.show',$p->slug) }}" style="color:inherit;text-decoration:none;">{{ $p->name }}</a>
      </div>
      <div style="font-size:16px;font-weight:800;color:var(--txt);margin-bottom:5px;">{{ $p->price_display }}</div>
      @if($p->in_stock)
        <div style="font-size:11.5px;color:var(--ok);font-weight:600;margin-bottom:8px;"><i class="fas fa-check-circle"></i> In Stock</div>
        @if($p->type === 'simple')
          <button onclick="directAddToCart({{ $p->id }}, null, this)"
                  style="width:100%;padding:8px;background:var(--pr-lt);color:var(--pr);border:1.5px solid #ffcdd2;border-radius:3px;font-size:12.5px;font-weight:700;cursor:pointer;font-family:var(--font);transition:.15s;"
                  onmouseover="this.style.background='var(--pr)';this.style.color='#fff'"
                  onmouseout="this.style.background='var(--pr-lt)';this.style.color='var(--pr)'">
            <i class="fas fa-shopping-cart"></i> Add to Cart
          </button>
        @else
          <button onclick="openQAP({{ $p->id }})"
                  style="width:100%;padding:8px;background:#fff8e1;color:#e65100;border:1.5px solid #ffcc80;border-radius:3px;font-size:12.5px;font-weight:700;cursor:pointer;font-family:var(--font);transition:.15s;"
                  onmouseover="this.style.background='var(--sec)';this.style.color='#fff'"
                  onmouseout="this.style.background='#fff8e1';this.style.color='#e65100'">
            <i class="fas fa-layer-group"></i> Select Options
          </button>
        @endif
      @else
        <div style="font-size:11.5px;color:var(--err);font-weight:600;margin-bottom:8px;"><i class="fas fa-times-circle"></i> Out of Stock</div>
        <div style="width:100%;padding:7px;background:#f5f5f5;color:var(--mut);border:1.5px solid var(--bdr);border-radius:3px;font-size:12px;font-weight:600;text-align:center;">
          Unavailable
        </div>
      @endif
    </div>
  </div>
  @endif
  @endforeach
</div>
@endif
</div>
@endsection
@push('scripts')
<script>
async function removeFromWishlist(productId, btn) {
  const card = btn.closest('.prod-card');
  try {
    await fetch(`/wishlist/${productId}`, {
      method:'DELETE',
      headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'}
    });
    card.style.opacity='0';
    card.style.transition='opacity .3s';
    setTimeout(()=>card.remove(), 300);
    const wlBadge = document.getElementById('wlBadge');
    if (wlBadge) {
      const c = parseInt(wlBadge.textContent||'0') - 1;
      wlBadge.textContent = c;
      wlBadge.style.display = c > 0 ? '' : 'none';
    }
    window['showToast']?.('Removed from wishlist', 0);
  } catch(e) {}
}
</script>
@endpush
