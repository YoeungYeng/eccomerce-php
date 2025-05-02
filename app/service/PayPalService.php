<?php

namespace App\Services;

use GuzzleHttp\Client;
use JsonException;

class PayPalService
{
    protected $client;
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.secret');
        $this->baseUrl = config('services.paypal.base_url');
    }

    /**
     * Retrieves an access token from PayPal.
     *
     * @return string|null The access token, or null on failure.
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws JsonException
     */
    public function getAccessToken(): ?string
    {
        $response = $this->client->post("{$this->baseUrl}/v1/oauth2/token", [
            'auth' => [$this->clientId, $this->clientSecret],
            'form_params' => ['grant_type' => 'client_credentials'],
        ]);

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        return $data['access_token'] ?? null;
    }

    /**
     * Creates a PayPal order.
     *
     * @param float $amount The amount of the order.
     * @return array|null The decoded JSON response, or null on failure.
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws JsonException
     */
    public function createOrder(float $amount): ?array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return null;
        }

        $response = $this->client->post("{$this->baseUrl}/v2/checkout/orders", [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => $amount,
                    ],
                ]],
                'application_context' => [
                    'return_url' => 'https://your-frontend.com/success',
                    'cancel_url' => 'https://your-frontend.com/cancel',
                ],
            ],
        ]);

        $body = $response->getBody()->getContents();
        return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Captures a PayPal order payment.
     *
     * @param string $orderId The ID of the order to capture.
     * @return array|null The decoded JSON response, or null on failure.
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws JsonException
     */
    public function capturePayment(string $orderId): ?array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return null;
        }

        $response = $this->client->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture", [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ],
        ]);

        $body = $response->getBody()->getContents();
        return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }
}