<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    protected $fillable = [
        'order_number','user_id','shipping_name','shipping_phone','shipping_address',
        'shipping_city','shipping_dzongkhag','notes','subtotal','shipping_cost','total',
        'payment_method','payment_status','razorpay_order_id','razorpay_payment_id',
        'status','assigned_to','paid_at','shipped_at','delivered_at'
    ];
    protected $casts = ['paid_at'=>'datetime','shipped_at'=>'datetime','delivered_at'=>'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function assignedEmployee() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function statusLogs() { return $this->hasMany(OrderStatusLog::class)->orderBy('created_at','desc'); }

    public static function generateNumber(): string {
        return 'BHT-'.date('YmdHis').'-'.strtoupper(substr(uniqid(),0,6));
    }

    public function getStatusColorAttribute(): string {
        return match($this->status) {
            'pending'          => 'warning',
            'confirmed'        => 'info',
            'processing'       => 'info',
            'packed'           => 'info',
            'shipped'          => 'primary',
            'out_for_delivery' => 'primary',
            'delivered'        => 'success',
            'cancelled'        => 'danger',
            'returned'         => 'danger',
            default            => 'secondary',
        };
    }

    public function getPaymentStatusColorAttribute(): string {
        return match($this->payment_status) {
            'paid'     => 'success',
            'pending'  => 'warning',
            'failed'   => 'danger',
            'refunded' => 'info',
            default    => 'secondary',
        };
    }

    public function canBeCancelled(): bool {
        return in_array($this->status, ['pending','confirmed','processing']);
    }
}
