<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use App\Services\PayPalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JsonException;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentController extends Controller
{
    protected $paypal;

    public function __construct(PayPalClient $paypal)
    {
        $this->paypal = $paypal;
    }
    
    public function paypal(Request $request)
    {
        $this->paypal->setApiCredentials(config('paypal'));
        $this->paypal->setAccessToken($this->paypal->getAccessToken());

        $amount = $request->input('amount', 100); // default 100
        $currency = $request->input('currency_code', 'USD'); // default USD

        $response = $this->paypal->createOrder([
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => $amount,
                    ],
                ],
            ],
        ]);

        // save to database
        $payment = Payments::create([
            'order_id' => $response['id'] ?? null,
            'status' => $response['status'] ?? 'CREATED',
            'amount' => $amount,
            'currency' => $currency,
            'payment_method' => 'paypal',
            'description' => $request->input('description', 'PayPal payment'),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $response,
            'payment_id' => $payment->id,
        ]);
    }

}
