<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Product, Variant, StockLog};
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller {
    public function __construct(private ProductService $svc) {}

    // Master stock overview page
    public function index(Request $request) {
        $query = Product::with(['category','variants','stockLogs' => fn($q) => $q->latest('created_at')->limit(1)])
            ->latest();
        if ($s = $request->search)   $query->where('name','like',"%$s%");
        if ($c = $request->category) $query->where('category_id',$c);
        if ($request->filter === 'low')     $query->where('stock','<',5)->where('stock_type','limited');
        if ($request->filter === 'out')     $query->where('stock',0)->where('stock_type','limited');
        if ($request->filter === 'unlimited') $query->where('stock_type','unlimited');

        $products   = $query->paginate(20)->withQueryString();
        $categories = \App\Models\Category::orderBy('name')->get();

        // Summary counts
        $totalProducts = Product::count();
        $outOfStock    = Product::where(function($q){ $q->where('type','simple')->where('stock_type','limited')->where('stock',0); })->orWhereHas('variants', fn($q) => $q->where('stock_type','limited')->where('stock',0))->count();
        $lowStock      = Product::where(function($q){ $q->where('type','simple')->where('stock_type','limited')->where('stock','>',0)->where('stock','<',5); })->orWhereHas('variants', fn($q) => $q->where('stock_type','limited')->where('stock','>',0)->where('stock','<',5))->count();
        $unlimited     = Product::where('stock_type','unlimited')->orWhereHas('variants', fn($q) => $q->where('stock_type','unlimited'))->count();

        return view('admin.stock.index', compact('products','categories','totalProducts','outOfStock','lowStock','unlimited'));
    }

    // Per-product stock detail page
    public function show(Product $product) {
        $product->load(['category','variants.attributeOptions.attribute']);
        $logs = StockLog::where('product_id',$product->id)
            ->with('variant')
            ->latest('created_at')
            ->paginate(30);
        return view('admin.stock.show', compact('product','logs'));
    }

    // AJAX: update stock for a simple product or a variant
    public function update(Request $request, Product $product) {
        $request->validate([
            'variant_id' => 'nullable|exists:variants,id',
            'action'     => 'required|in:add,decrease,adjust',
            'qty'        => 'required|integer|min:0',
            'note'       => 'nullable|string|max:255',
        ]);

        $variantId = $request->variant_id ? (int)$request->variant_id : null;
        $action    = $request->action;
        $qty       = (int)$request->qty;
        $note      = $request->note ?? '';
        $by        = Auth::user()->name;

        if ($variantId) {
            $variant = Variant::findOrFail($variantId);
            if ($variant->stock_type === 'unlimited') {
                return response()->json(['success'=>false,'message'=>'This variant has unlimited stock — no adjustment needed.'], 422);
            }
            $oldStock = $variant->stock;
            $newStock = match($action) {
                'add'      => $oldStock + $qty,
                'decrease' => max(0, $oldStock - $qty),
                'adjust'   => $qty,
            };
            $variant->update(['stock' => $newStock]);
        } else {
            if ($product->stock_type === 'unlimited') {
                return response()->json(['success'=>false,'message'=>'This product has unlimited stock — no adjustment needed.'], 422);
            }
            $oldStock = $product->stock;
            $newStock = match($action) {
                'add'      => $oldStock + $qty,
                'decrease' => max(0, $oldStock - $qty),
                'adjust'   => $qty,
            };
            $product->update(['stock' => $newStock]);
        }

        // Log it
        $actionLabel = match($action) {
            'add'      => "Added $qty units",
            'decrease' => "Decreased by $qty units",
            'adjust'   => "Adjusted to $qty units",
        };
        StockLog::create([
            'product_id' => $product->id,
            'variant_id' => $variantId,
            'old_stock'  => $oldStock,
            'new_stock'  => $newStock,
            'old_price'  => null,
            'new_price'  => null,
            'changed_by' => $by,
            'note'       => trim("$actionLabel" . ($note ? " — $note" : '')),
            'created_at' => now(),
        ]);

        return response()->json([
            'success'   => true,
            'message'   => "Stock updated: $oldStock → $newStock",
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
        ]);
    }
}
