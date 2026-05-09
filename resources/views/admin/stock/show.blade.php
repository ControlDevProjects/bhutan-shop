@extends('admin.layouts.app')
@section('title','Stock — '.$product->name)
@section('page-title','Stock Management')
@section('topbar-actions')
    <a href="{{ route('admin.stock.index') }}" class="btn btn-sc btn-sm"><i class="fas fa-arrow-left"></i> Back to Stock</a>
    <a href="{{ route('admin.products.edit',$product) }}" class="btn btn-sc btn-sm"><i class="fas fa-edit"></i> Edit Product</a>
@endsection
@section('content')

{{-- Product header card --}}
<div class="card mb16">
<div class="card-bd" style="display:flex;gap:16px;align-items:center;">
    @if($product->primary_image)
    <img src="{{ asset('storage/'.$product->primary_image) }}" style="width:72px;height:72px;object-fit:cover;border-radius:8px;border:1px solid var(--bdr);flex-shrink:0;">
    @endif
    <div style="flex:1;">
        <div style="font-size:18px;font-weight:800;">{{ $product->name }}</div>
        <div style="font-size:12px;color:var(--mut);margin-top:2px;">
            {{ $product->category?->name ?? 'No Category' }} ·
            <span class="badge {{ $product->type==='simple'?'b-info':'b-sec' }}">{{ ucfirst($product->type) }}</span>
            · <span class="badge b-{{ $product->status==='active'?'ok':'err' }}">{{ ucfirst($product->status) }}</span>
        </div>
    </div>
    <div style="text-align:right;">
        <div style="font-size:22px;font-weight:800;color:var(--pr);">{{ $product->price_display }}</div>
        <div style="font-size:12px;color:var(--mut);">Current price</div>
    </div>
</div>
</div>

<div style="display:grid;grid-template-columns:1fr 380px;gap:16px;align-items:start;">

{{-- Left: stock levels + adjust --}}
<div>

@if($product->type === 'simple')
{{-- ── Simple product stock ── --}}
<div class="card mb16">
<div class="card-hd"><h2><i class="fas fa-warehouse" style="color:var(--pr);"></i> Stock Level</h2></div>
<div class="card-bd">
    @if($product->stock_type === 'unlimited')
    <div style="text-align:center;padding:20px 0;">
        <div style="font-size:48px;color:var(--info);margin-bottom:8px;"><i class="fas fa-infinity"></i></div>
        <div style="font-size:16px;font-weight:700;color:var(--info);">Unlimited Stock</div>
        <div style="font-size:12.5px;color:var(--mut);margin-top:4px;">This product has no stock limit</div>
    </div>
    @else
    <div style="display:flex;gap:24px;align-items:center;flex-wrap:wrap;">
        <div style="text-align:center;padding:16px 24px;background:#fafafa;border-radius:8px;border:1px solid var(--bdr);">
            <div style="font-size:52px;font-weight:800;line-height:1;color:{{ $product->stock==0?'var(--err)':($product->stock<5?'var(--warn)':'var(--ok)') }};" id="simpleStockVal">{{ $product->stock }}</div>
            <div style="font-size:12px;color:var(--mut);margin-top:4px;">units in stock</div>
        </div>
        <div style="flex:1;">
            @if($product->stock == 0)
            <div style="padding:10px 14px;background:#ffebee;border:1px solid #ffcdd2;border-radius:6px;color:var(--err);font-weight:600;font-size:13.5px;margin-bottom:12px;">
                <i class="fas fa-times-circle"></i> Out of Stock — customers cannot order
            </div>
            @elseif($product->stock < 5)
            <div style="padding:10px 14px;background:#fff8e1;border:1px solid #ffe082;border-radius:6px;color:var(--warn);font-weight:600;font-size:13.5px;margin-bottom:12px;">
                <i class="fas fa-exclamation-triangle"></i> Low Stock — restock soon!
            </div>
            @else
            <div style="padding:10px 14px;background:#e8f5e9;border:1px solid #a5d6a7;border-radius:6px;color:var(--ok);font-weight:600;font-size:13.5px;margin-bottom:12px;">
                <i class="fas fa-check-circle"></i> In Stock
            </div>
            @endif
            <button onclick="openModal({{ $product->id }},null,'{{ addslashes($product->name) }}',{{ $product->stock }})"
                    class="btn btn-pr" style="width:100%;justify-content:center;gap:8px;">
                <i class="fas fa-sliders-h"></i> Adjust Stock
            </button>
        </div>
    </div>
    @endif
