<?php

namespace App\Services;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;

class PayPalService
{
    protected $client;

    public function __construct()
    {
        $clientId = config('paypal.client_id');
        $clientSecret = config('paypal.secret');

        $env = config('paypal.settings.mode') === 'sandbox'
            ? new SandboxEnvironment($clientId, $clientSecret)
            : new ProductionEnvironment($clientId, $clientSecret);

        $this->client = new PayPalHttpClient($env);
    }

    public function client()
    {
        return $this->client;
    }
}
