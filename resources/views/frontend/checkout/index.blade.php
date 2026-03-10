@extends('frontend.layouts.app')
@section('title','Checkout')
@section('content')
<div class="page-wrap">
<div class="breadcrumb"><a href="{{ route('home') }}">Home</a><span class="sep">/</span><a href="{{ route('cart.index') }}">Cart</a><span class="sep">/</span><span>Checkout</span></div>

{{-- Steps --}}
<div style="display:flex;align-items:center;gap:0;margin-bottom:20px;background:var(--card);border:1px solid var(--bdr);border-radius:var(--r2);padding:14px 20px;">
  @foreach(['Cart','Address','Payment','Confirmation'] as $i => $step)
  <div style="display:flex;align-items:center;gap:0;flex:1;">
    <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;font-weight:{{ $i<=1?'700':'500' }};color:{{ $i<=1?'var(--pr)':'var(--mut)' }};">
      <div style="width:24px;height:24px;border-radius:50%;background:{{ $i<=1?'var(--pr)':'#e0e0e0' }};color:{{ $i<=1?'#fff':'var(--mut)' }};display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;">
        {{ $i<1?'✓':($i+1) }}
      </div>
      {{ $step }}
    </div>
    @if(!$loop->last)<div style="flex:1;height:2px;background:{{ $i<1?'var(--pr)':'#e0e0e0' }};margin:0 8px;"></div>@endif
  </div>
  @endforeach
</div>

<form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm">
@csrf
<div style="display:grid;grid-template-columns:1fr 360px;gap:16px;align-items:start;">

{{-- Left --}}
<div>
<div class="card mb16">
  <div class="card-hd" style="background:#fafafa;"><h2><i class="fas fa-map-marker-alt" style="color:var(--pr);"></i> Delivery Address</h2></div>
  <div class="card-bd">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
      <div class="fg"><label class="lbl">Full Name <span class="req">*</span></label><input name="shipping_name" class="fc" value="{{ old('shipping_name',$user->name) }}" required>@error('shipping_name')<div style="color:var(--err);font-size:12px;margin-top:3px;">{{ $message }}</div>@enderror</div>
      <div class="fg"><label class="lbl">Phone Number <span class="req">*</span></label><input name="shipping_phone" class="fc" value="{{ old('shipping_phone',$user->phone) }}" required placeholder="+975 XXXXXXXX">@error('shipping_phone')<div style="color:var(--err);font-size:12px;margin-top:3px;">{{ $message }}</div>@enderror</div>
    </div>
    <div class="fg"><label class="lbl">Street Address <span class="req">*</span></label><textarea name="shipping_address" class="fc" rows="2" required placeholder="House/Building number, Street, Area...">{{ old('shipping_address',$user->address) }}</textarea>@error('shipping_address')<div style="color:var(--err);font-size:12px;margin-top:3px;">{{ $message }}</div>@enderror</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
      <div class="fg"><label class="lbl">City/Town <span class="req">*</span></label><input name="shipping_city" class="fc" value="{{ old('shipping_city',$user->city) }}" required>@error('shipping_city')<div style="color:var(--err);font-size:12px;margin-top:3px;">{{ $message }}</div>@enderror</div>
      <div class="fg"><label class="lbl">Dzongkhag <span class="req">*</span></label>
        <select name="shipping_dzongkhag" class="fc" required>
          <option value="">— Select Dzongkhag —</option>
          @foreach(['Bumthang','Chhukha','Dagana','Gasa','Haa','Lhuntse','Monggar','Paro','Pemagatshel','Punakha','Samdrup Jongkhar','Samtse','Sarpang','Thimphu','Trashigang','Trashiyangtse','Trongsa','Tsirang','Wangdue Phodrang','Zhemgang'] as $dz)
          <option value="{{ $dz }}" {{ old('shipping_dzongkhag',$user->dzongkhag)===$dz?'selected':'' }}>{{ $dz }}</option>
          @endforeach
        </select>
        @error('shipping_dzongkhag')<div style="color:var(--err);font-size:12px;margin-top:3px;">{{ $message }}</div>@enderror
      </div>
    </div>
    <div class="fg" style="margin-bottom:0;"><label class="lbl">Order Notes (optional)</label><textarea name="notes" class="fc" rows="2" placeholder="Special instructions for delivery...">{{ old('notes') }}</textarea></div>
  </div>
</div>

