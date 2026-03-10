<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller {
    public function store(Request $request, int $productId) {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:120',
            'body'   => 'nullable|string|max:2000',
        ]);

        $existing = Review::where('product_id', $productId)
                          ->where('user_id', Auth::id())
                          ->first();

        if ($existing) {
            $existing->update([
                'rating' => $request->rating,
                'title'  => $request->title,
                'body'   => $request->body,
            ]);
            $msg = 'Your review has been updated.';
        } else {
            Review::create([
                'product_id'  => $productId,
                'user_id'     => Auth::id(),
                'rating'      => $request->rating,
                'title'       => $request->title,
                'body'        => $request->body,
                'is_approved' => true,
            ]);
            $msg = 'Review submitted! Thank you.';
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success'=>true,'message'=>$msg]);
        }
        return back()->with('success', $msg);
    }

    public function destroy(int $productId) {
        Review::where('product_id', $productId)
              ->where('user_id', Auth::id())
              ->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success'=>true,'message'=>'Review deleted.']);
        }
        return back()->with('success', 'Review deleted.');
    }
}
