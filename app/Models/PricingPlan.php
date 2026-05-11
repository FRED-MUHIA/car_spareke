<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    protected $fillable = [
        'name', 'slug', 'price', 'billing_period', 'listing_limit',
        'features', 'is_featured', 'cta_label',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_featured' => 'boolean',
        ];
    }
}
