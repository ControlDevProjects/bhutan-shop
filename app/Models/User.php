<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable {
    use Notifiable;
    protected $fillable = ['name','email','phone','address','city','dzongkhag','role','is_active','password'];
    protected $hidden = ['password','remember_token'];
    protected $casts = ['password' => 'hashed'];

    public function orders(): HasMany { return $this->hasMany(Order::class); }
    public function assignedOrders(): HasMany { return $this->hasMany(Order::class, 'assigned_to'); }
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isEmployee(): bool { return $this->role === 'employee'; }
    public function isCustomer(): bool { return $this->role === 'customer'; }
    public function isStaff(): bool { return in_array($this->role, ['admin','employee']); }
}
