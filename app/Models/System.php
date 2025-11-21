<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'parent_id',
        'system_name',
        'system_url',
        'type',
        'owner',
        'configurations',
        'global_product_options',
        'settings',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'owner' => 'array',
        'configurations' => 'array',
        'global_product_options' => 'array',
        'settings' => 'array',
        'logs' => 'array',
    ];
}
