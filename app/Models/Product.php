<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {
    use SoftDeletes;
    protected $fillable = [
        'category_id','name','slug','description','type','price','stock_type','stock',
        'image_1','image_2','image_3','is_featured','status',
        'shipping_type','shipping_flat_rate','processing_days'
    ];
    protected $casts = ['price'=>'decimal:2','is_featured'=>'boolean','shipping_flat_rate'=>'decimal:2'];

    public function category()   { return $this->belongsTo(Category::class); }
    public function variants()   { return $this->hasMany(Variant::class); }
    public function attributes() { return $this->belongsToMany(Attribute::class, 'product_attributes'); }
    public function stockLogs()  { return $this->hasMany(StockLog::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function wishlists()  { return $this->hasMany(Wishlist::class); }

    public function getTotalStockAttribute(): int|string {
        if ($this->type === 'variant') {
            $variants = $this->variants;
            if ($variants->contains(fn($v) => $v->stock_type === 'unlimited')) return 'Unlimited';
            return $variants->sum('stock');
        }
        if ($this->stock_type === 'unlimited') return 'Unlimited';
        return $this->stock;
    }

    public function getPriceDisplayAttribute(): string {
        if ($this->type === 'simple') return 'BTN '.number_format($this->price, 2);
        $prices = $this->variants->pluck('price');
        if ($prices->isEmpty()) return 'BTN 0.00';
        $min = $prices->min(); $max = $prices->max();
        return $min == $max ? 'BTN '.number_format($min,2) : 'BTN '.number_format($min,2).' – BTN '.number_format($max,2);
    }

    public function getMinPriceAttribute(): float {
        if ($this->type === 'simple') return (float)$this->price;
        return (float)($this->variants->min('price') ?? 0);
    }

    public function getPrimaryImageAttribute(): ?string {
        return $this->image_1 ?? $this->image_2 ?? $this->image_3 ?? null;
    }

    public function getIsLowStockAttribute(): bool {
        if ($this->type === 'variant') return $this->variants->some(fn($v) => $v->stock_type==='limited' && $v->stock < 5 && $v->stock > 0);
        if ($this->stock_type === 'unlimited') return false;
        return $this->stock < 5;
    }

    public function getInStockAttribute(): bool {
        if ($this->type === 'variant') return $this->variants->some(fn($v) => $v->stock_type==='unlimited' || $v->stock > 0);
        if ($this->stock_type === 'unlimited') return true;
        return $this->stock > 0;
    }

    // Compute shipping cost for this product based on order subtotal
    public function getShippingCost(float $orderSubtotal): float {
        return match($this->shipping_type ?? 'standard') {
            'free'       => 0.0,
            'flat_rate'  => (float)($this->shipping_flat_rate ?? 150),
            'express'    => 300.0,
            default      => $orderSubtotal >= 5000 ? 0.0 : 150.0,
        };
    }

    // Expected delivery date
    public function getExpectedDeliveryAttribute(): array {
        $processingDays = $this->processing_days ?? 1;
        $shippingDays = match($this->shipping_type ?? 'standard') {
            'express' => 1,
            'free'    => 5,
            default   => 3,
        };
        $minDate = now()->addDays($processingDays + $shippingDays);
        $maxDate = now()->addDays($processingDays + $shippingDays + 2);
        return [
            'min' => $minDate,
            'max' => $maxDate,
            'min_str' => $minDate->format('D, d M'),
            'max_str' => $maxDate->format('D, d M'),
            'processing_days' => $processingDays,
            'shipping_days'   => $shippingDays,
        ];
    }
}
