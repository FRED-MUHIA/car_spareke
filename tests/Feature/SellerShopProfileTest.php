<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerShopProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_upload_shop_logo_and_banner_images(): void
    {
        Storage::fake('public');

        $seller = User::create([
            'name' => 'Seller',
            'email' => 'seller@example.com',
            'phone' => '+254700000002',
            'location' => 'Nairobi',
            'role' => 'seller',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($seller)
            ->post(route('seller.shop.store'), [
                'name' => 'Kasongo Parts',
                'location' => 'Nairobi',
                'phone' => '+254700000002',
                'whatsapp' => '+254700000002',
                'email' => 'seller@example.com',
                'description' => 'Quality spare parts',
                'logo_image' => UploadedFile::fake()->create('logo.webp', 64, 'image/webp'),
                'banner_image' => UploadedFile::fake()->create('banner.webp', 128, 'image/webp'),
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $shop = Shop::firstOrFail();

        $this->assertStringStartsWith('/storage/shops/', $shop->logo_path);
        $this->assertStringStartsWith('/storage/shops/', $shop->banner_path);
        Storage::disk('public')->assertExists(substr($shop->logo_path, 9));
        Storage::disk('public')->assertExists(substr($shop->banner_path, 9));
    }
}