<div class="card">
  <div class="card-hd" style="background:#fafafa;"><h2><i class="fas fa-credit-card" style="color:var(--pr);"></i> Payment Method</h2></div>
  <div class="card-bd" style="display:flex;flex-direction:column;gap:10px;">
    <label id="lbl-cod" style="display:flex;align-items:flex-start;gap:14px;padding:14px 16px;border:2px solid var(--pr);border-radius:var(--r);cursor:pointer;background:var(--pr-lt);transition:.2s;">
      <input type="radio" name="payment_method" value="cod" checked style="margin-top:2px;accent-color:var(--pr);">
      <div>
        <div style="font-weight:700;font-size:14px;display:flex;align-items:center;gap:8px;"><i class="fas fa-money-bill-wave" style="color:var(--ok);"></i> Cash on Delivery</div>
        <div style="font-size:12.5px;color:var(--mut);margin-top:3px;">Pay when your order arrives. Order is marked as Paid after delivery confirmation.</div>
      </div>
    </label>
    <label id="lbl-rzp" style="display:flex;align-items:flex-start;gap:14px;padding:14px 16px;border:2px solid var(--bdr);border-radius:var(--r);cursor:pointer;transition:.2s;">
      <input type="radio" name="payment_method" value="razorpay" style="margin-top:2px;accent-color:var(--pr);">
      <div>
        <div style="font-weight:700;font-size:14px;display:flex;align-items:center;gap:8px;"><i class="fas fa-bolt" style="color:#528ff0;"></i> Online Payment (Razorpay)</div>
        <div style="font-size:12.5px;color:var(--mut);margin-top:3px;">Pay securely with Cards, UPI, or Net Banking. Instant payment confirmation.</div>
      </div>
    </label>
  </div>
</div>
</div>

{{-- Right: Summary --}}
<div>
<div class="card">
  <div class="card-hd" style="background:#fafafa;"><h2>Order Summary ({{ count($items) }} items)</h2></div>
  <div style="max-height:280px;overflow-y:auto;">
  @foreach($items as $item)
  <div style="display:flex;gap:10px;padding:12px 16px;border-bottom:1px solid var(--bdr2);align-items:center;">
    <div style="width:50px;height:50px;border-radius:var(--r);overflow:hidden;border:1px solid var(--bdr);flex-shrink:0;background:#f5f5f5;">
      @if($item['image'])<img src="{{ asset('storage/'.$item['image']) }}" style="width:100%;height:100%;object-fit:cover;">
      @else<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#ddd;"><i class="fas fa-image"></i></div>@endif
    </div>
    <div style="flex:1;">
      <div style="font-size:13px;font-weight:600;line-height:1.3;">{{ $item['name'] }}</div>
      @if($item['variant_name'])<div style="font-size:11px;color:var(--mut);">{{ $item['variant_name'] }}</div>@endif
      <div style="font-size:12px;color:var(--mut);">Qty: {{ $item['qty'] }}</div>
    </div>
    <div style="font-size:13px;font-weight:700;color:var(--pr);">BTN {{ number_format($item['price']*$item['qty'],2) }}</div>
  </div>
  @endforeach
  </div>
  <div class="card-bd">
    <div style="display:flex;justify-content:space-between;font-size:13.5px;margin-bottom:8px;"><span>Subtotal</span><span>BTN {{ number_format($subtotal,2) }}</span></div>
    <div style="display:flex;justify-content:space-between;font-size:13.5px;margin-bottom:14px;"><span>Delivery</span><span style="{{ $shipping==0?'color:var(--ok);font-weight:700;':'' }}">{{ $shipping==0?'FREE':'BTN '.number_format($shipping,2) }}</span></div>
    <div style="border-top:2px dashed var(--bdr);padding-top:14px;display:flex;justify-content:space-between;font-size:18px;font-weight:800;"><span>Total</span><span style="color:var(--pr);">BTN {{ number_format($subtotal+$shipping,2) }}</span></div>
    <button type="submit" class="btn btn-pr btn-full" style="margin-top:16px;padding:13px;font-size:15px;">
      <i class="fas fa-check-circle"></i> Place Order
    </button>
    <div style="text-align:center;margin-top:10px;font-size:12px;color:var(--mut);"><i class="fas fa-shield-alt"></i> Secure & Encrypted Checkout</div>
  </div>
</div>
</div>
</div>
</form>
</div>
@endsection
@push('scripts')
<script>
document.querySelectorAll('input[name="payment_method"]').forEach(r => {
  r.addEventListener('change', () => {
    document.getElementById('lbl-cod').style.borderColor = 'var(--bdr)';
    document.getElementById('lbl-cod').style.background = '';
    document.getElementById('lbl-rzp').style.borderColor = 'var(--bdr)';
    document.getElementById('lbl-rzp').style.background = '';
    const lbl = document.getElementById('lbl-'+r.value);
    if (lbl) { lbl.style.borderColor = 'var(--pr)'; lbl.style.background = 'var(--pr-lt)'; }
  });
});
</script>
@endpush
