<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\{Product, Variant};
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller {
    public function __construct(private CartService $cart) {}

    public function index() {
        $items    = $this->cart->get();
        $subtotal = $this->cart->total();
        $shipping = $this->cart->calculateShipping();
        return view('frontend.cart.index', compact('items','subtotal','shipping'));
    }

    public function add(Request $request) {
        // Support both JSON body (AJAX) and form POST
        $data = $request->all();
        if (empty($data) && $request->getContent()) {
            $data = json_decode($request->getContent(), true) ?? [];
            $request->merge($data);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'nullable|integer|min:1',
        ]);

        $qty       = max(1, (int)($request->qty ?? 1));
        $variantId = $request->variant_id ? (int)$request->variant_id : null;

        try {
            $this->cart->add((int)$request->product_id, $variantId, $qty);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json(['success'=>false,'message'=>'Could not add to cart: '.$e->getMessage()], 422);
            }
            return back()->with('error', 'Could not add to cart.');
        }

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json(['count'=>$this->cart->count(),'message'=>'Added to cart!','success'=>true]);
        }
        return back()->with('success','Added to cart! 🛒');
    }

    public function update(Request $request, string $key) {
        $this->cart->update($key, (int)$request->qty);
        if ($request->ajax()) return response()->json(['count'=>$this->cart->count()]);
        return back()->with('success','Cart updated.');
    }

    public function remove(string $key) {
        $this->cart->remove($key);
        if (request()->ajax()) return response()->json(['count'=>$this->cart->count()]);
        return back()->with('success','Item removed.');
    }

    public function count() {
        return response()->json(['count'=>$this->cart->count()]);
    }

    // API: get full product variant data for quick-add panel
    public function quickView(int $productId) {
        $product = Product::with(['variants.attributeOptions.attribute','attributes.options','category'])
            ->findOrFail($productId);

        $variantMap = [];
        foreach ($product->variants as $v) {
            $key = $v->attributeOptions->pluck('id')->sort()->values()->join(',');
            $variantMap[$key] = [
                'id'         => $v->id,
                'name'       => $v->name,
                'price'      => $v->price,
                'stock_type' => $v->stock_type,
                'stock'      => $v->stock,
                'in_stock'   => $v->stock_type === 'unlimited' || $v->stock > 0,
                'image_1'    => $v->image_1 ? asset('storage/'.$v->image_1) : null,
                'image_2'    => $v->image_2 ? asset('storage/'.$v->image_2) : null,
                'image_3'    => $v->image_3 ? asset('storage/'.$v->image_3) : null,
            ];
        }

        // Build attribute→options map with availability
        $attributes = [];
        foreach ($product->attributes as $attr) {
            $opts = [];
            foreach ($attr->options as $opt) {
                // Check if any variant with this option is in stock
                $available = $product->variants->some(function($v) use ($opt) {
                    return $v->attributeOptions->contains('id', $opt->id)
                        && ($v->stock_type === 'unlimited' || $v->stock > 0);
                });
                $opts[] = ['id'=>$opt->id,'value'=>$opt->value,'available'=>$available];
            }
            $attributes[] = ['id'=>$attr->id,'name'=>$attr->name,'options'=>$opts];
        }

        // Find cheapest available variant
        $cheapestVariant = $product->variants
            ->filter(fn($v) => $v->stock_type === 'unlimited' || $v->stock > 0)
            ->sortBy('price')
            ->first();

        return response()->json([
            'id'           => $product->id,
            'name'         => $product->name,
            'slug'         => $product->slug,
            'type'         => $product->type,
            'description'  => $product->description,
            'price'        => $product->type === 'simple' ? $product->price : ($cheapestVariant?->price ?? 0),
            'price_display'=> $product->price_display,
            'stock_type'   => $product->stock_type,
            'stock'        => $product->stock,
            'in_stock'     => $product->in_stock,
            'image_1'      => $product->image_1 ? asset('storage/'.$product->image_1) : null,
            'image_2'      => $product->image_2 ? asset('storage/'.$product->image_2) : null,
            'image_3'      => $product->image_3 ? asset('storage/'.$product->image_3) : null,
            'attributes'   => $attributes,
            'variant_map'  => $variantMap,
            'default_variant_id' => $cheapestVariant?->id,
            'default_variant_option_ids' => $cheapestVariant ? $cheapestVariant->attributeOptions->pluck('id') : [],
        ]);
    }
}
