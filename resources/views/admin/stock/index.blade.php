@extends('admin.layouts.app')
@section('title','Stock Management')
@section('page-title','Stock Management')
@section('topbar-actions')
    <a href="{{ route('admin.products.create') }}" class="btn btn-pr btn-sm"><i class="fas fa-plus"></i> Add Product</a>
@endsection
@section('content')

@if(session('success'))<div class="alert alert-ok mb16"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif

{{-- Summary cards --}}
<div class="g4" style="margin-bottom:18px;">
    <div class="stat-card" style="border-left:4px solid var(--info);">
        <div class="stat-icon" style="background:#e3f2fd;color:var(--info);"><i class="fas fa-boxes"></i></div>
        <div><div class="stat-val">{{ $totalProducts }}</div><div class="stat-lbl">Total Products</div></div>
    </div>
    <div class="stat-card" style="border-left:4px solid var(--err);">
        <div class="stat-icon" style="background:#ffebee;color:var(--err);"><i class="fas fa-times-circle"></i></div>
        <div><div class="stat-val">{{ $outOfStock }}</div><div class="stat-lbl">Out of Stock</div></div>
    </div>
    <div class="stat-card" style="border-left:4px solid var(--warn);">
        <div class="stat-icon" style="background:#fff8e1;color:var(--warn);"><i class="fas fa-exclamation-triangle"></i></div>
        <div><div class="stat-val">{{ $lowStock }}</div><div class="stat-lbl">Low Stock (&lt; 5)</div></div>
    </div>
    <div class="stat-card" style="border-left:4px solid var(--ok);">
        <div class="stat-icon" style="background:#e8f5e9;color:var(--ok);"><i class="fas fa-infinity"></i></div>
        <div><div class="stat-val">{{ $unlimited }}</div><div class="stat-lbl">Unlimited Stock</div></div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb16">
