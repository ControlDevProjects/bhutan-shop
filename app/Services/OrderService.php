<?php
namespace App\Services;
use App\Models\{Order, OrderItem, OrderStatusLog};
use Illuminate\Support\Facades\{DB, Auth};

class OrderService {
    public function __construct(private ProductService $productService) {}

    public function createFromCart(array $cartItems, array $data, string $paymentMethod): Order {
        return DB::transaction(function() use ($cartItems, $data, $paymentMethod) {
            $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cartItems));
            $shipping = $subtotal >= 5000 ? 0 : 150; // Free shipping over BTN 5000

            $order = Order::create([
                'order_number'      => Order::generateNumber(),
                'user_id'           => Auth::id(),
                'shipping_name'     => $data['shipping_name'],
                'shipping_phone'    => $data['shipping_phone'],
                'shipping_address'  => $data['shipping_address'],
                'shipping_city'     => $data['shipping_city'],
                'shipping_dzongkhag'=> $data['shipping_dzongkhag'],
                'notes'             => $data['notes'] ?? null,
                'subtotal'          => $subtotal,
                'shipping_cost'     => $shipping,
                'total'             => $subtotal + $shipping,
                'payment_method'    => $paymentMethod,
                'payment_status'    => $paymentMethod === 'razorpay' ? 'pending' : 'pending',
                'status'            => 'pending',
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'],
                    'variant_id'   => $item['variant_id'],
                    'product_name' => $item['name'],
                    'variant_name' => $item['variant_name'],
                    'sku'          => $item['sku'],
                    'price'        => $item['price'],
                    'quantity'     => $item['qty'],
                    'subtotal'     => $item['price'] * $item['qty'],
                ]);
                // Decrement stock
                $product = \App\Models\Product::find($item['product_id']);
                if ($product) $this->productService->decrementStock($product, $item['variant_id'], $item['qty']);
            }

            $this->logStatus($order, null, 'pending', 'Order placed', Auth::id());
            return $order;
        });
    }

    public function updateStatus(Order $order, string $newStatus, ?string $note = null, ?int $userId = null): void {
        $old = $order->status;
        $order->update(['status' => $newStatus]);

        if ($newStatus === 'shipped') $order->update(['shipped_at' => now()]);
        if ($newStatus === 'delivered') {
            $order->update(['delivered_at' => now()]);
            if ($order->payment_method === 'cod') $this->markPaid($order, $userId);
        }
        if ($newStatus === 'cancelled') {
            // Restore stock
            foreach ($order->items as $item) {
                $product = \App\Models\Product::find($item->product_id);
                if ($product) $this->productService->incrementStock($product, $item->variant_id, $item->quantity);
            }
        }

        $this->logStatus($order, $old, $newStatus, $note, $userId);
    }

    public function markPaid(Order $order, ?int $userId = null): void {
        $order->update(['payment_status' => 'paid', 'paid_at' => now()]);
        $this->logStatus($order, $order->status, $order->status, 'Payment confirmed', $userId);
    }

    private function logStatus(Order $order, ?string $old, string $new, ?string $note, ?int $userId): void {
        OrderStatusLog::create([
            'order_id'   => $order->id,
            'old_status' => $old,
            'new_status' => $new,
            'note'       => $note,
            'changed_by' => $userId,
            'created_at' => now(),
        ]);
    }
}
