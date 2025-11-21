<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_ids', 
        'name',
        'description',
        'price',
        'stock',
        'is_active',
        'options',
        'metadata',
    ];

    protected $casts = [
        'category_ids' => 'array',
        'options' => 'array',
        'metadata' => 'array',
    ];

    protected $attributes = [
        'options' => '{"size": "", "material": "", "color": ""}', // Default structure for options
        'metadata' => '{"images": [], "features": []}',           // Default structure for metadata
    ];

    public function categories()
    {
        return Category::whereIn('id', $this->category_ids)->where('status', 'Active')->get();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
