<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'user_id', 'shop_id', 'category_id', 'car_make_id', 'car_model_id',
        'title', 'slug', 'part_type', 'part_number', 'year_from', 'year_to', 'condition',
        'price', 'currency', 'location', 'images', 'description', 'seller_name',
        'seller_phone', 'seller_whatsapp', 'status', 'is_featured', 'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_featured' => 'boolean',
            'sold_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function make(): BelongsTo
    {
        return $this->belongsTo(CarMake::class, 'car_make_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }

    public function image(): string
    {
        return $this->imageList()[0];
    }

    public function imageList(): array
    {
        $images = collect($this->images ?? [])
            ->flatten()
            ->filter(fn ($image) => is_string($image) && trim($image) !== '')
            ->values()
            ->all();

        return $images ?: ['https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?auto=format&fit=crop&w=900&q=80'];
    }

    public function whatsappNumber(): string
    {
        $number = preg_replace('/\D+/', '', $this->seller_whatsapp ?: $this->seller_phone);

        if (str_starts_with($number, '254')) {
            return $number;
        }

        if (str_starts_with($number, '0')) {
            return '254'.substr($number, 1);
        }

        if (str_starts_with($number, '7') || str_starts_with($number, '1')) {
            return '254'.$number;
        }

        return $number;
    }

    public function whatsappUrl(): string
    {
        return 'https://wa.me/'.$this->whatsappNumber();
    }
}
