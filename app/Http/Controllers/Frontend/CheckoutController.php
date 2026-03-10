<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Services\{CartService, OrderService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller {
    public function __construct(private CartService $cart, private OrderService $orderSvc) {}

    public function index() {
        if (!Auth::check()) return redirect()->route('login')->with('error','Please login to checkout.');
        $items = $this->cart->get();
        if (empty($items)) return redirect()->route('cart.index')->with('error','Your cart is empty.');
        $subtotal = $this->cart->total();
        $shipping = $this->cart->calculateShipping();
        $user = Auth::user();
        return view('frontend.checkout.index', compact('items','subtotal','shipping','user'));
    }

    public function store(Request $request) {
        if (!Auth::check()) return redirect()->route('login');
        $items = $this->cart->get();
        if (empty($items)) return redirect()->route('cart');

        $request->validate([
            'shipping_name'     => 'required|string|max:100',
            'shipping_phone'    => 'required|string|max:20',
            'shipping_address'  => 'required|string',
            'shipping_city'     => 'required|string|max:100',
            'shipping_dzongkhag'=> 'required|string|max:100',
            'payment_method'    => 'required|in:cod,razorpay',
        ]);

        $order = $this->orderSvc->createFromCart($items, $request->all(), $request->payment_method);
        $this->cart->clear();

        if ($request->payment_method === 'razorpay') {
            return redirect()->route('checkout.payment', $order)->with('info','Complete your payment below.');
        }
        return redirect()->route('orders.show', $order)->with('success','Order placed! 🎉 Your order number is '.$order->order_number);
    }

    public function payment(\App\Models\Order $order) {
        if ($order->user_id !== Auth::id()) abort(403);
        if ($order->payment_status === 'paid') return redirect()->route('orders.show',$order);
        // Razorpay order creation
        $razorpayKey = config('services.razorpay.key_id','rzp_test_demo');
        return view('frontend.checkout.payment', compact('order','razorpayKey'));
    }

    public function verifyPayment(Request $request, \App\Models\Order $order) {
        if ($order->user_id !== Auth::id()) abort(403);
        $request->validate(['razorpay_payment_id'=>'required','razorpay_order_id'=>'required','razorpay_signature'=>'required']);

        // In production, verify signature with Razorpay SDK
        // For demo, we trust the payment
        $order->update([
            'payment_status'     => 'paid',
            'razorpay_payment_id'=> $request->razorpay_payment_id,
            'razorpay_order_id'  => $request->razorpay_order_id,
            'paid_at'            => now(),
            'status'             => 'confirmed',
        ]);
        $this->orderSvc->updateStatus($order,'confirmed','Payment verified via Razorpay',Auth::id());
        return redirect()->route('orders.show',$order)->with('success','Payment successful! 🎉');
    }
}
