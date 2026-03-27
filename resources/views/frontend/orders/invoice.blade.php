@php
  $currency    = $settings['currency_symbol']  ?? 'BTN';
  $gstPct      = (float)($settings['gst_percentage']  ?? 0);
  $gstLabel    = $settings['gst_label']         ?? 'GST';
  $gstInclusive= ($settings['gst_inclusive']    ?? '1') === '1';
  $companyName = $settings['company_name']      ?? 'Bhutan Shop';
  $companyTag  = $settings['company_tagline']   ?? '';
  $companyAddr = $settings['company_address']   ?? 'Thimphu, Bhutan';
  $companyEmail= $settings['company_email']     ?? '';
  $companyPhone= $settings['company_phone']     ?? '';
  $gstin       = $settings['company_gstin']     ?? '';
  $footer      = $settings['invoice_footer']    ?? 'Thank you for your purchase!';

  $subtotal    = (float)$order->subtotal;
  $shipping    = (float)$order->shipping_cost;
  $gstAmount   = $gstPct > 0
    ? ($gstInclusive
        ? round($subtotal * $gstPct / (100 + $gstPct), 2)
        : round($subtotal * $gstPct / 100, 2))
    : 0;
  $grandTotal  = $subtotal + $shipping + ($gstInclusive ? 0 : $gstAmount);

  $isPaid      = $order->payment_status === 'paid';
  $statusColor = match($order->status) {
    'delivered'       => '#2e7d32',
    'cancelled','returned' => '#c62828',
    'shipped','out_for_delivery' => '#1565c0',
    default           => '#e65100',
  };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Invoice {{ $order->order_number }} — {{ $companyName }}</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box;}
  body{font-family:'Segoe UI',Arial,sans-serif;font-size:13px;color:#1a1a1a;background:#f0f2f5;line-height:1.5;}
  .page{max-width:820px;margin:30px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.10);}

  /* ── Top toolbar (screen only) ── */
  .toolbar{background:#1a1a2e;color:#fff;padding:12px 32px;display:flex;align-items:center;justify-content:space-between;gap:12px;}
  .toolbar a,.toolbar button{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;padding:7px 16px;border-radius:5px;font-size:12.5px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px;font-family:inherit;transition:.15s;}
  .toolbar a:hover,.toolbar button:hover{background:rgba(255,255,255,.22);}
  .toolbar-right{display:flex;gap:8px;}

  /* ── Invoice body ── */
  .inv{padding:40px 48px;}

  /* Header band */
  .inv-header{display:flex;justify-content:space-between;align-items:flex-start;padding-bottom:28px;border-bottom:2px solid #f0f0f0;margin-bottom:28px;gap:20px;}
  .brand-name{font-size:26px;font-weight:800;color:#c0392b;letter-spacing:-.5px;}
  .brand-tag{font-size:12px;color:#888;margin-top:2px;}
  .brand-detail{font-size:12px;color:#555;margin-top:8px;line-height:1.7;}
  .inv-meta{text-align:right;}
  .inv-title{font-size:28px;font-weight:800;color:#1a1a1a;letter-spacing:1px;text-transform:uppercase;}
  .inv-number{font-family:monospace;font-size:15px;font-weight:700;color:#c0392b;margin-top:4px;}
  .inv-date{font-size:12px;color:#888;margin-top:4px;}
  .status-stamp{display:inline-block;padding:5px 18px;border-radius:20px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;margin-top:8px;border:2px solid;}

  /* Address grid */
  .addr-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:28px;}
  .addr-box{background:#fafafa;border:1px solid #eee;border-radius:8px;padding:16px;}
  .addr-box-title{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#aaa;margin-bottom:8px;}
  .addr-name{font-weight:700;font-size:14px;color:#1a1a1a;margin-bottom:4px;}
  .addr-line{font-size:12.5px;color:#555;line-height:1.7;}

  /* Items table */
  .items-table{width:100%;border-collapse:collapse;margin-bottom:24px;}
  .items-table thead tr{background:#1a1a2e;color:#fff;}
  .items-table th{padding:11px 14px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;}
  .items-table th:last-child,.items-table td:last-child{text-align:right;}
  .items-table th:nth-child(2),.items-table td:nth-child(2){text-align:center;}
  .items-table th:nth-child(3),.items-table td:nth-child(3){text-align:right;}
  .items-table tbody tr{border-bottom:1px solid #f0f0f0;transition:background .15s;}
  .items-table tbody tr:hover{background:#fafafa;}
  .items-table td{padding:13px 14px;font-size:13px;vertical-align:middle;}
  .items-table tbody tr:last-child{border-bottom:none;}
  .product-name{font-weight:600;color:#1a1a1a;}
  .product-variant{font-size:11.5px;color:#c0392b;background:#fff3f0;padding:2px 7px;border-radius:3px;display:inline-block;margin-top:3px;}
  .product-sku{font-family:monospace;font-size:11px;color:#aaa;display:block;margin-top:2px;}

  /* Totals */
  .totals-wrap{display:flex;justify-content:flex-end;margin-bottom:28px;}
  .totals-box{width:280px;border:1px solid #eee;border-radius:8px;overflow:hidden;}
  .totals-row{display:flex;justify-content:space-between;padding:9px 16px;font-size:13px;border-bottom:1px solid #f4f4f4;}
  .totals-row:last-child{border-bottom:none;}
  .totals-row.grand{background:#1a1a2e;color:#fff;font-size:15px;font-weight:800;padding:12px 16px;}
  .totals-row.grand .val{color:#f5a623;}
  .totals-label{color:#666;}
  .totals-row.grand .totals-label{color:#ccc;}
  .gst-note{font-size:11px;color:#888;text-align:right;padding:5px 16px;background:#fafafa;}

  /* Payment badge */
  .pay-section{display:flex;gap:20px;margin-bottom:28px;flex-wrap:wrap;}
  .pay-box{flex:1;min-width:200px;background:#fafafa;border:1px solid #eee;border-radius:8px;padding:16px;}
  .pay-box-title{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#aaa;margin-bottom:8px;}
  .pay-status{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700;}
  .pay-status.paid{background:#e8f5e9;color:#2e7d32;}
  .pay-status.pending{background:#fff8e1;color:#f57f17;}
  .pay-status.failed{background:#ffebee;color:#c62828;}

  /* Footer */
  .inv-footer{border-top:2px solid #f0f0f0;padding-top:20px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;}
  .inv-footer-note{font-size:12px;color:#888;font-style:italic;flex:1;}
  .inv-footer-brand{font-size:13px;font-weight:700;color:#c0392b;}
  .page-num{font-size:11px;color:#ccc;text-align:center;margin-top:12px;}

  /* Paid stamp overlay */
  .paid-stamp{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-25deg);font-size:80px;font-weight:900;color:rgba(46,125,50,.08);letter-spacing:6px;pointer-events:none;z-index:0;text-transform:uppercase;}

  /* Print */
  @media print {
    body{background:#fff;}
    .toolbar,.no-print{display:none!important;}
    .page{box-shadow:none;margin:0;border-radius:0;}
    .inv{padding:24px 32px;}
    .items-table tbody tr:hover{background:transparent;}
  }
</style>
</head>
<body>

{{-- Toolbar (screen only) --}}
<div class="toolbar no-print">
  <div style="display:flex;align-items:center;gap:10px;">
    @auth
    <a href="{{ url()->previous() }}">← Back</a>
    @endauth
    <span style="font-size:13px;opacity:.7;">Invoice {{ $order->order_number }}</span>
  </div>
  <div class="toolbar-right">
    <button onclick="window.print()">🖨️ Print</button>
    <button onclick="downloadPDF()">⬇️ Save PDF</button>
  </div>
</div>

<div class="page">
<div class="inv">

  @if($isPaid)
  <div class="paid-stamp" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-30deg);font-size:90px;font-weight:900;color:rgba(46,125,50,.06);pointer-events:none;z-index:0;text-transform:uppercase;letter-spacing:8px;white-space:nowrap;">PAID</div>
  @endif

  {{-- ── Header ── --}}
  <div class="inv-header">
    <div>
      <div class="brand-name">{{ $companyName }}</div>
      @if($companyTag)<div class="brand-tag">{{ $companyTag }}</div>@endif
      <div class="brand-detail">
        {{ $companyAddr }}<br>
        @if($companyPhone)📞 {{ $companyPhone }}<br>@endif
        @if($companyEmail)✉️ {{ $companyEmail }}<br>@endif
        @if($gstin)<strong>GSTIN:</strong> {{ $gstin }}@endif
      </div>
    </div>
    <div class="inv-meta">
      <div class="inv-title">Tax Invoice</div>
      <div class="inv-number">{{ $order->order_number }}</div>
      <div class="inv-date">Issued: {{ $order->created_at->format('d F Y') }}</div>
      @if($order->delivered_at)
      <div class="inv-date">Delivered: {{ \Carbon\Carbon::parse($order->delivered_at)->format('d F Y') }}</div>
      @endif
      <span class="status-stamp"
            style="color:{{ $statusColor }};border-color:{{ $statusColor }};background:{{ $statusColor }}18;">
        {{ ucwords(str_replace('_',' ',$order->status)) }}
      </span>
    </div>
  </div>

  {{-- ── Address grid ── --}}
  <div class="addr-grid">
    <div class="addr-box">
      <div class="addr-box-title">📦 Ship To</div>
      <div class="addr-name">{{ $order->shipping_name }}</div>
      <div class="addr-line">
        {{ $order->shipping_address }}<br>
        {{ $order->shipping_city }}, {{ $order->shipping_dzongkhag }}<br>
        📞 {{ $order->shipping_phone }}
      </div>
    </div>
    <div class="addr-box">
      <div class="addr-box-title">👤 Customer</div>
      <div class="addr-name">{{ $order->user->name }}</div>
      <div class="addr-line">
        {{ $order->user->email }}<br>
        @if($order->user->phone)📞 {{ $order->user->phone }}@endif
      </div>
    </div>
    <div class="addr-box">
      <div class="addr-box-title">📋 Order Info</div>
      <div class="addr-line">
        <strong>Order #:</strong> {{ $order->order_number }}<br>
        <strong>Date:</strong> {{ $order->created_at->format('d M Y') }}<br>
        <strong>Payment:</strong> {{ strtoupper(str_replace('_',' ',$order->payment_method)) }}<br>
        @if($order->notes)<strong>Note:</strong> {{ $order->notes }}@endif
      </div>
    </div>
  </div>

  {{-- ── Items table ── --}}
  <table class="items-table">
    <thead>
      <tr>
        <th style="width:45%;">Item Description</th>
        <th style="width:10%;">Qty</th>
        <th style="width:18%;">Unit Price</th>
        @if($gstPct > 0 && !$gstInclusive)
        <th style="width:12%;">{{ $gstLabel }}</th>
        @endif
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($order->items as $item)
      @php
        $itemGst = $gstPct > 0 && !$gstInclusive
          ? round($item->price * $gstPct / 100, 2)
          : 0;
        $itemTotal = $item->price * $item->quantity + ($itemGst * $item->quantity);
      @endphp
      <tr>
        <td>
          <div class="product-name">{{ $item->product_name }}</div>
          @if($item->variant_name)
          <span class="product-variant">{{ $item->variant_name }}</span>
          @endif
          @if($item->sku)
          <span class="product-sku">SKU: {{ $item->sku }}</span>
          @endif
        </td>
        <td style="text-align:center;font-weight:600;">{{ $item->quantity }}</td>
        <td style="text-align:right;">{{ $currency }} {{ number_format($item->price,2) }}</td>
        @if($gstPct > 0 && !$gstInclusive)
        <td style="text-align:right;color:#888;">{{ $currency }} {{ number_format($itemGst,2) }}</td>
        @endif
        <td style="text-align:right;font-weight:700;color:#1a1a1a;">
          {{ $currency }} {{ number_format($gstInclusive ? $item->subtotal : $itemTotal, 2) }}
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{-- ── Totals ── --}}
  <div class="totals-wrap">
    <div class="totals-box">
      <div class="totals-row">
        <span class="totals-label">Subtotal</span>
        <span class="val">{{ $currency }} {{ number_format($subtotal,2) }}</span>
      </div>
      <div class="totals-row">
        <span class="totals-label">Shipping</span>
        <span class="val" style="{{ $shipping == 0 ? 'color:#2e7d32;font-weight:700;' : '' }}">
          {{ $shipping == 0 ? 'FREE' : $currency.' '.number_format($shipping,2) }}
        </span>
      </div>
      @if($gstPct > 0)
      <div class="totals-row">
        <span class="totals-label">{{ $gstLabel }} ({{ $gstPct }}%{{ $gstInclusive ? ', incl.' : '' }})</span>
        <span class="val" style="color:#888;">{{ $currency }} {{ number_format($gstAmount,2) }}</span>
      </div>
      @endif
      <div class="totals-row grand">
        <span class="totals-label">Grand Total</span>
        <span class="val">{{ $currency }} {{ number_format($grandTotal,2) }}</span>
      </div>
      @if($gstPct > 0 && $gstInclusive)
      <div class="gst-note">Incl. {{ $gstLabel }} of {{ $currency }} {{ number_format($gstAmount,2) }}</div>
      @endif
    </div>
  </div>

  {{-- ── Payment info ── --}}
  <div class="pay-section">
    <div class="pay-box">
      <div class="pay-box-title">💳 Payment Status</div>
      <span class="pay-status {{ $order->payment_status }}">
        @if($order->payment_status === 'paid')✔@elseif($order->payment_status === 'failed')✕@else⏳@endif
        {{ ucfirst($order->payment_status) }}
      </span>
      @if($order->paid_at)
      <div style="font-size:11.5px;color:#888;margin-top:6px;">Paid on {{ \Carbon\Carbon::parse($order->paid_at)->format('d M Y, h:i A') }}</div>
      @endif
    </div>
    <div class="pay-box">
      <div class="pay-box-title">💰 Payment Method</div>
      <div style="font-weight:700;font-size:14px;margin-top:4px;">
        @if($order->payment_method === 'cod')
          💵 Cash on Delivery
        @elseif($order->payment_method === 'razorpay')
          💳 Razorpay (Online)
        @else
          🏦 Bank Transfer
        @endif
      </div>
      @if($order->razorpay_payment_id)
      <div style="font-family:monospace;font-size:11px;color:#aaa;margin-top:4px;">Ref: {{ $order->razorpay_payment_id }}</div>
      @endif
    </div>
    @if($order->shipped_at || $order->delivered_at)
    <div class="pay-box">
      <div class="pay-box-title">🚚 Delivery Timeline</div>
      <div style="font-size:12.5px;line-height:1.9;">
        @if($order->shipped_at)
        <div>📦 Shipped: {{ \Carbon\Carbon::parse($order->shipped_at)->format('d M Y') }}</div>
        @endif
        @if($order->delivered_at)
        <div>✅ Delivered: {{ \Carbon\Carbon::parse($order->delivered_at)->format('d M Y') }}</div>
        @endif
      </div>
    </div>
    @endif
  </div>

  {{-- ── Footer ── --}}
  <div class="inv-footer">
    <div class="inv-footer-note">"{{ $footer }}"</div>
    <div style="text-align:right;">
      <div class="inv-footer-brand">{{ $companyName }}</div>
      <div style="font-size:11px;color:#aaa;margin-top:3px;">Authorised Signatory</div>
      <div style="width:120px;border-top:1.5px solid #ccc;margin:24px 0 4px auto;"></div>
      <div style="font-size:11px;color:#aaa;">Signature</div>
    </div>
  </div>

  <div class="page-num" style="margin-top:16px;font-size:11px;color:#ccc;">
    This is a computer-generated invoice and does not require a physical signature.
  </div>

</div>{{-- /.inv --}}
</div>{{-- /.page --}}

<script>
function downloadPDF() {
  // Uses browser's built-in print-to-PDF
  const origTitle = document.title;
  document.title = 'Invoice_{{ $order->order_number }}';
  window.print();
  document.title = origTitle;
}
</script>
</body>
</html>
