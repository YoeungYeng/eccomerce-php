<?php

namespace App\Http\Controllers;


use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;
class PaymentController extends Controller
{
    //
    protected $payPalService;

    public function createTransaction(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $provider->setAccessToken($paypalToken);

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => "10.00"
                    ]
                ]
            ],
            "application_context" => [
                "return_url" => route('paypal.success'),
                "cancel_url" => route('paypal.cancel'),
            ]
        ]);

        return response()->json($response);
    }

    public function captureTransaction(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->setAccessToken($provider->getAccessToken());

        $response = $provider->capturePaymentOrder($request->input('token'));

        return response()->json($response);
    }
}
