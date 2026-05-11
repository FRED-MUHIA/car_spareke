<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'logo_path',
        'favicon_path',
        'footer_heading',
        'footer_description',
        'home_content',
        'maintenance_mode',
    ];

    protected $casts = [
        'home_content' => 'array',
        'maintenance_mode' => 'boolean',
    ];

    public static function current(): self
    {
        if (! Schema::hasTable('site_settings')) {
            return new self([
                'site_name' => 'Spare Parts Marketplace',
                'footer_heading' => 'Spare Parts Marketplace',
                'footer_description' => 'A scalable automotive marketplace for dealers, shops, garages, mechanics, and individual sellers to publish listings and receive buyer inquiries.',
                'maintenance_mode' => false,
            ]);
        }

        return self::query()->firstOrCreate([], [
            'site_name' => 'Spare Parts Marketplace',
            'footer_heading' => 'Spare Parts Marketplace',
            'footer_description' => 'A scalable automotive marketplace for dealers, shops, garages, mechanics, and individual sellers to publish listings and receive buyer inquiries.',
            'maintenance_mode' => false,
        ]);
    }

    public function homepageContent(): array
    {
        return array_replace_recursive($this->homepageDefaults(), $this->home_content ?? []);
    }

    public static function homepageDefaults(): array
    {
        return [
            'hero_badge' => 'Automotive eCommerce marketplace',
            'hero_title' => 'Find genuine car spare parts from trusted sellers.',
            'hero_subtitle' => 'Search by problem, part name, car model, year, price, and seller location.',
            'part_prompt' => 'What part are you looking for?',
            'part_placeholder' => 'e.g. Brake pads, radiator, alternator...',
            'search_note' => "Know what part you need? Great. We'll show options that match your car.",
            'common_issues' => ['Brakes squeaking', 'Engine light on', 'AC not working', 'Battery dying', 'Suspension noise'],
            'trust_eyebrow' => 'Why people trust us',
            'trust_title' => 'Built for safer spare parts buying',
            'trust_cards' => [
                ['title' => 'Verified sellers', 'description' => 'Sellers create accounts and wait for admin approval before listing parts.'],
                ['title' => 'Direct contact', 'description' => 'Buyers can call or WhatsApp shops and dealers directly before purchasing.'],
                ['title' => 'Car-fit search', 'description' => 'Search by make, model, year, part type, condition, price, and location.'],
                ['title' => 'Local shops & garages', 'description' => 'Find nearby parts shops and garages with contact details and services.'],
            ],
            'categories_eyebrow' => 'Shop by category',
            'categories_title' => 'Popular part categories',
            'trending_eyebrow' => 'Fresh inventory',
            'trending_title' => 'Trending Spare Parts',
            'shops_title' => 'Featured shops',
            'garages_title' => 'Featured garages',
            'cta_title' => 'Sell Your Spare Parts Today',
            'cta_text' => 'Create a listing, add photos, publish your ad, and receive buyer inquiries.',
            'cta_button' => 'Start Selling',
        ];
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? '/storage/'.ltrim($this->logo_path, '/') : null;
    }

    public function getFaviconUrlAttribute(): ?string
    {
        return $this->favicon_path ? '/storage/'.ltrim($this->favicon_path, '/') : null;
    }
}
