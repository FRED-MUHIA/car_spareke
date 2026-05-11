<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pricing_plans')->updateOrInsert(
            ['slug' => 'starter-package'],
            [
                'name' => 'Starter Package',
                'price' => 500,
                'billing_period' => 'month',
                'listing_limit' => 15,
                'features' => json_encode([
                    '15 active listings',
                    'Seller dashboard',
                    'Direct calls and WhatsApp inquiries',
                    'Buyer inquiry notifications',
                ]),
                'is_featured' => false,
                'cta_label' => 'Choose Starter',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('pricing_plans')->where('slug', 'starter-package')->delete();
    }
};
