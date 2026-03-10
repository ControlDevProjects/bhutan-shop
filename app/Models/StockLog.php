<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StockLog extends Model {
    public $timestamps = false;
    protected $fillable = ['product_id','variant_id','old_stock','new_stock','old_price','new_price','changed_by','note','created_at'];
    protected $casts = ['created_at'=>'datetime','old_price'=>'decimal:2','new_price'=>'decimal:2'];
    public function product() { return $this->belongsTo(Product::class); }
    public function variant() { return $this->belongsTo(Variant::class); }
}