</div>
</div>

@else
{{-- ── Variant product stock ── --}}
<div class="card mb16">
<div class="card-hd">
    <h2><i class="fas fa-layer-group" style="color:var(--pr);"></i> Variant Stock ({{ $product->variants->count() }} variants)</h2>
</div>
<div class="tw">
<table>
<thead>
    <tr>
        <th>Variant</th>
        <th>Options</th>
        <th>Stock Type</th>
        <th style="text-align:center;">Current Stock</th>
        <th style="text-align:center;">Status</th>
        <th style="text-align:center;">Adjust</th>
    </tr>
</thead>
<tbody>
@foreach($product->variants as $v)
@php
  $opts = $v->attributeOptions->map(fn($o) => $o->attribute->name.': '.$o->value)->join(', ');
@endphp
<tr id="vrow_{{ $v->id }}">
    <td style="font-weight:600;">{{ $v->name }}</td>
    <td style="font-size:12px;color:var(--mut);">{{ $opts }}</td>
    <td>
        <span class="badge {{ $v->stock_type==='unlimited'?'b-info':'b-sec' }}">{{ ucfirst($v->stock_type) }}</span>
    </td>
    <td style="text-align:center;">
        @if($v->stock_type === 'unlimited')
        <span style="color:var(--info);"><i class="fas fa-infinity"></i></span>
        @else
        <span id="vstock_{{ $v->id }}" style="font-size:18px;font-weight:800;color:{{ $v->stock==0?'var(--err)':($v->stock<5?'var(--warn)':'var(--ok)') }};">
            <span class="sval">{{ $v->stock }}</span>
        </span>
        @endif
    </td>
    <td style="text-align:center;">
        @if($v->stock_type === 'unlimited')
        <span style="font-size:11.5px;color:var(--info);font-weight:600;"><i class="fas fa-infinity"></i> Unlimited</span>
        @elseif($v->stock == 0)
        <span class="badge b-err" style="font-size:11px;"><i class="fas fa-times-circle"></i> Out</span>
        @elseif($v->stock < 5)
        <span class="badge b-warn" style="font-size:11px;"><i class="fas fa-exclamation-triangle"></i> Low</span>
        @else
        <span class="badge b-ok" style="font-size:11px;"><i class="fas fa-check"></i> Good</span>
        @endif
    </td>
    <td style="text-align:center;">
        @if($v->stock_type === 'unlimited')
        <span style="font-size:11px;color:var(--mut);font-style:italic;">N/A</span>
        @else
        <button onclick="openModal({{ $product->id }},{{ $v->id }},'{{ addslashes($product->name) }} — {{ addslashes($v->name) }}',{{ $v->stock }})"
                class="btn btn-sc btn-sm" style="padding:5px 12px;font-size:12px;">
            <i class="fas fa-sliders-h"></i> Adjust
        </button>
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

{{-- Right: Recent activity summary --}}
<div>
<div class="card">
<div class="card-hd"><h2><i class="fas fa-chart-bar" style="color:var(--pr);"></i> Quick Summary</h2></div>
<div class="card-bd" style="font-size:13px;">
    @php
      $totalAdded   = 0; $totalRemoved = 0;
      foreach($logs as $log) {
        $diff = ($log->new_stock ?? 0) - ($log->old_stock ?? 0);
        if ($diff > 0) $totalAdded += $diff;
        else $totalRemoved += abs($diff);
      }
    @endphp
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;">
        <div style="padding:14px;background:#e8f5e9;border-radius:8px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:var(--ok);">+{{ $totalAdded }}</div>
            <div style="font-size:11.5px;color:var(--ok);">Units added</div>
        </div>
        <div style="padding:14px;background:#ffebee;border-radius:8px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:var(--err);">−{{ $totalRemoved }}</div>
            <div style="font-size:11.5px;color:var(--err);">Units removed</div>
        </div>
    </div>
    <div style="font-size:12.5px;color:var(--mut);">{{ $logs->total() }} total log entries</div>
