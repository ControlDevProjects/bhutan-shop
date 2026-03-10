<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Attribute extends Model {
    protected $fillable = ['name'];
    public function options() { return $this->hasMany(AttributeOption::class); }
    public function products() { return $this->belongsToMany(Product::class, 'product_attributes'); }
}
