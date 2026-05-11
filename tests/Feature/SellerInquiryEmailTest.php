<?php

namespace Tests\Feature;

use App\Mail\SellerInquiryMail;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SellerInquiryEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_inquiry_is_emailed_to_seller_from_sales_address(): void
    {
        Mail::fake();

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
            'name' => 'Lighting',
            'slug' => 'lighting',
        ]);

        $product = Product::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Toyota Prado Complete Headlight',
            'slug' => 'toyota-prado-complete-headlight',
            'part_type' => 'Headlight',
            'part_number' => '81145-60A80',
            'year_from' => 2021,
            'year_to' => 2021,
            'condition' => 'New',
            'price' => 22000,
            'location' => 'Nairobi',
            'images' => ['/storage/parts/headlight.webp'],
            'description' => 'Complete headlight assembly',
            'seller_name' => 'Seller',
            'seller_phone' => '+254700000002',
            'status' => 'active',
        ]);

        $this->post(route('parts.inquiry', $product), [
            'customer_name' => 'Fredrick Muhia',
            'customer_phone' => '7588088713',
            'customer_email' => 'fredrickmuhiag@gmail.com',
            'message' => 'I am interested in Toyota Prado Complete Headlight.',
        ])
            ->assertRedirect()
            ->assertSessionHas('status', 'Inquiry sent. The seller has been emailed and can now contact you.');

        Mail::assertSent(SellerInquiryMail::class, function (SellerInquiryMail $mail) {
            return $mail->hasTo('seller@example.com')
                && $mail->hasFrom('sales@carspares.co.ke')
                && $mail->inquiry->customer_name === 'Fredrick Muhia'
                && $mail->inquiry->product?->title === 'Toyota Prado Complete Headlight';
        });
    }
}
