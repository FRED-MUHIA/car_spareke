<?php

namespace App\Services;

use App\Models\PlanPayment;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MpesaStkService
{
    public function configured(): bool
    {
        return collect([
            config('services.mpesa.consumer_key'),
            config('services.mpesa.consumer_secret'),
            config('services.mpesa.shortcode'),
            config('services.mpesa.passkey'),
        ])->every(fn ($value) => filled($value));
    }

    public function push(PlanPayment $payment): array
    {
        if (! $this->configured()) {
            throw new RuntimeException('M-Pesa STK is not configured. Add Daraja credentials in the .env file.');
        }

        $timestamp = now()->format('YmdHis');
        $shortcode = config('services.mpesa.shortcode');
        $password = base64_encode($shortcode.config('services.mpesa.passkey').$timestamp);

        $response = Http::withToken($this->accessToken())->post($this->baseUrl().'/mpesa/stkpush/v1/processrequest', [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int) ceil((float) $payment->amount),
            'PartyA' => $payment->phone,
            'PartyB' => $shortcode,
            'PhoneNumber' => $payment->phone,
            'CallBackURL' => config('services.mpesa.callback_url'),
            'AccountReference' => $payment->account_code,
            'TransactionDesc' => 'Car Spares '.$payment->plan->name,
        ]);

        return $response->json() ?? [];
    }

    public function normalizePhone(string $phone): string
    {
        $number = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($number, '254')) {
            return $number;
        }

        if (str_starts_with($number, '0')) {
            return '254'.substr($number, 1);
        }

        if (str_starts_with($number, '7') || str_starts_with($number, '1')) {
            return '254'.$number;
        }

        return $number;
    }

    private function accessToken(): string
    {
        $response = Http::withBasicAuth(
            config('services.mpesa.consumer_key'),
            config('services.mpesa.consumer_secret')
        )->get($this->baseUrl().'/oauth/v1/generate', [
            'grant_type' => 'client_credentials',
        ]);

        return $response->json('access_token') ?? throw new RuntimeException('Unable to get M-Pesa access token.');
    }

    private function baseUrl(): string
    {
        return config('services.mpesa.env') === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }
}
