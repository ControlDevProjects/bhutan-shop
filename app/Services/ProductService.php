<?php
namespace App\Services;
use App\Models\{Product, Variant, StockLog, AttributeOption};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{DB, Storage};

class ProductService {
    public function generateSlug(string $name, ?int $excludeId = null): string {
        $slug = Str::slug($name); $original = $slug; $count = 1;
        while (true) {
            $q = Product::where('slug', $slug);
            if ($excludeId) $q->where('id','!=',$excludeId);
            if (!$q->exists()) break;
            $slug = $original.'-'.$count++;
        }
        return $slug;
    }

    public function generateSku(Product $product, array $optionValues): string {
        $prefix = strtoupper(substr(Str::slug($product->name),0,4));
        $suffix = strtoupper(implode('-', array_map(fn($v) => substr(Str::slug($v),0,3), $optionValues)));
        $base = $prefix.'-'.$suffix; $sku = $base; $count = 1;
        while (Variant::where('sku',$sku)->exists()) $sku = $base.'-'.$count++;
        return $sku;
    }

    public function handleProductImages(Request $request, ?Product $product = null): array {
        $images = ['image_1'=>$product?->image_1,'image_2'=>$product?->image_2,'image_3'=>$product?->image_3];
        foreach ([1,2,3] as $i) {
            $key = "image_{$i}";
            if ($request->hasFile($key)) {
                if ($product?->$key) Storage::disk('public')->delete($product->$key);
                $images[$key] = $request->file($key)->store('products','public');
            } elseif ($request->boolean("remove_{$key}")) {
                if ($product?->$key) Storage::disk('public')->delete($product->$key);
                $images[$key] = null;
            }
        }
        return $images;
    }

    public function handleVariantImages(Request $request, string $prefix, ?Variant $variant = null): array {
        $images = ['image_1'=>$variant?->image_1,'image_2'=>$variant?->image_2,'image_3'=>$variant?->image_3];
        foreach ([1,2,3] as $i) {
            $key = "image_{$i}";
            $fileKey = "{$prefix}{$key}";
            if ($request->hasFile($fileKey)) {
                if ($variant?->$key) Storage::disk('public')->delete($variant->$key);
                $images[$key] = $request->file($fileKey)->store('products/variants','public');
            } elseif ($request->boolean("remove_{$prefix}{$key}")) {
                if ($variant?->$key) Storage::disk('public')->delete($variant->$key);
                $images[$key] = null;
            }
        }
        return $images;
    }

    public function logChange(int $productId, ?int $variantId, ?int $oldStock, ?int $newStock, $oldPrice, $newPrice, string $by='admin'): void {
        if ($oldStock===$newStock && $oldPrice==$newPrice) return;
        StockLog::create(['product_id'=>$productId,'variant_id'=>$variantId,'old_stock'=>$oldStock,'new_stock'=>$newStock,'old_price'=>$oldPrice,'new_price'=>$newPrice,'changed_by'=>$by,'created_at'=>now()]);
    }

    public function decrementStock(Product $product, ?int $variantId, int $qty): void {
        if ($variantId) {
            $v = Variant::find($variantId);
            if ($v && $v->stock_type==='limited') $v->decrement('stock',$qty);
        } else {
            if ($product->stock_type==='limited') $product->decrement('stock',$qty);
        }
    }

    public function incrementStock(Product $product, ?int $variantId, int $qty): void {
        if ($variantId) {
            $v = Variant::find($variantId);
            if ($v && $v->stock_type==='limited') $v->increment('stock',$qty);
        } else {
            if ($product->stock_type==='limited') $product->increment('stock',$qty);
        }
    }
}
