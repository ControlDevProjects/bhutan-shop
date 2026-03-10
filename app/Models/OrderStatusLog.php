<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderStatusLog extends Model {
    public $timestamps = false;
    protected $fillable = ['order_id','old_status','new_status','note','changed_by','created_at'];
    protected $casts = ['created_at'=>'datetime'];
    public function order() { return $this->belongsTo(Order::class); }
    public function changedBy() { return $this->belongsTo(User::class,'changed_by'); }
}
