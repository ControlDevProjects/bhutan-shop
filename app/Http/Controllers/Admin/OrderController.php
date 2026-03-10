<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Order, User};
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller {
    public function __construct(private OrderService $svc) {}

    public function index(Request $request) {
        $query = Order::with(['user','assignedEmployee'])->latest();
        if ($s=$request->search) $query->where('order_number','like',"%$s%")->orWhereHas('user',fn($q)=>$q->where('name','like',"%$s%"));
        if ($st=$request->status) $query->where('status',$st);
        if ($ps=$request->payment_status) $query->where('payment_status',$ps);
        if ($pm=$request->payment_method) $query->where('payment_method',$pm);
        $orders = $query->paginate(20)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order) {
        $order->load(['items.product','items.variant','user','assignedEmployee','statusLogs.changedBy']);
        $employees = User::whereIn('role',['admin','employee'])->where('is_active',true)->orderBy('name')->get();
        return view('admin.orders.show', compact('order','employees'));
    }

    public function updateStatus(Request $request, Order $order) {
        $request->validate(['status'=>'required|in:pending,confirmed,processing,packed,shipped,out_for_delivery,delivered,cancelled,returned','note'=>'nullable|string|max:500']);
        $this->svc->updateStatus($order, $request->status, $request->note, Auth::id());
        return back()->with('success','Order status updated to: '.ucfirst(str_replace('_',' ',$request->status)));
    }

    public function markPaid(Request $request, Order $order) {
        $this->svc->markPaid($order, Auth::id());
        return back()->with('success','Order marked as PAID.');
    }

    public function assignEmployee(Request $request, Order $order) {
        $request->validate(['assigned_to'=>'nullable|exists:users,id']);
        $order->update(['assigned_to'=>$request->assigned_to]);
        return back()->with('success','Order assigned.');
    }
}
