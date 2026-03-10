<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\{Wishlist, Product};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller {
    public function index() {
        $items = Wishlist::with(['product.category','product.variants'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
        return view('frontend.wishlist.index', compact('items'));
    }

    public function toggle(Request $request) {
        $request->validate(['product_id'=>'required|exists:products,id']);
        $productId = $request->product_id;
        $userId    = Auth::id();

        $existing = Wishlist::where('user_id',$userId)->where('product_id',$productId)->first();
        if ($existing) {
            $existing->delete();
            $msg = 'removed'; $count = Wishlist::where('user_id',$userId)->count();
            return response()->json(['status'=>'removed','message'=>'Removed from wishlist','count'=>$count]);
        }
        Wishlist::create(['user_id'=>$userId,'product_id'=>$productId]);
        $count = Wishlist::where('user_id',$userId)->count();
        return response()->json(['status'=>'added','message'=>'Added to wishlist ❤️','count'=>$count]);
    }

    public function remove(int $productId) {
        Wishlist::where('user_id',Auth::id())->where('product_id',$productId)->delete();
        if (request()->ajax()) return response()->json(['success'=>true]);
        return back()->with('success','Removed from wishlist.');
    }

    public function ids() {
        if (!Auth::check()) return response()->json(['ids'=>[]]);
        $ids = Wishlist::where('user_id',Auth::id())->pluck('product_id');
        return response()->json(['ids'=>$ids,'count'=>$ids->count()]);
    }
}
