<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Garage extends Model
{
    protected $fillable = [
        'user_id', 'name', 'slug', 'location', 'phone', 'whatsapp', 'image_url', 'license_path', 'public_verified_at', 'license_rejection_reason', 'services', 'specialization_brands',
        'rating', 'review_count', 'description', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'services' => 'array',
            'specialization_brands' => 'array',
            'public_verified_at' => 'datetime',
            'is_featured' => 'boolean',
        ];
    }

    public function specializationLabel(): string
    {
        $brands = collect($this->specialization_brands ?? [])
            ->filter(fn ($brand) => is_string($brand) && trim($brand) !== '')
            ->values()
            ->all();

        return $brands === [] ? 'All brands' : implode(', ', $brands);
    }

    public function image(): string
    {
        return $this->image_url ?: 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?auto=format&fit=crop&w=1200&q=80';
    }

    public function licenseUrl(): ?string
    {
        return $this->license_path ? '/storage/'.ltrim($this->license_path, '/') : null;
    }

    public function isPubliclyVisible(): bool
    {
        return $this->user_id === null || (bool) $this->public_verified_at;
    }

    public function isVerified(): bool
    {
        return $this->user_id === null || (bool) $this->public_verified_at;
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->where(function (Builder $inner): void {
            $inner->whereNull('user_id')
                ->orWhere(function (Builder $accountGarage): void {
                    $accountGarage
                        ->whereNotNull('license_path')
                        ->whereNotNull('public_verified_at')
                        ->whereHas('user', fn (Builder $user) => $user->whereNotNull('email_verified_at'));
                });
        });
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(GarageReview::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
