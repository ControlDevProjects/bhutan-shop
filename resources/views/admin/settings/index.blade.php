@extends('admin.layouts.app')
@section('title','Store Settings')
@section('page-title','Store Settings')
@section('content')

@if(session('success'))
<div class="alert alert-ok mb16"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}">
@csrf @method('PUT')

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

{{-- ── Company Details ── --}}
<div class="card">
<div class="card-hd"><h2><i class="fas fa-building" style="color:var(--pr);"></i> Company Details</h2></div>
<div class="card-bd">
    <div class="fg">
        <label class="lbl">Company Name <span class="req">*</span></label>
        <input type="text" name="company_name" class="fc" required value="{{ $s['company_name'] ?? '' }}" placeholder="Bhutan Shop">
    </div>
    <div class="fg">
        <label class="lbl">Tagline / Slogan</label>
        <input type="text" name="company_tagline" class="fc" value="{{ $s['company_tagline'] ?? '' }}" placeholder="Authentic Bhutanese Products">
    </div>
    <div class="fg">
        <label class="lbl">Email</label>
        <input type="email" name="company_email" class="fc" value="{{ $s['company_email'] ?? '' }}" placeholder="info@bhutanshop.bt">
    </div>
    <div class="fg">
        <label class="lbl">Phone</label>
        <input type="text" name="company_phone" class="fc" value="{{ $s['company_phone'] ?? '' }}" placeholder="+975 2 123456">
    </div>
    <div class="fg">
        <label class="lbl">Address</label>
        <textarea name="company_address" class="fc" rows="2" placeholder="Thimphu, Bhutan">{{ $s['company_address'] ?? '' }}</textarea>
    </div>
    <div class="fg">
        <label class="lbl">Invoice Footer Note</label>
        <textarea name="invoice_footer" class="fc" rows="2" placeholder="Thank you for shopping with us!">{{ $s['invoice_footer'] ?? '' }}</textarea>
    </div>
</div>
</div>

{{-- ── GST / Tax ── --}}
<div>
<div class="card mb16">
<div class="card-hd"><h2><i class="fas fa-percent" style="color:var(--pr);"></i> Tax / GST Settings</h2></div>
<div class="card-bd">
    <div class="fg">
        <label class="lbl">GSTIN / Tax Registration Number</label>
        <input type="text" name="company_gstin" class="fc" value="{{ $s['company_gstin'] ?? '' }}" placeholder="Leave blank if not applicable">
        <div class="hint">Shown on invoices and checkout</div>
    </div>
    <div class="g2">
        <div class="fg">
            <label class="lbl">Tax Label <span class="req">*</span></label>
            <input type="text" name="gst_label" class="fc" required value="{{ $s['gst_label'] ?? 'GST' }}" placeholder="GST / VAT / Tax">
        </div>
        <div class="fg">
            <label class="lbl">Tax % <span class="req">*</span></label>
            <input type="number" name="gst_percentage" class="fc" required step="0.01" min="0" max="100" value="{{ $s['gst_percentage'] ?? '0' }}" placeholder="0">
        </div>
    </div>
    <div class="fg">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13.5px;">
            <input type="checkbox" name="gst_inclusive" value="1" {{ ($s['gst_inclusive'] ?? '1') === '1' ? 'checked' : '' }}>
            <span>Prices are <strong>tax-inclusive</strong> (tax is already included in displayed price)</span>
        </label>
        <div class="hint">If unchecked, tax is added on top of the product price at checkout</div>
    </div>
    <div class="fg">
        <label class="lbl">Currency Symbol <span class="req">*</span></label>
        <input type="text" name="currency_symbol" class="fc" required value="{{ $s['currency_symbol'] ?? 'BTN' }}" placeholder="BTN" style="max-width:100px;">
    </div>
</div>
</div>

{{-- ── Shipping Defaults ── --}}
<div class="card">
<div class="card-hd"><h2><i class="fas fa-truck" style="color:var(--pr);"></i> Default Shipping Rates</h2></div>
<div class="card-bd">
    <p style="font-size:12.5px;color:var(--mut);margin-bottom:14px;">
        These are the <strong>global defaults</strong>. Individual products can override these in their own settings.
    </p>
    <div class="fg">
        <label class="lbl">Standard Shipping Cost ({{ $s['currency_symbol'] ?? 'BTN' }}) <span class="req">*</span></label>
        <input type="number" name="shipping_default_cost" class="fc" required step="0.01" min="0" value="{{ $s['shipping_default_cost'] ?? '150' }}">
    </div>
    <div class="fg">
        <label class="lbl">Free Shipping Above ({{ $s['currency_symbol'] ?? 'BTN' }}) <span class="req">*</span></label>
        <input type="number" name="shipping_free_above" class="fc" required step="0.01" min="0" value="{{ $s['shipping_free_above'] ?? '5000' }}">
        <div class="hint">Set to 0 to disable free shipping threshold</div>
    </div>
    <div class="fg">
        <label class="lbl">Express Shipping Cost ({{ $s['currency_symbol'] ?? 'BTN' }}) <span class="req">*</span></label>
        <input type="number" name="shipping_express_cost" class="fc" required step="0.01" min="0" value="{{ $s['shipping_express_cost'] ?? '300' }}">
    </div>
    <div style="margin-top:4px;padding:10px 12px;background:#fff8e1;border:1px solid #ffe0b2;border-radius:var(--r);font-size:12.5px;color:#e65100;">
        <i class="fas fa-info-circle"></i>
        Product-level shipping type overrides these defaults. Go to any product → edit → Shipping Type to set per-product rules.
    </div>
</div>
</div>
</div>

</div>

<div style="margin-top:16px;display:flex;justify-content:flex-end;">
    <button type="submit" class="btn btn-pr" style="padding:11px 28px;font-size:14px;">
        <i class="fas fa-save"></i> Save Settings
    </button>
</div>
</form>

@endsection
