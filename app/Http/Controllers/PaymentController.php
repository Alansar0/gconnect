<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VirtualAccount;
use App\Models\Transaction;

class PaymentPointWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $raw = $request->getContent();
        $signature = $request->header('Paymentpoint-Signature');

        $expected = hash_hmac(
            'sha256',
            $raw,
            config('services.paymentpoint.webhook_secret')
        );

        if (!hash_equals($expected, $signature)) {
            return response()->json(['error'=>'Invalid signature'], 400);
        }

        $data = json_decode($raw, true);

        if ($data['transaction_status'] !== 'success') {
            return response()->json(['ignored'=>'not successful']);
        }

        if (Transaction::where('reference', $data['transaction_id'])->exists()) {
            return response()->json(['duplicate'=>true]);
        }

        $virtual = VirtualAccount::where(
            'account_number',
            $data['receiver']['account_number']
        )->firstOrFail();

        $wallet = $virtual->wallet;

        $wallet->credit(
            $data['settlement_amount'],
            $data['transaction_id'],
            'PaymentPoint Bank Transfer'
        );

        return response()->json(['status'=>'credited']);
    }
}
