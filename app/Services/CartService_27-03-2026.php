<?php
namespace App\Services;
use App\Models\{Product, Variant};
use Illuminate\Support\Facades\Session;

class CartService {
    const KEY = 'bhutan_cart';

    public function get(): array { return Session::get(self::KEY, []); }

    public function add(int $productId, ?int $variantId, int $qty): array {
        $cart = $this->get();
        $key  = $productId.'-'.($variantId ?? '0');
        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $product = Product::findOrFail($productId);
            $variant = $variantId ? Variant::findOrFail($variantId) : null;
            $price   = $variant ? $variant->price : $product->price;
            $cart[$key] = [
                'product_id'   => $productId,
                'variant_id'   => $variantId,
                'name'         => $product->name,
                'variant_name' => $variant?->name,
                'sku'          => $variant?->sku,
                'price'        => (float)$price,
                'image'        => $variant?->primary_image ?? $product->primary_image,
                'qty'          => $qty,
                'slug'         => $product->slug,
                'shipping_type'=> $product->shipping_type ?? 'standard',
            ];
        }
        Session::put(self::KEY, $cart);
        return $cart;
    }

    public function update(string $key, int $qty): void {
        $cart = $this->get();
        if ($qty <= 0) unset($cart[$key]);
        else $cart[$key]['qty'] = $qty;
        Session::put(self::KEY, $cart);
    }

    public function remove(string $key): void {
        $cart = $this->get();
        unset($cart[$key]);
        Session::put(self::KEY, $cart);
    }

    public function clear(): void { Session::forget(self::KEY); }

    public function total(): float {
        return array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $this->get()));
    }

    public function count(): int {
        return array_sum(array_column($this->get(), 'qty'));
    }

    public function calculateShipping(): float {
        $subtotal = $this->total();
        $items    = $this->get();
        // If any item has free shipping, it's free
        foreach ($items as $item) {
            if (($item['shipping_type'] ?? 'standard') === 'free') return 0.0;
        }
        // Express shipping if any item needs it
        foreach ($items as $item) {
            if (($item['shipping_type'] ?? 'standard') === 'express') return 300.0;
        }
        // Check flat_rate
        foreach ($items as $item) {
            if (($item['shipping_type'] ?? 'standard') === 'flat_rate') {
                // Will use product's flat_rate; use 150 as default
                return 150.0;
            }
        }
        // Standard: free above 5000
        return $subtotal >= 5000 ? 0.0 : 150.0;
    }
}
