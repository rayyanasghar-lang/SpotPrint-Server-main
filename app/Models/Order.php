<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str; 

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'user_id',
        'session_id',
        'order_number',
        'address_info',
        'metadata',
        'price_detail',
        'total',
        'order_status', // 'Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered', 'Completed', 'Cancelled', 'Expired'
        'payment_status', // 'Unpaid', 'Paid', 'Cancelled', 'Failed', 'Overdue', 'Refunded'
        'status_logs',
        'shipping_carrier',
        'tracking_number',
        'payment_method',
        'transaction_id',
        'payment_details',
        'payment_logs',
    ];

    protected $casts = [
        'address_info' => 'array',
        'metadata' => 'array',
        'price_detail' => 'array',
        'status_logs' => 'array',
        'payment_details' => 'array',
        'payment_logs' => 'array',
    ];

    protected $attributes = [
        'address_info' => '{"billing_address": {}, "shipping_address": {}}', // Default structure for address info
        'price_detail' => '{"subtotal": 0.00, "discount": 0.00, "tax": 0.00, "shipping_cost": 0.00}', // Default structure for price details
        'status_logs' => '[]',      // Default to an empty array
        'metadata' => '{}',         // Default empty metadata
        'payment_details' => '{}',  // Default empty payment details
        'payment_logs' => '[]',     // Default to an empty array
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function couponRedemption()
    {
        return $this->hasOne(CouponRedemption::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateSessionId()
    {
        return (string) Str::uuid(); // Generate a unique session_id using UUID.
    }

    public static function generateOrderNumber()
    {
        // Generate a unique order number
        $orderPrefix = 'ORD-'; // Prefix for order numbers
        $timestamp = now()->format('YmdHis'); // Current timestamp in YmdHis format
        $randomNumber = mt_rand(1000, 9999); // Random 4-digit number
        return $orderPrefix . $timestamp . '-' . $randomNumber;
    }
}
