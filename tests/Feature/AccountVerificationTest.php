<?php

namespace Tests\Feature;

use App\Mail\SellerApprovedMail;
use App\Mail\SellerPendingApprovalMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AccountVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_verify_pending_seller_and_seller_can_login(): void
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
            'name' => 'Pending Seller',
            'email' => 'seller@example.com',
            'phone' => '+254700000002',
            'location' => 'Nairobi',
            'role' => 'seller',
            'password' => Hash::make('password'),
        ]);

        $this->post(route('login.store'), [
            'email' => $seller->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        Mail::fake();

        $this->actingAs($admin)
            ->patch(route('admin.users.verify', $seller))
            ->assertRedirect();

        $this->assertNotNull($seller->fresh()->email_verified_at);
        Mail::assertSent(SellerApprovedMail::class, fn ($mail) => $mail->hasTo($seller->email));

        $this->post(route('login.store'), [
            'email' => $seller->email,
            'password' => 'password',
        ])->assertRedirect(route('seller.dashboard'));
    }

    public function test_pending_seller_receives_email_after_registration(): void
    {
        Mail::fake();

        $this->post(route('register.store'), [
            'name' => 'Pending Seller',
            'email' => 'seller@example.com',
            'phone' => '+254700000002',
            'location' => 'Nairobi',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('login'));

        $seller = User::where('email', 'seller@example.com')->firstOrFail();

        $this->assertNull($seller->email_verified_at);
        Mail::assertSent(SellerPendingApprovalMail::class, fn ($mail) => $mail->hasTo($seller->email));
    }

    public function test_admin_can_put_user_on_probation_and_block_login(): void
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

        $this->actingAs($admin)
            ->patch(route('admin.users.probation', $seller), [
                'probation' => true,
                'probation_reason' => 'Buyer complaint under review',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $seller->refresh();

        $this->assertNotNull($seller->probation_at);
        $this->assertSame('Buyer complaint under review', $seller->probation_reason);

        $this->post(route('login.store'), [
            'email' => 'seller@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->actingAs($admin)
            ->patch(route('admin.users.probation', $seller), [
                'probation' => false,
            ])
            ->assertRedirect();

        $this->assertNull($seller->fresh()->probation_at);
    }

    public function test_admin_can_delete_user_account(): void
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

        $this->actingAs($admin)
            ->delete(route('admin.users.delete', $seller))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('users', [
            'email' => 'seller@example.com',
        ]);
    }
}