</div>
</div>
</div>
</div>

{{-- ── Full Stock Log History ── --}}
<div class="card" style="margin-top:18px;">
<div class="card-hd"><h2><i class="fas fa-history" style="color:var(--pr);"></i> Stock History ({{ $logs->total() }} entries)</h2></div>
@if($logs->isEmpty())
<div class="card-bd" style="text-align:center;padding:32px;color:var(--mut);">
    <div style="font-size:32px;margin-bottom:10px;">📋</div>
    No stock history yet. Adjustments will appear here.
</div>
@else
<div class="tw">
<table>
<thead>
    <tr>
        <th>Date & Time</th>
        <th>Variant</th>
        <th style="text-align:center;">Before</th>
        <th style="text-align:center;">Change</th>
        <th style="text-align:center;">After</th>
        <th>Note / Reason</th>
        <th>Changed By</th>
    </tr>
</thead>
<tbody>
@foreach($logs as $log)
@php
  $diff     = ($log->new_stock ?? 0) - ($log->old_stock ?? 0);
  $isAdd    = $diff > 0;
  $isAdj    = $log->note && str_contains($log->note, 'Adjusted');
@endphp
<tr>
    <td style="white-space:nowrap;font-size:12px;">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y') }}<br><span style="color:var(--mut);">{{ \Carbon\Carbon::parse($log->created_at)->format('h:i A') }}</span></td>
    <td style="font-size:12.5px;">{{ $log->variant?->name ?? '—' }}</td>
    <td style="text-align:center;font-size:14px;font-weight:600;color:var(--txt2);">{{ $log->old_stock ?? '—' }}</td>
    <td style="text-align:center;">
        @if($diff != 0)
        <span style="font-size:14px;font-weight:800;color:{{ $diff>0?'var(--ok)':'var(--err)' }};">
            {{ $diff > 0 ? '+' : '' }}{{ $diff }}
        </span>
        @else
        <span style="color:var(--mut);font-size:12px;">adjusted</span>
        @endif
    </td>
    <td style="text-align:center;">
        <span style="font-size:16px;font-weight:800;color:{{ ($log->new_stock??0)==0?'var(--err)':(($log->new_stock??0)<5?'var(--warn)':'var(--ok)') }};">
            {{ $log->new_stock ?? '—' }}
        </span>
    </td>
    <td style="font-size:12.5px;color:var(--txt2);max-width:200px;">{{ $log->note ?? '—' }}</td>
    <td style="font-size:12px;color:var(--mut);">{{ $log->changed_by ?? '—' }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@if($logs->hasPages())
<div style="padding:14px 18px;">{{ $logs->links() }}</div>
@endif
@endif
</div>

{{-- ═══ ADJUST MODAL (shared with index page) ═══ --}}
<div id="stockModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9000;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:10px;width:420px;max-width:95vw;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;">
    <div style="background:var(--pr);color:#fff;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
      <div>
        <div style="font-size:15px;font-weight:700;" id="modalTitle">Adjust Stock</div>
        <div style="font-size:12px;opacity:.8;margin-top:2px;">Current: <span id="modalCurrent">—</span> units</div>
      </div>
      <button onclick="closeModal()" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:30px;height:30px;border-radius:50%;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-family:inherit;">&times;</button>
    </div>
    <div style="padding:20px;">
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:18px;">
        <label id="lbl_add" onclick="setAction('add')">
          <div style="text-align:center;padding:10px 6px;border:2px solid var(--ok);border-radius:6px;cursor:pointer;background:#e8f5e9;color:var(--ok);">
            <div style="font-size:18px;margin-bottom:3px;">+</div><div style="font-size:12px;font-weight:700;">Add</div>
          </div>
        </label>
        <label id="lbl_decrease" onclick="setAction('decrease')">
          <div style="text-align:center;padding:10px 6px;border:2px solid var(--bdr);border-radius:6px;cursor:pointer;background:#fafafa;color:var(--txt2);">
            <div style="font-size:18px;margin-bottom:3px;">−</div><div style="font-size:12px;font-weight:700;">Decrease</div>
          </div>
        </label>
        <label id="lbl_adjust" onclick="setAction('adjust')">
          <div style="text-align:center;padding:10px 6px;border:2px solid var(--bdr);border-radius:6px;cursor:pointer;background:#fafafa;color:var(--txt2);">
            <div style="font-size:18px;margin-bottom:3px;">=</div><div style="font-size:12px;font-weight:700;">Set to</div>
          </div>
        </label>
      </div>
      <div style="margin-bottom:14px;">
        <label style="font-size:12px;font-weight:700;color:var(--txt2);display:block;margin-bottom:6px;text-transform:uppercase;letter-spacing:.4px;" id="qtyLabel">Units to Add</label>
        <input type="number" id="modalQty" min="0" value="1"
               style="width:100%;padding:11px 14px;border:1.5px solid var(--bdr);border-radius:var(--r);font-size:22px;font-weight:800;text-align:center;font-family:var(--font);outline:none;"
               oninput="updatePreview()">
      </div>
      <div id="stockPreview" style="background:#f9f9f9;border:1px solid var(--bdr2);border-radius:6px;padding:12px;margin-bottom:14px;text-align:center;font-size:14px;color:var(--txt2);">
        <span id="previewText">—</span>
      </div>
      <div style="margin-bottom:18px;">
        <label style="font-size:12px;font-weight:700;color:var(--txt2);display:block;margin-bottom:6px;text-transform:uppercase;letter-spacing:.4px;">Note / Reason (optional)</label>
        <input type="text" id="modalNote" maxlength="255" placeholder="e.g. New delivery, stock correction..."
               style="width:100%;padding:9px 12px;border:1.5px solid var(--bdr);border-radius:var(--r);font-size:13px;font-family:var(--font);outline:none;">
      </div>
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
@push('scripts')
<script>
const CSRF = document.querySelector('meta[name=csrf-token]').content;
let modalProductId = {{ $product->id }};
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
  const modal = document.getElementById('stockModal');
  modal.style.display = 'flex';
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
    const inner = document.getElementById('lbl_'+a).querySelector('div');
    const colors = { add:'var(--ok)', decrease:'var(--err)', adjust:'var(--info)' };
    const bgs    = { add:'#e8f5e9',   decrease:'#ffebee',   adjust:'#e3f2fd' };
    if (a === action) {
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
  const cur = modalCurStock;
  let newVal, color;
  if (modalAction === 'add')           { newVal = cur + qty;             color = 'var(--ok)'; }
  else if (modalAction === 'decrease') { newVal = Math.max(0, cur - qty);color = newVal===0?'var(--err)':'var(--warn)'; }
  else                                 { newVal = qty;                    color = qty===0?'var(--err)':'var(--ok)'; }
  document.getElementById('previewText').innerHTML =
    `<strong>${cur}</strong> → <strong style="color:${color};font-size:18px;">${newVal}</strong> units`;
}

async function submitStock() {
  const qty  = parseInt(document.getElementById('modalQty').value);
  const note = document.getElementById('modalNote').value;
  const btn  = document.getElementById('saveStockBtn');

  if (isNaN(qty) || qty < 0) { showModalMsg('Enter a valid quantity.', false); return; }

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
      // Update stock display
      const el = document.getElementById(modalVariantId ? `vstock_${modalVariantId}` : 'simpleStockVal');
      if (el) {
        const sval = el.querySelector ? el.querySelector('.sval') : null;
        if (sval) sval.textContent = data.new_stock;
        else el.textContent = data.new_stock;
        el.style.color = data.new_stock===0?'var(--err)':data.new_stock<5?'var(--warn)':'var(--ok)';
      }
      showModalMsg(data.message, true);
      setTimeout(() => { closeModal(); location.reload(); }, 1200);
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
document.addEventListener('keydown', e => { if (e.key==='Escape') closeModal(); });
</script>
@endpush
