<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\{Product, Category};

class HomeController extends Controller {
    public function index() {
        // Featured products (shown in hero banner area)
        $featured = Product::with(['category','variants'])
            ->where('status','active')
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();

        // New arrivals
        $newArrivals = Product::with(['category','variants'])
            ->where('status','active')
            ->latest()
            ->take(10)
            ->get();

        // Best sellers (by order count)
        $bestSellers = Product::with(['category','variants'])
            ->withCount('orderItems')
            ->where('status','active')
            ->orderByDesc('order_items_count')
            ->take(10)
            ->get();

        // All active categories
        $categories = Category::where('is_active', true)
            ->withCount(['products' => fn($q) => $q->where('status','active')])
            ->orderByDesc('products_count')
            ->get();

        // In-stock deals (low stock urgency)
        $deals = Product::with(['category','variants'])
            ->where('status','active')
            ->where(function($q) {
                $q->where(fn($s) => $s->where('type','simple')->where('stock_type','limited')->whereBetween('stock',[1,10]))
                  ->orWhere(fn($s) => $s->where('type','variant')->whereHas('variants', fn($v) => $v->where('stock_type','limited')->whereBetween('stock',[1,10])));
            })
            ->take(6)
            ->get();

        return view('frontend.home', compact('featured','newArrivals','bestSellers','categories','deals'));
    }
}
