<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Variant;
use App\Services\ProductService;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'stock_type' => 'required|in:limited,unlimited',
            'stock' => 'nullable|integer|min:0',
            'attribute_option_ids' => 'required|array|min:1',
            'attribute_option_ids.*' => 'exists:attribute_options,id',
            'image_1' => 'nullable|image|max:2048',
            'image_2' => 'nullable|image|max:2048',
            'image_3' => 'nullable|image|max:2048',
        ]);

        // Check if this combination already exists
        $existingVariant = $this->findVariantByOptions($product, $validated['attribute_option_ids']);
        if ($existingVariant) {
            return response()->json([
                'success' => false,
                'message' => 'This variant combination already exists.',
            ], 422);
        }

        // Handle images
        foreach ([1, 2, 3] as $slot) {
            $field = "image_{$slot}";
            if ($request->hasFile($field)) {
                $validated[$field] = $this->productService->uploadImage(
                    $request->file($field),
                    'products/variants'
                );
            } else {
                unset($validated[$field]);
            }
        }

        $variant = $this->productService->createVariant($product, $validated);
        $variant->load('attributeOptions.attribute');

        return response()->json([
            'success' => true,
            'message' => 'Variant added successfully!',
            'variant' => [
                'id' => $variant->id,
                'name' => $variant->name,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'stock_type' => $variant->stock_type,
                'stock' => $variant->stock,
                'image_1' => $variant->image_1,
                'image_2' => $variant->image_2,
                'image_3' => $variant->image_3,
                'attribute_option_ids' => $variant->attributeOptions->pluck('id')->toArray(),
            ],
        ]);
    }

    public function update(Request $request, Product $product, Variant $variant)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'stock_type' => 'required|in:limited,unlimited',
            'stock' => 'nullable|integer|min:0',
        ]);

        $this->productService->updateVariant($variant, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Variant updated successfully!',
            'variant' => [
                'id' => $variant->id,
                'price' => $variant->fresh()->price,
                'stock_type' => $variant->fresh()->stock_type,
                'stock' => $variant->fresh()->stock,
            ],
        ]);
    }

    public function destroy(Product $product, Variant $variant)
    {
        $this->productService->deleteVariant($variant);
        return response()->json(['success' => true, 'message' => 'Variant removed!']);
    }

    public function updateImages(Request $request, Product $product, Variant $variant)
    {
        $request->validate([
            'image_1' => 'nullable|image|max:2048',
            'image_2' => 'nullable|image|max:2048',
            'image_3' => 'nullable|image|max:2048',
        ]);

        $images = [];
        foreach ([1, 2, 3] as $slot) {
            if ($request->hasFile("image_{$slot}")) {
                $images[$slot] = $request->file("image_{$slot}");
            }
        }

        $this->productService->updateVariantImages($variant, $images);

        return response()->json([
            'success' => true,
            'variant' => [
                'image_1' => $variant->fresh()->image_1,
                'image_2' => $variant->fresh()->image_2,
                'image_3' => $variant->fresh()->image_3,
            ],
        ]);
    }

    public function removeImage(Request $request, Product $product, Variant $variant)
    {
        $slot = $request->input('slot');
        $this->productService->removeVariantImage($variant, $slot);
        return response()->json(['success' => true]);
    }

    private function findVariantByOptions(Product $product, array $optionIds): ?Variant
    {
        sort($optionIds);

        foreach ($product->variants as $variant) {
            $variantOptionIds = $variant->attributeOptions->pluck('id')->sort()->values()->toArray();
            if ($variantOptionIds === $optionIds) {
                return $variant;
            }
        }

        return null;
    }
}
