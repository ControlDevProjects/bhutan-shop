<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\{Product, Category};
use Illuminate\Http\Request;

class ProductController extends Controller {
    public function index(Request $request) {
        $query = Product::with(['category','variants'])->where('status','active');

        if ($s=$request->search)    $query->where('name','like',"%$s%");
        if ($c=$request->category)  $query->where('category_id',$c);
        if ($t=$request->type)      $query->where('type',$t);

        // Availability filter
        if ($request->availability === 'in_stock') {
            $query->where(function($q) {
                $q->where(function($sq) {
                    $sq->where('type','simple')
                       ->where(fn($x)=>$x->where('stock_type','unlimited')->orWhere('stock','>',0));
                })->orWhere(function($sq) {
                    $sq->where('type','variant')
                       ->whereHas('variants',fn($x)=>$x->where(fn($y)=>$y->where('stock_type','unlimited')->orWhere('stock','>',0)));
                });
            });
        }

        switch($request->sort) {
            case 'price_asc':  $query->orderByRaw("COALESCE(price,(SELECT MIN(price) FROM variants WHERE product_id=products.id AND deleted_at IS NULL)) ASC"); break;
            case 'price_desc': $query->orderByRaw("COALESCE(price,(SELECT MAX(price) FROM variants WHERE product_id=products.id AND deleted_at IS NULL)) DESC"); break;
            case 'name_asc':   $query->orderBy('name'); break;
            case 'newest':     $query->latest(); break;
            default:           $query->orderByRaw("is_featured DESC, created_at DESC"); break;
        }

        $products  = $query->paginate(16)->withQueryString();
        $categories= Category::where('is_active',true)->orderBy('name')->get();
        return view('frontend.products.index', compact('products','categories'));
    }

    public function show(string $slug) {
        $product = Product::with([
            'category',
            'variants.attributeOptions.attribute',
            'attributes.options',
            'reviews.user',
        ])->where('slug',$slug)->where('status','active')->firstOrFail();

        $variantMap = [];
        foreach ($product->variants as $v) {
            $key = $v->attributeOptions->pluck('id')->sort()->values()->join(',');
            $variantMap[$key] = [
                'id'         => $v->id,
                'name'       => $v->name,
                'sku'        => $v->sku,
                'price'      => $v->price,
                'stock_type' => $v->stock_type,
                'stock'      => $v->stock,
                'image_1'    => $v->image_1 ? asset('storage/'.$v->image_1) : null,
                'image_2'    => $v->image_2 ? asset('storage/'.$v->image_2) : null,
                'image_3'    => $v->image_3 ? asset('storage/'.$v->image_3) : null,
            ];
        }

        // User's own review (if any)
        $userReview = auth()->check()
            ? $product->reviews->firstWhere('user_id', auth()->id())
            : null;

        // Average rating
        $avgRating  = $product->reviews->avg('rating') ?? 0;
        $ratingCount = $product->reviews->count();

        return view('frontend.products.show', compact('product','variantMap','userReview','avgRating','ratingCount'));
    }
}
