@extends('frontend.layouts.app')
@section('title','Complete Payment')
@section('content')
<div style="max-width:540px;margin:0 auto;">
<div class="card">
<div style="text-align:center;padding:28px 20px 16px;">
    <div style="font-size:48px;margin-bottom:12px;">💳</div>
    <h1 style="font-size:22px;font-weight:700;">Complete Your Payment</h1>
    <p style="color:var(--mut);margin-top:6px;font-size:14px;">Order <strong>{{ $order->order_number }}</strong></p>
</div>
<div class="card-bd">
    <div style="background:#f8f8f8;border-radius:8px;padding:16px;margin-bottom:20px;">
        <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:8px;">
            <span>Subtotal</span><span>BTN {{ number_format($order->subtotal,2) }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:12px;">
            <span>Shipping</span><span>{{ $order->shipping_cost == 0 ? 'FREE' : 'BTN '.number_format($order->shipping_cost,2) }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:20px;font-weight:700;border-top:2px solid var(--bdr);padding-top:12px;">
            <span>Total</span><span style="color:var(--pr);">BTN {{ number_format($order->total,2) }}</span>
        </div>
    </div>
    <button id="rzp-button" class="btn btn-pr" style="width:100%;justify-content:center;padding:14px;font-size:16px;display:flex;">
        <i class="fas fa-bolt"></i> &nbsp; Pay BTN {{ number_format($order->total,2) }}
    </button>
    <form id="payVerifyForm" method="POST" action="{{ route('checkout.verify',$order) }}" style="display:none;">
        @csrf
        <input type="hidden" name="razorpay_payment_id" id="rzp_payment_id">
        <input type="hidden" name="razorpay_order_id" id="rzp_order_id">
        <input type="hidden" name="razorpay_signature" id="rzp_signature">
    </form>
    <div style="text-align:center;margin-top:14px;">
        <a href="{{ route('orders.show',$order) }}" style="color:var(--mut);font-size:13px;">Pay later / Go to order</a>
    </div>
</div>
</div>
</div>
@endsection
@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('rzp-button').addEventListener('click', function() {
    var options = {
        key: "{{ $razorpayKey }}",
        amount: {{ (int)($order->total * 100) }},
        currency: "INR", // Razorpay uses INR; in production use BTN/INR conversion
        name: "Bhutan Shop",
        description: "Order {{ $order->order_number }}",
        order_id: "{{ $order->razorpay_order_id ?? '' }}",
        image: "https://example.com/logo.png",
        handler: function(response) {
            document.getElementById('rzp_payment_id').value = response.razorpay_payment_id;
            document.getElementById('rzp_order_id').value = response.razorpay_order_id || '';
            document.getElementById('rzp_signature').value = response.razorpay_signature || '';
            document.getElementById('payVerifyForm').submit();
        },
        prefill: {
            name: "{{ $order->shipping_name }}",
            contact: "{{ $order->shipping_phone }}"
        },
        theme: { color: "#c0392b" }
    };
    var rzp = new Razorpay(options);
    rzp.on('payment.failed', function(response) {
        alert('Payment failed: ' + response.error.description);
    });
    rzp.open();
});
</script>
@endpush
