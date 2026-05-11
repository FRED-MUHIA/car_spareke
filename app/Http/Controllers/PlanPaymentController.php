<?php

namespace App\Http\Controllers;

use App\Models\PlanPayment;
use App\Models\PricingPlan;
use App\Services\MpesaStkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;

class PlanPaymentController extends Controller
{
    public function select(PricingPlan $plan): RedirectResponse
    {
        $user = Auth::user();

        $user->update([
            'pricing_plan_id' => $plan->id,
        ]);

        if ((float) $plan->price <= 0) {
            return redirect()
                ->route($user->role === 'garage' ? 'garage.dashboard' : 'seller.dashboard')
                ->with('status', "{$plan->name} selected for account {$user->account_code}.");
        }

        return redirect()->route('plans.pay', $plan);
    }

    public function pay(PricingPlan $plan): View
    {
        $user = Auth::user();
        $latestPayment = $user->planPayments()
            ->with('plan')
            ->where('pricing_plan_id', $plan->id)
            ->latest()
            ->first();

        return view('billing.plan-payment', [
            'plan' => $plan,
            'user' => $user,
            'latestPayment' => $latestPayment,
            'isTokened' => $latestPayment?->status === 'paid',
        ]);
    }

    public function stk(Request $request, PricingPlan $plan, MpesaStkService $mpesa): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:40'],
        ]);

        $user = Auth::user();
        $user->update(['pricing_plan_id' => $plan->id]);

        if ($user->planPayments()->where('pricing_plan_id', $plan->id)->where('status', 'paid')->exists()) {
            return back()->with('status', 'This package is already Tokened for your account.');
        }

        $payment = PlanPayment::create([
            'user_id' => $user->id,
            'pricing_plan_id' => $plan->id,
            'account_code' => $user->account_code,
            'amount' => $plan->price,
            'phone' => $mpesa->normalizePhone($data['phone']),
            'status' => 'pending',
        ]);

        try {
            $payload = $mpesa->push($payment->load('plan'));

            $payment->update([
                'merchant_request_id' => $payload['MerchantRequestID'] ?? null,
                'checkout_request_id' => $payload['CheckoutRequestID'] ?? null,
                'response_code' => $payload['ResponseCode'] ?? null,
                'response_description' => $payload['ResponseDescription'] ?? ($payload['errorMessage'] ?? null),
                'response_payload' => $payload,
                'status' => (($payload['ResponseCode'] ?? null) === '0') ? 'stk_sent' : 'pending',
            ]);

            return back()->with('status', 'M-Pesa STK prompt sent. Check your phone to complete payment.');
        } catch (RuntimeException $exception) {
            $payment->update([
                'status' => 'pending_configuration',
                'response_description' => $exception->getMessage(),
            ]);

            return back()->with('status', $exception->getMessage());
        }
    }

    public function callback(Request $request): JsonResponse
    {
        $payload = $request->all();
        $checkoutRequestId = data_get($payload, 'Body.stkCallback.CheckoutRequestID');
        $resultCode = data_get($payload, 'Body.stkCallback.ResultCode');
        $resultDescription = data_get($payload, 'Body.stkCallback.ResultDesc');
        $accountCode = $this->accountCodeFromCallback($payload);

        $payment = null;

        if ($checkoutRequestId || $accountCode) {
            $payment = PlanPayment::query()
                ->when($checkoutRequestId, fn ($query) => $query->where('checkout_request_id', $checkoutRequestId))
                ->when($accountCode, fn ($query) => $query->where('account_code', $accountCode))
                ->latest()
                ->first();
        }

        if (! $payment && $accountCode) {
            $payment = PlanPayment::query()
                ->where('account_code', $accountCode)
                ->whereIn('status', ['pending', 'stk_sent'])
                ->latest()
                ->first();
        }

        if ($payment) {
            $payment->update([
                'status' => ((string) $resultCode === '0') ? 'paid' : 'failed',
                'response_code' => (string) $resultCode,
                'response_description' => $resultDescription,
                'response_payload' => $payload,
            ]);

            if ((string) $resultCode === '0') {
                $payment->user?->update([
                    'pricing_plan_id' => $payment->pricing_plan_id,
                ]);
            }
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    private function accountCodeFromCallback(array $payload): ?string
    {
        $directValue = collect([
            data_get($payload, 'AccountReference'),
            data_get($payload, 'BillRefNumber'),
            data_get($payload, 'Body.stkCallback.AccountReference'),
            data_get($payload, 'Body.stkCallback.BillRefNumber'),
        ])->filter()->first();

        if ($directValue) {
            return (string) $directValue;
        }

        $metadataItem = collect(data_get($payload, 'Body.stkCallback.CallbackMetadata.Item', []))
            ->firstWhere('Name', 'AccountReference');

        return is_array($metadataItem) ? ($metadataItem['Value'] ?? null) : null;
    }
}
