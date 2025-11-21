<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\SoftDeletes;


class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'type',
        'value',
        'description',
        'start_date',
        'end_date',
        'max_uses',
        'used_count',
        'status',
        'category',
    ];

    protected $casts = [
        'gift_card_details' => 'array',
        'details' => 'array',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user');
    }

    public function redemptions()
    {
        return $this->hasMany(CouponRedemption::class);
    }

    public function isValid($time_zone = null)
    {
        $currentDate = now($time_zone);
        return $this->status == 'Yes' &&
            // ($this->start_date == null || $this->start_date <= $currentDate) &&
            ($this->end_date == null || $this->end_date >= $currentDate) &&
            ($this->max_uses == null || $this->max_uses > $this->used_count);
    }
}
