<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SellerProductEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_edit_product_with_nested_empty_image_payload(): void
    {
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
            'images' => [[]],
            'description' => 'Premium brake pads',
            'seller_name' => 'Seller',
            'seller_phone' => '+254700000002',
            'status' => 'active',
        ]);

        $this->actingAs($seller)
            ->get(route('seller.products.edit', $product))
            ->assertOk()
            ->assertSee('Edit spare part listing');
    }

    public function test_seller_can_upload_webp_product_image(): void
    {
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

        $this->actingAs($seller)
            ->post(route('seller.products.store'), [
                'title' => 'Brake Pads',
                'category_id' => $category->id,
                'part_type' => 'braking',
                'year_from' => 2021,
                'year_to' => 2021,
                'condition' => 'New',
                'price' => 5600,
                'location' => 'Nairobi',
                'description' => 'Premium brake pads',
                'images' => [
                    UploadedFile::fake()->create('brake-pads.webp', 64, 'image/webp'),
                ],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertStringEndsWith('.webp', Product::firstOrFail()->image());
    }

    public function test_seller_can_add_part_number_and_buyers_can_filter_by_it(): void
    {
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
            'name' => 'Engine Parts',
            'slug' => 'engine-parts',
        ]);

        $this->actingAs($seller)
            ->post(route('seller.products.store'), [
                'title' => 'Toyota Oil Filter',
                'category_id' => $category->id,
                'part_type' => 'Oil filter',
                'part_number' => '90915-YZZE1',
                'year_from' => 2021,
                'year_to' => 2021,
                'condition' => 'New',
                'price' => 1800,
                'location' => 'Nairobi',
                'description' => 'Genuine Toyota oil filter',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $product = Product::firstOrFail();

        $this->assertSame('90915-YZZE1', $product->part_number);

        $this->get(route('parts.index', ['part_number' => 'YZZE1']))
            ->assertOk()
            ->assertSee('Toyota Oil Filter')
            ->assertSee('90915-YZZE1');

        $this->get(route('parts.index', ['q' => '90915']))
            ->assertOk()
            ->assertSee('Toyota Oil Filter');
    }

    public function test_product_whatsapp_links_use_kenya_country_code(): void
    {
        $category = Category::create([
            'name' => 'Lighting',
            'slug' => 'lighting',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'title' => 'Toyota Prado Complete Headlight',
            'slug' => 'toyota-prado-complete-headlight',
            'part_type' => 'Headlight',
            'year_from' => 2021,
            'year_to' => 2021,
            'condition' => 'New',
            'price' => 22000,
            'location' => 'Nairobi',
            'images' => ['/storage/parts/headlight.webp'],
            'description' => 'Complete headlight assembly',
            'seller_name' => 'Seller',
            'seller_phone' => '07588088713',
            'seller_whatsapp' => '7588088713',
            'status' => 'active',
        ]);

        $this->assertSame('2547588088713', $product->whatsappNumber());

        $this->get(route('parts.show', $product))
            ->assertOk()
            ->assertSee('https://wa.me/2547588088713');
    }

    public function test_seller_can_add_alternative_image_without_removing_existing_image(): void
    {
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

        $this->actingAs($seller)
            ->put(route('seller.products.update', $product), [
                'title' => 'Brake Pads',
                'category_id' => $category->id,
                'part_type' => 'braking',
                'year_from' => 2021,
                'year_to' => 2021,
                'condition' => 'New',
                'price' => 5600,
                'location' => 'Nairobi',
                'description' => 'Premium brake pads',
                'image_url' => '/storage/parts/main.webp',
                'images' => [
                    UploadedFile::fake()->create('alternate.webp', 64, 'image/webp'),
                ],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('seller.dashboard'));

        $images = $product->fresh()->images;

        $this->assertContains('/storage/parts/main.webp', $images);
        $this->assertCount(2, $images);
        $this->assertStringEndsWith('.webp', $images[1]);
    }

    public function test_product_images_are_limited_to_two(): void
    {
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

        $this->actingAs($seller)
            ->post(route('seller.products.store'), [
                'title' => 'Brake Pads',
                'category_id' => $category->id,
                'part_type' => 'braking',
                'year_from' => 2021,
                'year_to' => 2021,
                'condition' => 'New',
                'price' => 5600,
                'location' => 'Nairobi',
                'description' => 'Premium brake pads',
                'images' => [
                    UploadedFile::fake()->create('main.webp', 64, 'image/webp'),
                    UploadedFile::fake()->create('alternate.webp', 64, 'image/webp'),
                ],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertCount(2, Product::firstOrFail()->images);
    }

    public function test_seller_is_prompted_to_upgrade_after_five_active_free_listings(): void
    {
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

        foreach (range(1, 5) as $index) {
            Product::create([
                'user_id' => $seller->id,
                'category_id' => $category->id,
                'title' => "Brake Pads {$index}",
                'slug' => "brake-pads-{$index}",
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
        }

        $this->actingAs($seller)
            ->get(route('seller.dashboard'))
            ->assertOk()
            ->assertSee('Free listing limit reached')
            ->assertSee('Upgrade now');

        $this->actingAs($seller)
            ->get(route('sell'))
            ->assertRedirect(route('pricing'))
            ->assertSessionHas('status', 'Your free listing limit is complete. Upgrade your package to add more spare parts.');
    }
}
