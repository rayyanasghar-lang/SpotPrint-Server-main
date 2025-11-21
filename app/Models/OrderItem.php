<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_snapshot',
        'selected_options',
        'files',
        'quantity',
        'unit_price',
        'total_price',
        'price_detail',
        'status_logs',
    ];

    protected $casts = [
        'product_snapshot' => 'array',
        'selected_options' => 'array',
        'files' => 'array',
        'status_logs' => 'array',
        'price_detail' => 'array'
    ];

    protected $attributes = [
        'product_snapshot' => '{"name": "", "price": 0.00, "stock": 0, "options": {}}', // Default structure for product snapshot
        'selected_options' => '{"size": "", "material": "", "color": ""}',              // Default structure for selected options
        'files' => '[]',            // Default to an empty array
        'status_logs' => '[]',      // Default to an empty array
    ];

    protected $hidden = [
        //'product_snapshot',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
