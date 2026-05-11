<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'account_code',
        'role',
        'pricing_plan_id',
        'location',
        'email_verified_at',
        'probation_at',
        'probation_reason',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'probation_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isOnProbation(): bool
    {
        return $this->probation_at !== null;
    }

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if (! $user->account_code) {
                $user->account_code = static::generateAccountCode();
            }
        });
    }

    public static function generateAccountCode(): string
    {
        do {
            $code = 'CSK-'.Str::upper(Str::random(8));
        } while (static::where('account_code', $code)->exists());

        return $code;
    }

    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class);
    }

    public function garage(): HasOne
    {
        return $this->hasOne(Garage::class);
    }

    public function pricingPlan(): BelongsTo
    {
        return $this->belongsTo(PricingPlan::class);
    }

    public function planPayments(): HasMany
    {
        return $this->hasMany(PlanPayment::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class, 'seller_id');
    }
}
