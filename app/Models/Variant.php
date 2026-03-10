<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model {
    use SoftDeletes;
    protected $fillable = ['product_id','name','sku','price','stock_type','stock','image_1','image_2','image_3'];
    protected $casts = ['price'=>'decimal:2'];

    public function product() { return $this->belongsTo(Product::class); }
    public function attributeOptions() { return $this->belongsToMany(AttributeOption::class, 'variant_attribute_options'); }
    public function stockLogs() { return $this->hasMany(StockLog::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }

    public function getPrimaryImageAttribute(): ?string { return $this->image_1 ?? $this->image_2 ?? $this->image_3 ?? null; }
    public function getInStockAttribute(): bool { return $this->stock_type === 'unlimited' || $this->stock > 0; }
    public function getStockDisplayAttribute(): string {
        if ($this->stock_type === 'unlimited') return 'In Stock';
        return $this->stock > 0 ? $this->stock.' in stock' : 'Out of Stock';
    }
}
