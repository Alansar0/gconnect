<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaymentPointService
{
    public static function createVirtualAccount($user)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.config('services.paymentpoint.secret'),
            'api-key' => config('services.paymentpoint.api_key'),
            'Content-Type' => 'application/json'
        ])->post(config('services.paymentpoint.base_url').'/createVirtualAccount', [
            'email' => $user->email,
            'name' => $user->full_name,
            'phoneNumber' => $user->phone_number,
            'bankCode' => ['20946','20897'],
            'businessId' => config('services.paymentpoint.business_id')
        ]);

        return $response->json();
    }
}
