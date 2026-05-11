<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $plans = [
            [
                'name' => 'Free Listing',
                'price' => 0,
                'listing_limit' => 5,
                'features' => ['5 active listings', 'Basic seller profile', 'Direct calls and WhatsApp inquiries'],
                'is_featured' => false,
                'cta_label' => 'Start Free',
            ],
            [
                'name' => 'Starter Package',
                'price' => 500,
                'listing_limit' => 15,
                'features' => ['15 active listings', 'Seller dashboard', 'Direct calls and WhatsApp inquiries', 'Buyer inquiry notifications'],
                'is_featured' => false,
                'cta_label' => 'Choose Starter',
            ],
            [
                'name' => 'Dealer Package',
                'price' => 2500,
                'listing_limit' => 50,
                'features' => ['50 active listings', 'Shop profile', 'Featured products', 'Inquiry dashboard'],
                'is_featured' => true,
                'cta_label' => 'Choose Dealer',
            ],
            [
                'name' => 'Premium Shop Package',
                'price' => 6500,
                'listing_limit' => null,
                'features' => ['Unlimited listings', 'Promoted ads', 'Premium shop placement', 'Priority support'],
                'is_featured' => false,
                'cta_label' => 'Go Premium',
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('pricing_plans')->updateOrInsert(
                ['slug' => Str::slug($plan['name'])],
                [
                    'name' => $plan['name'],
                    'price' => $plan['price'],
                    'billing_period' => 'month',
                    'listing_limit' => $plan['listing_limit'],
                    'features' => json_encode($plan['features']),
                    'is_featured' => $plan['is_featured'],
                    'cta_label' => $plan['cta_label'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('pricing_plans')
            ->whereIn('slug', ['free-listing', 'dealer-package', 'premium-shop-package'])
            ->delete();
    }
};
