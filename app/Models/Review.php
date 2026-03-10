<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Review extends Model {
    protected $fillable = ['product_id','user_id','rating','title','body','is_approved'];
    protected $casts    = ['is_approved'=>'boolean','rating'=>'integer'];

    public function product() { return $this->belongsTo(Product::class); }
    public function user()    { return $this->belongsTo(User::class); }

    public function getStarsHtmlAttribute(): string {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $stars .= $i <= $this->rating
                ? '<i class="fas fa-star" style="color:#f5a623;"></i>'
                : '<i class="far fa-star" style="color:#ddd;"></i>';
        }
        return $stars;
    }
}
