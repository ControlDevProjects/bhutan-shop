<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Order, Product, User};

class DashboardController extends Controller {
    public function index() {
        $stats = [
            'total_orders'    => Order::count(),
            'pending_orders'  => Order::where('status','pending')->count(),
            'total_revenue'   => Order::where('payment_status','paid')->sum('total'),
            'total_products'  => Product::count(),
            'low_stock'       => Product::where('type','simple')->where('stock_type','limited')->where('stock','<',5)->count(),
            'total_customers' => User::where('role','customer')->count(),
            'by_status'       => Order::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count','status')->toArray(),
        ];
        $recentOrders = Order::with('user')->latest()->limit(8)->get();
        $lowStockProducts = Product::with('variants')
            ->where(function($q) {
                $q->where(fn($sq)=>$sq->where('type','simple')->where('stock_type','limited')->where('stock','<',5)->where('stock','>',0));
                $q->orWhereHas('variants',fn($sq)=>$sq->where('stock_type','limited')->where('stock','<',5)->where('stock','>',0));
            })->limit(8)->get();
        return view('admin.dashboard.index', compact('stats','recentOrders','lowStockProducts'));
    }
}
