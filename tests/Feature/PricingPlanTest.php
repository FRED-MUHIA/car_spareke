<?php

namespace Tests\Feature;

use App\Models\PlanPayment;
use App\Models\PricingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_starter_package_is_shown_on_pricing_page(): void
    {
        $this->get(route('pricing'))
            ->assertOk()
            ->assertSee('Starter Package')
            ->assertSee('KES 500')
            ->assertSee('15 active listings')
            ->assertSee(route('plans.select', PricingPlan::where('slug', 'starter-package')->first()), false);
    }

    public function test_user_selects_paid_plan_and_sees_account_payment_reference(): void
    {
        $user = User::factory()->create([
            'role' => 'seller',
            'phone' => '07588088713',
            'location' => 'Nairobi',
        ]);
        $plan = PricingPlan::where('slug', 'starter-package')->firstOrFail();

        $this->actingAs($user)
            ->get(route('plans.select', $plan))
            ->assertRedirect(route('plans.pay', $plan));

        $this->assertSame($plan->id, $user->fresh()->pricing_plan_id);

        $this->actingAs($user)
            ->get(route('plans.pay', $plan))
            ->assertOk()
            ->assertSee($user->fresh()->account_code)
            ->assertSee('Pay Now with M-Pesa STK')
            ->assertSee('KES 500');
    }

    public function test_stk_payment_records_account_code_when_mpesa_is_not_configured(): void
    {
        config([
            'services.mpesa.consumer_key' => null,
            'services.mpesa.consumer_secret' => null,
            'services.mpesa.shortcode' => null,
            'services.mpesa.passkey' => null,
        ]);

        $user = User::factory()->create([
            'role' => 'seller',
            'phone' => '07588088713',
            'location' => 'Nairobi',
        ]);
        $plan = PricingPlan::where('slug', 'starter-package')->firstOrFail();

        $this->actingAs($user)
            ->post(route('plans.stk', $plan), ['phone' => '07588088713'])
            ->assertSessionHas('status', 'M-Pesa STK is not configured. Add Daraja credentials in the .env file.');

        $payment = PlanPayment::firstOrFail();

        $this->assertSame($user->account_code, $payment->account_code);
        $this->assertSame('2547588088713', $payment->phone);
        $this->assertSame('pending_configuration', $payment->status);
    }

    public function test_dashboard_shows_pay_button_until_plan_is_paid(): void
    {
        $plan = PricingPlan::where('slug', 'starter-package')->firstOrFail();
        $user = User::factory()->create([
            'role' => 'seller',
            'phone' => '07588088713',
            'location' => 'Nairobi',
            'pricing_plan_id' => $plan->id,
        ]);

        $this->actingAs($user)
            ->get(route('seller.dashboard'))
            ->assertOk()
            ->assertSee('Pay Now')
            ->assertDontSee('Tokened');

        PlanPayment::create([
            'user_id' => $user->id,
            'pricing_plan_id' => $plan->id,
            'account_code' => $user->account_code,
            'amount' => $plan->price,
            'phone' => '2547588088713',
            'status' => 'paid',
        ]);

        $this->actingAs($user)
            ->get(route('seller.dashboard'))
            ->assertOk()
            ->assertSee('Tokened')
            ->assertDontSee('Pay Now');
    }

    public function test_paid_plan_payment_page_hides_stk_button(): void
    {
        $plan = PricingPlan::where('slug', 'starter-package')->firstOrFail();
        $user = User::factory()->create([
            'role' => 'seller',
            'phone' => '07588088713',
            'location' => 'Nairobi',
            'pricing_plan_id' => $plan->id,
        ]);

        PlanPayment::create([
            'user_id' => $user->id,
            'pricing_plan_id' => $plan->id,
            'account_code' => $user->account_code,
            'amount' => $plan->price,
            'phone' => '2547588088713',
            'status' => 'paid',
        ]);

        $this->actingAs($user)
            ->get(route('plans.pay', $plan))
            ->assertOk()
            ->assertSee('Tokened')
            ->assertDontSee('Pay Now with M-Pesa STK');
    }

    public function test_mpesa_callback_uses_account_code_to_activate_paid_plan(): void
    {
        $plan = PricingPlan::where('slug', 'starter-package')->firstOrFail();
        $user = User::factory()->create([
            'role' => 'seller',
            'phone' => '07588088713',
            'location' => 'Nairobi',
        ]);

        $payment = PlanPayment::create([
            'user_id' => $user->id,
            'pricing_plan_id' => $plan->id,
            'account_code' => $user->account_code,
            'amount' => $plan->price,
            'phone' => '2547588088713',
            'status' => 'stk_sent',
            'checkout_request_id' => 'ws_CO_12345',
        ]);

        $this->postJson(route('mpesa.callback'), [
            'Body' => [
                'stkCallback' => [
                    'CheckoutRequestID' => 'ws_CO_12345',
                    'ResultCode' => 0,
                    'ResultDesc' => 'The service request is processed successfully.',
                    'CallbackMetadata' => [
                        'Item' => [
                            ['Name' => 'Amount', 'Value' => 2500],
                            ['Name' => 'AccountReference', 'Value' => $user->account_code],
                        ],
                    ],
                ],
            ],
        ])->assertOk();

        $this->assertSame('paid', $payment->fresh()->status);
        $this->assertSame($plan->id, $user->fresh()->pricing_plan_id);

        $this->actingAs($user->fresh())
            ->get(route('seller.dashboard'))
            ->assertSee('Starter Package')
            ->assertSee('Tokened')
            ->assertDontSee('Pay Now');
    }
}
