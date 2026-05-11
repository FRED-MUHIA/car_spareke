<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_site_settings_and_assets(): void
    {
        Storage::fake('public');

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'phone' => '+254700000001',
            'location' => 'Nairobi',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $homeContent = SiteSetting::homepageDefaults();
        $homeContent['common_issues'] = implode(', ', $homeContent['common_issues']);

        $this->actingAs($admin)
            ->patch(route('admin.site-settings.update'), [
                'site_name' => 'Car Spare KE',
                'footer_heading' => 'Car Spare KE',
                'footer_description' => 'Trusted parts, shops, and garages.',
                'home' => $homeContent,
                'logo' => UploadedFile::fake()->create('logo.png', 10, 'image/png'),
                'favicon' => UploadedFile::fake()->create('favicon.png', 10, 'image/png'),
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $settings = SiteSetting::current();

        $this->assertSame('Car Spare KE', $settings->site_name);
        $this->assertSame('Trusted parts, shops, and garages.', $settings->footer_description);
        $this->assertSame('Find genuine car spare parts from trusted sellers.', $settings->homepageContent()['hero_title']);
        Storage::disk('public')->assertExists($settings->logo_path);
        Storage::disk('public')->assertExists($settings->favicon_path);
    }

    public function test_admin_can_update_category_icons(): void
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

        $category = Category::create([
            'name' => 'Engine Parts',
            'slug' => 'engine-parts',
            'icon' => 'old',
            'description' => 'Engine spares',
            'is_featured' => true,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.category-icons.update'), [
                'categories' => [
                    $category->id => [
                        'icon' => 'gear',
                        'icon_file' => UploadedFile::fake()->create('engine.svg', 5, 'image/svg+xml'),
                    ],
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertSame('gear', $category->fresh()->icon);
        Storage::disk('public')->assertExists($category->fresh()->icon_path);
    }

    public function test_maintenance_mode_blocks_visitors_but_allows_admins(): void
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

        SiteSetting::current()->update(['maintenance_mode' => true]);

        $this->get(route('home'))
            ->assertStatus(503)
            ->assertSee('We are tidying up!');

        $this->get(route('admin.dashboard'))
            ->assertStatus(503)
            ->assertSee('We are tidying up!');

        $this->get(route('login'))->assertOk();

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertOk();

        $this->actingAs($admin)
            ->patch(route('admin.maintenance-mode.update'), [
                'maintenance_mode' => false,
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertFalse(SiteSetting::current()->fresh()->maintenance_mode);
    }
}
