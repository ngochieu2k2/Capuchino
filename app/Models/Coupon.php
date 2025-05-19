<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';
    protected $fillable = [
        'code',
        'discount',
        'start_date',
        'end_date',
        'usage_limit',
        'user_limit',
        'is_active',
        'min_order_amount',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected static function booted()
    {
        static::retrieved(function ($coupon) {
            if ($coupon->expired() && $coupon->is_active) {
                // Tránh gây lặp vô hạn: dùng query builder thay vì model
                static::where('id', $coupon->id)->update(['is_active' => false]);
                $coupon->is_active = false; // cập nhật local model
            }
        });

        static::saving(function ($coupon) {
            if ($coupon->expired()) {
                $coupon->is_active = false;
            }
        });
    }

    public function expired()
    {
        return $this->end_date && now()->greaterThan($this->end_date);
    }
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('used_count')->withTimestamps();
    }
}
