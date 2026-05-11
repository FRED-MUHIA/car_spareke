<?php

namespace Tests\Feature;

use App\Mail\SellerPendingApprovalMail;
use App\Models\Garage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GarageAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_garage_account_and_create_profile_after_approval(): void
    {
        Mail::fake();

        $this->post(route('register.store'), [
            'name' => 'Coastline Auto Clinic',
            'email' => 'garage@example.com',
            'phone' => '+254722100200',
            'location' => 'Mombasa',
            'role' => 'garage',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $garageUser = User::where('email', 'garage@example.com')->firstOrFail();

        $this->assertSame('garage', $garageUser->role);
        $this->assertNull($garageUser->email_verified_at);
        Mail::assertSent(SellerPendingApprovalMail::class);

        $garageUser->update(['email_verified_at' => now()]);

        $this->post(route('login.store'), [
            'email' => 'garage@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('garage.dashboard'));

        $this->actingAs($garageUser)
            ->put(route('garage.profile.update'), [
                'name' => 'Coastline Auto Clinic',
                'location' => 'Mombasa',
                'phone' => '+254722100200',
                'whatsapp' => '+254722100200',
                'image_url' => 'https://example.com/garage.jpg',
                'services' => ['AC Repair', 'Body Work'],
                'specialization_brands' => 'Toyota, Mazda',
                'description' => 'Verified garage with experienced mechanics.',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'Garage profile saved.');

        $garage = Garage::firstOrFail();

        $this->assertSame($garageUser->id, $garage->user_id);
        $this->assertSame(['AC Repair', 'Body Work'], $garage->services);
        $this->assertSame(['Toyota', 'Mazda'], $garage->specialization_brands);
    }

    public function test_garage_account_can_upload_profile_image(): void
    {
        Storage::fake('public');

        $garageUser = User::create([
            'name' => 'Coastline Auto Clinic',
            'email' => 'garage@example.com',
            'phone' => '+254722100200',
            'location' => 'Mombasa',
            'role' => 'garage',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($garageUser)
            ->put(route('garage.profile.update'), [
                'name' => 'Coastline Auto Clinic',
                'location' => 'Mombasa',
                'phone' => '+254722100200',
                'whatsapp' => '+254722100200',
                'image' => UploadedFile::fake()->create('garage.webp', 64, 'image/webp'),
                'services' => ['AC Repair'],
                'specialization_brands' => '',
                'description' => 'Verified garage with experienced mechanics.',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'Garage profile saved.');

        $garage = Garage::firstOrFail();

        $this->assertStringStartsWith('/storage/garages/', $garage->image_url);
        Storage::disk('public')->assertExists(substr($garage->image_url, 9));
    }

    public function test_garage_account_verification_allows_access_but_public_visibility_requires_license(): void
    {
        Storage::fake('public');
        Mail::fake();

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'phone' => '+254700000001',
            'location' => 'Nairobi',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $this->post(route('register.store'), [
            'name' => 'Coastline Auto Clinic',
            'email' => 'garage@example.com',
            'phone' => '+254722100200',
            'location' => 'Mombasa',
            'role' => 'garage',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('login'));

        $garageUser = User::where('email', 'garage@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->patch(route('admin.users.verify', $garageUser))
            ->assertRedirect();

        $this->post(route('login.store'), [
            'email' => 'garage@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('garage.dashboard'));

        $this->actingAs($garageUser)
            ->put(route('garage.profile.update'), [
                'name' => 'Coastline Auto Clinic',
                'location' => 'Mombasa',
                'phone' => '+254722100200',
                'whatsapp' => '+254722100200',
                'services' => ['AC Repair'],
                'specialization_brands' => 'Toyota',
                'description' => 'Verified garage with experienced mechanics.',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'Garage profile saved.');

        $garage = Garage::firstOrFail();

        $this->assertNull($garage->license_path);

        $this->actingAs($garageUser)
            ->get(route('garage.dashboard'))
            ->assertOk()
            ->assertSee('Not verified');

        $this->post(route('logout'));

        $this->get(route('garages.index'))
            ->assertOk()
            ->assertDontSee('Coastline Auto Clinic');

        $this->get(route('garages.show', $garage))->assertNotFound();

        $this->actingAs($garageUser)
            ->put(route('garage.profile.update'), [
                'name' => 'Coastline Auto Clinic',
                'location' => 'Mombasa',
                'phone' => '+254722100200',
                'whatsapp' => '+254722100200',
                'license' => UploadedFile::fake()->create('license.pdf', 64, 'application/pdf'),
                'services' => ['AC Repair'],
                'specialization_brands' => 'Toyota',
                'description' => 'Verified garage with experienced mechanics.',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'Garage profile saved.');

        $garage->refresh();

        $this->assertNotNull($garage->license_path);
        Storage::disk('public')->assertExists($garage->license_path);

        $this->actingAs($garageUser)
            ->get(route('garage.dashboard'))
            ->assertOk()
            ->assertSee('Not verified');

        $this->post(route('logout'));

        $this->get(route('garages.index'))
            ->assertOk()
            ->assertDontSee('Coastline Auto Clinic');

        $this->get(route('garages.show', $garage))->assertNotFound();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Garage licence review')
            ->assertSee('View uploaded licence');

        $this->actingAs($admin)
            ->patch(route('admin.garages.public-verify', $garage))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->get(route('garages.index'))
            ->assertOk()
            ->assertSee('Coastline Auto Clinic')
            ->assertSee('Verified');

        $this->get(route('garages.show', $garage))
            ->assertOk()
            ->assertSee('Verified');
    }

    public function test_admin_can_revoke_garage_public_verification_with_reason(): void
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

        $garageUser = User::create([
            'name' => 'Coastline Auto Clinic',
            'email' => 'garage@example.com',
            'phone' => '+254722100200',
            'location' => 'Mombasa',
            'role' => 'garage',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
        ]);

        $garage = Garage::create([
            'user_id' => $garageUser->id,
            'name' => 'Coastline Auto Clinic',
            'slug' => 'coastline-auto-clinic',
            'location' => 'Mombasa',
            'phone' => '+254722100200',
            'license_path' => 'garage-licenses/license.pdf',
            'public_verified_at' => now(),
            'services' => ['AC Repair'],
            'specialization_brands' => ['Toyota'],
            'description' => 'Verified garage with experienced mechanics.',
        ]);

        $this->get(route('garages.index'))
            ->assertOk()
            ->assertSee('Coastline Auto Clinic');

        $this->actingAs($admin)
            ->patch(route('admin.garages.public-revoke', $garage), [
                'license_rejection_reason' => 'Licence is not genuine',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $garage->refresh();

        $this->assertNull($garage->public_verified_at);
        $this->assertSame('Licence is not genuine', $garage->license_rejection_reason);

        $this->post(route('logout'));

        $this->get(route('garages.index'))
            ->assertOk()
            ->assertDontSee('Coastline Auto Clinic');

        $this->actingAs($garageUser)
            ->get(route('garage.dashboard'))
            ->assertOk()
            ->assertSee('Licence review issue')
            ->assertSee('Licence is not genuine');
    }
}
