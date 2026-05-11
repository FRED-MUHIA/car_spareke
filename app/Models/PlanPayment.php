<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanPayment extends Model
{
    protected $fillable = [
        'user_id', 'pricing_plan_id', 'account_code', 'amount', 'phone', 'status',
        'merchant_request_id', 'checkout_request_id', 'response_code',
        'response_description', 'response_payload',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'response_payload' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PricingPlan::class, 'pricing_plan_id');
    }
}
