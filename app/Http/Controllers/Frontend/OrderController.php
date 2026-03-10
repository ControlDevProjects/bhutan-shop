<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller {
    public function __construct(private OrderService $svc) {}

    public function index() {
        $orders = Order::where('user_id',Auth::id())->with('items')->latest()->paginate(10);
        return view('frontend.orders.index', compact('orders'));
    }

    public function show(Order $order) {
        if ($order->user_id !== Auth::id()) abort(403);
        $order->load(['items.product','items.variant','statusLogs']);
        return view('frontend.orders.show', compact('order'));
    }

    public function cancel(Order $order) {
        if ($order->user_id !== Auth::id()) abort(403);
        if (!$order->canBeCancelled()) return back()->with('error','This order cannot be cancelled.');
        $this->svc->updateStatus($order,'cancelled','Cancelled by customer',Auth::id());
        return back()->with('success','Order cancelled.');
    }
}
