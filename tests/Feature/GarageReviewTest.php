<?php

namespace Tests\Feature;

use App\Models\Garage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GarageReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_post_garage_review_and_rating_is_updated(): void
    {
        $garage = Garage::create([
            'name' => 'Coastline Auto Clinic',
            'slug' => 'coastline-auto-clinic',
            'location' => 'Mombasa',
            'phone' => '+254722100200',
            'services' => ['AC Repair'],
            'specialization_brands' => ['Toyota', 'Mazda'],
            'rating' => 4.0,
            'review_count' => 0,
            'description' => 'Verified garage.',
        ]);

        $this->post(route('garages.reviews.store', $garage), [
            'reviewer_name' => 'Jane Buyer',
            'rating' => 5,
            'comment' => 'Fast service and honest diagnosis.',
        ])->assertRedirect();

        $garage->refresh();

        $this->assertSame(1, $garage->review_count);
        $this->assertEquals(5.0, (float) $garage->rating);
        $this->assertDatabaseHas('garage_reviews', [
            'garage_id' => $garage->id,
            'reviewer_name' => 'Jane Buyer',
            'rating' => 5,
        ]);
    }

    public function test_garage_cards_show_brand_specialization_or_all_brands(): void
    {
        Garage::create([
            'name' => 'Coastline Auto Clinic',
            'slug' => 'coastline-auto-clinic',
            'location' => 'Mombasa',
            'phone' => '+254722100200',
            'services' => ['AC Repair'],
            'specialization_brands' => ['Toyota', 'Mazda'],
            'rating' => 4.0,
            'review_count' => 0,
            'description' => 'Verified garage.',
        ]);

        Garage::create([
            'name' => 'All Car Garage',
            'slug' => 'all-car-garage',
            'location' => 'Nairobi',
            'phone' => '+254722100201',
            'services' => ['Diagnostics'],
            'specialization_brands' => [],
            'rating' => 4.5,
            'review_count' => 0,
            'description' => 'Works on every brand.',
        ]);

        $this->get(route('garages.index'))
            ->assertOk()
            ->assertSee('Specialization:')
            ->assertSee('Toyota, Mazda')
            ->assertSee('All brands');
    }

    public function test_clicking_garage_opens_detail_page_with_image_and_location(): void
    {
        $garage = Garage::create([
            'name' => 'Coastline Auto Clinic',
            'slug' => 'coastline-auto-clinic',
            'location' => 'Mombasa',
            'phone' => '+254722100200',
            'image_url' => 'https://example.com/garage.jpg',
            'services' => ['AC Repair'],
            'specialization_brands' => ['Toyota'],
            'rating' => 4.0,
            'review_count' => 0,
            'description' => 'Verified garage.',
        ]);

        $this->get(route('garages.index'))
            ->assertOk()
            ->assertSee(route('garages.show', $garage));

        $this->get(route('garages.show', $garage))
            ->assertOk()
            ->assertSee('https://example.com/garage.jpg')
            ->assertSee('Location:')
            ->assertSee('Mombasa')
            ->assertSee('Specialization:')
            ->assertSee('Toyota');
    }

    public function test_featured_garage_cards_on_home_link_to_detail_page(): void
    {
        $garage = Garage::create([
            'name' => 'Torque Masters Garage',
            'slug' => 'torque-masters-garage',
            'location' => 'Industrial Area, Nairobi',
            'phone' => '+254722100200',
            'services' => ['Diagnostics', 'Engine Repair'],
            'specialization_brands' => [],
            'rating' => 4.8,
            'review_count' => 0,
            'description' => 'Verified garage.',
            'is_featured' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('garages.show', $garage));
    }
}