<div class="card-bd" style="padding:12px 18px;">
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:flex-end;">
    <div><label class="lbl">Search</label><input name="search" class="fc" value="{{ request('search') }}" placeholder="Product name..." style="width:200px;"></div>
    <div><label class="lbl">Category</label>
        <select name="category" class="fc" style="width:150px;">
            <option value="">All Categories</option>
            @foreach($categories as $c)
            <option value="{{ $c->id }}" {{ request('category')==$c->id?'selected':'' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div><label class="lbl">Stock Filter</label>
        <select name="filter" class="fc" style="width:150px;">
            <option value="">All</option>
            <option value="out" {{ request('filter')==='out'?'selected':'' }}>Out of Stock</option>
            <option value="low" {{ request('filter')==='low'?'selected':'' }}>Low Stock</option>
            <option value="unlimited" {{ request('filter')==='unlimited'?'selected':'' }}>Unlimited</option>
        </select>
    </div>
    <button type="submit" class="btn btn-sc"><i class="fas fa-search"></i> Filter</button>
    <a href="{{ route('admin.stock.index') }}" class="btn btn-sc"><i class="fas fa-times"></i></a>
</form>
</div>
</div>

{{-- Products stock table --}}
<div class="card">
<div class="tw">
<table>
<thead>
    <tr>
        <th width="50">Image</th>
        <th>Product</th>
        <th>Category</th>
        <th>Type</th>
        <th>Stock Status</th>
        <th width="200">Quick Adjust</th>
        <th width="100">Actions</th>
    </tr>
</thead>
<tbody>
@forelse($products as $p)
<tr id="prow_{{ $p->id }}">
    <td>
        @if($p->primary_image)
        <img src="{{ asset('storage/'.$p->primary_image) }}" style="width:44px;height:44px;object-fit:cover;border-radius:6px;border:1px solid var(--bdr);">
        @else
        <div style="width:44px;height:44px;background:#f0f0f0;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:18px;"><i class="fas fa-image"></i></div>
        @endif
    </td>
    <td>
        <div style="font-weight:600;font-size:13.5px;">{{ $p->name }}</div>
        <div style="font-size:11px;color:var(--mut);">{{ $p->slug }}</div>
        @if($p->is_featured)<span class="badge b-warn" style="font-size:10px;">⭐ Featured</span>@endif
    </td>
    <td style="font-size:12.5px;">{{ $p->category?->name ?? '—' }}</td>
    <td><span class="badge {{ $p->type==='simple'?'b-info':'b-sec' }}">{{ ucfirst($p->type) }}</span></td>
    <td>
        @if($p->type === 'simple')
            @if($p->stock_type === 'unlimited')
                <span style="color:var(--info);font-weight:600;font-size:13px;"><i class="fas fa-infinity"></i> Unlimited</span>
            @elseif($p->stock == 0)
                <span style="color:var(--err);font-weight:700;font-size:13px;" id="stock_{{ $p->id }}"><i class="fas fa-times-circle"></i> Out of Stock</span>
            @elseif($p->stock < 5)
                <span style="color:var(--warn);font-weight:700;font-size:13px;" id="stock_{{ $p->id }}"><i class="fas fa-exclamation-triangle"></i> <span class="sval">{{ $p->stock }}</span> units — Low</span>
            @else
                <span style="color:var(--ok);font-size:13px;" id="stock_{{ $p->id }}"><i class="fas fa-check-circle"></i> <span class="sval">{{ $p->stock }}</span> units</span>
            @endif
        @else
            {{-- Variant summary --}}
            @php
              $inStock  = $p->variants->filter(fn($v) => $v->stock_type==='unlimited'||$v->stock>0)->count();
              $total    = $p->variants->count();
              $hasLow   = $p->variants->some(fn($v) => $v->stock_type==='limited'&&$v->stock>0&&$v->stock<5);
              $hasOut   = $p->variants->some(fn($v) => $v->stock_type==='limited'&&$v->stock==0);
            @endphp
            <div style="font-size:12.5px;">
                <span style="color:{{ $hasOut?'var(--err)':($hasLow?'var(--warn)':'var(--ok)') }};font-weight:600;">
                    {{ $inStock }}/{{ $total }} variants in stock
                </span>
                @if($hasOut)<div style="font-size:11px;color:var(--err);">⚠ Some variants out of stock</div>@endif
                @if($hasLow)<div style="font-size:11px;color:var(--warn);">⚠ Some variants low stock</div>@endif
            </div>
        @endif
    </td>
    <td>
        @if($p->type === 'simple' && $p->stock_type !== 'unlimited')
        <div style="display:flex;gap:4px;align-items:center;">
            <button onclick="openModal({{ $p->id }},null,'{{ addslashes($p->name) }}',{{ $p->stock }})"
                    class="btn btn-sc btn-sm" style="padding:5px 10px;font-size:12px;">
                <i class="fas fa-sliders-h"></i> Adjust
            </button>
        </div>
        @elseif($p->type === 'simple' && $p->stock_type === 'unlimited')
        <span style="font-size:11.5px;color:var(--mut);font-style:italic;">No adjustment needed</span>
        @else
        <a href="{{ route('admin.stock.show',$p) }}" class="btn btn-sc btn-sm" style="padding:5px 10px;font-size:12px;">
            <i class="fas fa-layer-group"></i> Manage Variants
        </a>
        @endif
    </td>
    <td>
        <div style="display:flex;flex-direction:column;gap:4px;">
            <a href="{{ route('admin.stock.show',$p) }}" class="btn btn-sc btn-sm" style="padding:4px 10px;font-size:11.5px;"><i class="fas fa-history"></i> History</a>
            <a href="{{ route('admin.products.edit',$p) }}" class="btn btn-sc btn-sm" style="padding:4px 10px;font-size:11.5px;"><i class="fas fa-edit"></i> Edit</a>
        </div>
    </td>
</tr>
@empty
<tr><td colspan="7" style="text-align:center;padding:32px;color:var(--mut);">No products found.</td></tr>
@endforelse
</tbody>
</table>
</div>
@if($products->hasPages())
<div style="padding:14px 18px;">{{ $products->links() }}</div>
@endif
</div>

{{-- ═══ ADJUST STOCK MODAL ═══ --}}
<div id="stockModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9000;display:none;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:10px;width:420px;max-width:95vw;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;">
    {{-- Modal header --}}
    <div style="background:var(--pr);color:#fff;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
      <div>
        <div style="font-size:15px;font-weight:700;" id="modalTitle">Adjust Stock</div>
        <div style="font-size:12px;opacity:.8;margin-top:2px;" id="modalSub">Current stock: <span id="modalCurrent">—</span> units</div>
      </div>
      <button onclick="closeModal()" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:30px;height:30px;border-radius:50%;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-family:inherit;">&times;</button>
    </div>
    {{-- Modal body --}}
    <div style="padding:20px;">
      {{-- Action selector --}}
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:18px;">
        <label id="lbl_add" class="action-lbl active" onclick="setAction('add')">
          <input type="radio" name="stockAction" value="add" checked style="display:none;">
          <div style="text-align:center;padding:10px 6px;border:2px solid var(--ok);border-radius:6px;cursor:pointer;background:#e8f5e9;color:var(--ok);">
            <div style="font-size:18px;margin-bottom:3px;">+</div>
            <div style="font-size:12px;font-weight:700;">Add</div>
          </div>
        </label>
        <label id="lbl_decrease" class="action-lbl" onclick="setAction('decrease')">
          <input type="radio" name="stockAction" value="decrease" style="display:none;">
          <div style="text-align:center;padding:10px 6px;border:2px solid var(--bdr);border-radius:6px;cursor:pointer;background:#fafafa;color:var(--txt2);">
            <div style="font-size:18px;margin-bottom:3px;">−</div>
            <div style="font-size:12px;font-weight:700;">Decrease</div>
          </div>
        </label>
        <label id="lbl_adjust" class="action-lbl" onclick="setAction('adjust')">
          <input type="radio" name="stockAction" value="adjust" style="display:none;">
          <div style="text-align:center;padding:10px 6px;border:2px solid var(--bdr);border-radius:6px;cursor:pointer;background:#fafafa;color:var(--txt2);">
            <div style="font-size:18px;margin-bottom:3px;">=</div>
            <div style="font-size:12px;font-weight:700;">Set to</div>
          </div>
        </label>
      </div>
      {{-- Quantity --}}
      <div style="margin-bottom:14px;">
        <label style="font-size:12px;font-weight:700;color:var(--txt2);display:block;margin-bottom:6px;text-transform:uppercase;letter-spacing:.4px;" id="qtyLabel">Units to Add</label>
        <input type="number" id="modalQty" min="0" value="1"
               style="width:100%;padding:11px 14px;border:1.5px solid var(--bdr);border-radius:var(--r);font-size:22px;font-weight:800;text-align:center;font-family:var(--font);outline:none;transition:.2s;"
               onfocus="this.style.borderColor='var(--pr)'" onblur="this.style.borderColor='var(--bdr)'"
               oninput="updatePreview()">
      </div>
      {{-- Preview --}}
      <div id="stockPreview" style="background:#f9f9f9;border:1px solid var(--bdr2);border-radius:6px;padding:12px;margin-bottom:14px;text-align:center;font-size:14px;color:var(--txt2);">
        <span id="previewText">—</span>
      </div>
      {{-- Note --}}
      <div style="margin-bottom:18px;">
        <label style="font-size:12px;font-weight:700;color:var(--txt2);display:block;margin-bottom:6px;text-transform:uppercase;letter-spacing:.4px;">Note / Reason (optional)</label>
        <input type="text" id="modalNote" maxlength="255" placeholder="e.g. New delivery, damaged goods..."
               style="width:100%;padding:9px 12px;border:1.5px solid var(--bdr);border-radius:var(--r);font-size:13px;font-family:var(--font);outline:none;"
               onfocus="this.style.borderColor='var(--pr)'" onblur="this.style.borderColor='var(--bdr)'">
      </div>
      {{-- Buttons --}}
      <div style="display:flex;gap:10px;">
        <button onclick="closeModal()" style="flex:1;padding:11px;border:1.5px solid var(--bdr);background:#fff;border-radius:var(--r);font-size:14px;font-weight:600;cursor:pointer;font-family:var(--font);">Cancel</button>
        <button onclick="submitStock()" id="saveStockBtn" style="flex:2;padding:11px;background:var(--pr);color:#fff;border:none;border-radius:var(--r);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--font);display:flex;align-items:center;justify-content:center;gap:8px;">
          <i class="fas fa-save"></i> Save Stock
        </button>
      </div>
      <div id="modalMsg" style="display:none;margin-top:10px;font-size:13px;font-weight:600;text-align:center;"></div>
    </div>
  </div>
</div>

@endsection
@push('styles')
<style>
.action-lbl div{transition:.15s;}
</style>
@endpush
@push('scripts')
<script>
const CSRF = document.querySelector('meta[name=csrf-token]').content;
let modalProductId = null;
let modalVariantId = null;
let modalCurStock  = 0;
let modalAction    = 'add';

function openModal(productId, variantId, name, curStock) {
  modalProductId = productId;
  modalVariantId = variantId;
  modalCurStock  = curStock;
  modalAction    = 'add';
  document.getElementById('modalTitle').textContent   = name;
  document.getElementById('modalCurrent').textContent = curStock;
  document.getElementById('modalQty').value           = 1;
  document.getElementById('modalNote').value          = '';
  document.getElementById('modalMsg').style.display   = 'none';
  setAction('add');
  updatePreview();
  document.getElementById('stockModal').style.display = 'flex';
  setTimeout(()=>document.getElementById('modalQty').focus(), 100);
}

function closeModal() {
  document.getElementById('stockModal').style.display = 'none';
}

function setAction(action) {
  modalAction = action;
  const labels = { add:'Units to Add', decrease:'Units to Remove', adjust:'Set Stock To' };
  document.getElementById('qtyLabel').textContent = labels[action];
  ['add','decrease','adjust'].forEach(a => {
    const el = document.getElementById('lbl_'+a);
    const inner = el.querySelector('div');
    if (a === action) {
      const colors = { add:'var(--ok)', decrease:'var(--err)', adjust:'var(--info)' };
      const bgs    = { add:'#e8f5e9', decrease:'#ffebee', adjust:'#e3f2fd' };
      inner.style.borderColor = colors[a];
      inner.style.background  = bgs[a];
      inner.style.color       = colors[a];
    } else {
      inner.style.borderColor = 'var(--bdr)';
      inner.style.background  = '#fafafa';
      inner.style.color       = 'var(--txt2)';
    }
  });
  updatePreview();
}

function updatePreview() {
  const qty = parseInt(document.getElementById('modalQty').value) || 0;
  const cur  = modalCurStock;
  let newVal, color;
  if (modalAction === 'add')      { newVal = cur + qty; color = 'var(--ok)'; }
  else if (modalAction === 'decrease') { newVal = Math.max(0, cur - qty); color = newVal === 0 ? 'var(--err)' : 'var(--warn)'; }
  else                             { newVal = qty; color = qty === 0 ? 'var(--err)' : 'var(--ok)'; }
  document.getElementById('previewText').innerHTML =
    `<strong>${cur}</strong> → <strong style="color:${color};font-size:18px;">${newVal}</strong> units`;
}

async function submitStock() {
  const qty  = parseInt(document.getElementById('modalQty').value);
  const note = document.getElementById('modalNote').value;
  const btn  = document.getElementById('saveStockBtn');
  const msg  = document.getElementById('modalMsg');

  if (isNaN(qty) || qty < 0) {
    showModalMsg('Please enter a valid quantity.', false); return;
  }

  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
  btn.disabled  = true;

  try {
    const res  = await fetch(`/admin/stock/${modalProductId}/update`, {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
      body: JSON.stringify({ variant_id: modalVariantId, action: modalAction, qty, note }),
    });
    const data = await res.json();
    if (data.success) {
      modalCurStock = data.new_stock;
      document.getElementById('modalCurrent').textContent = data.new_stock;
      // Update inline stock display on the row
      const key    = modalVariantId ? `vstock_${modalVariantId}` : `stock_${modalProductId}`;
      const el     = document.getElementById(key);
      if (el) {
        const sval = el.querySelector('.sval');
        if (sval) sval.textContent = data.new_stock;
        el.style.color = data.new_stock === 0 ? 'var(--err)' : data.new_stock < 5 ? 'var(--warn)' : 'var(--ok)';
      }
      showModalMsg(data.message, true);
      setTimeout(closeModal, 1100);
    } else {
      showModalMsg(data.message || 'Update failed.', false);
    }
  } catch(e) {
    showModalMsg('Network error. Please try again.', false);
  } finally {
    btn.innerHTML = '<i class="fas fa-save"></i> Save Stock';
    btn.disabled  = false;
  }
}

function showModalMsg(msg, ok) {
  const el = document.getElementById('modalMsg');
  el.style.display = '';
  el.style.color   = ok ? 'var(--ok)' : 'var(--err)';
  el.textContent   = msg;
}

document.getElementById('stockModal').addEventListener('click', e => {
  if (e.target === document.getElementById('stockModal')) closeModal();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>
@endpush
