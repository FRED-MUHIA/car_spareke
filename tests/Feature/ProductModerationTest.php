<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProductModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_make_product_inactive_and_active_again(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'phone' => '+254700000001',
            'location' => 'Nairobi',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $seller = User::create([
            'name' => 'Seller',
            'email' => 'seller@example.com',
            'phone' => '+254700000002',
            'location' => 'Nairobi',
            'role' => 'seller',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $category = Category::create([
            'name' => 'Brake System',
            'slug' => 'brake-system',
        ]);

        $product = Product::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Brake Pads',
            'slug' => 'brake-pads',
            'part_type' => 'braking',
            'year_from' => 2021,
            'year_to' => 2021,
            'condition' => 'New',
            'price' => 5600,
            'location' => 'Nairobi',
            'images' => ['/storage/parts/main.webp'],
            'description' => 'Premium brake pads',
            'seller_name' => 'Seller',
            'seller_phone' => '+254700000002',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.products.status', $product), [
                'status' => 'inactive',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertSame('inactive', $product->fresh()->status);

        $this->post(route('logout'));

        $this->get(route('parts.show', $product))->assertNotFound();

        $this->actingAs($admin)
            ->get(route('parts.show', $product))
            ->assertOk();

        $this->actingAs($admin)
            ->patch(route('admin.products.status', $product), [
                'status' => 'active',
            ])
            ->assertRedirect();

        $this->assertSame('active', $product->fresh()->status);
    }
}
