<?php

namespace Domdanao\ZipSdkLaravel\Tests\Unit;

use Domdanao\ZipSdkLaravel\Tests\TestCase;
use Domdanao\ZipSdkLaravel\Services\ZipService;
use Domdanao\ZipSdkLaravel\Services\ZipCheckoutService\ZipCheckoutService;
use Illuminate\Support\Facades\Http;

class ZipCheckoutServiceTest extends TestCase
{
    protected ZipCheckoutService $zipCheckoutService;
    protected ZipService $zipService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->zipService = new ZipService();
        $this->zipCheckoutService = new ZipCheckoutService($this->zipService);
    }

    public function testCreateSession()
    {
        Http::fake([
            'api.sandbox.zip.co/v2/checkout/sessions' => Http::response([
                'id' => 'cs_123456',
                'object' => 'checkout.session',
                'status' => 'pending',
                'payment_url' => 'https://checkout.zip.co/cs_123456',
                'currency' => 'PHP',
                'payment_method_types' => ['card', 'gcash', 'maya'],
                'success_url' => 'https://example.com/success',
                'cancel_url' => 'https://example.com/cancel',
                'line_items' => [
                    [
                        'name' => 'Test Product',
                        'amount' => 10000,
                        'currency' => 'PHP',
                        'quantity' => 1,
                    ],
                ],
            ], 200),
        ]);

        $sessionData = [
            'currency' => 'PHP',
            'payment_method_types' => ['card', 'gcash', 'maya'],
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
            'line_items' => [
                [
                    'name' => 'Test Product',
                    'amount' => 10000,
                    'currency' => 'PHP',
                    'quantity' => 1,
                ],
            ],
        ];

        $response = $this->zipCheckoutService->createSession($sessionData);

        $this->assertEquals('cs_123456', $response->id);
        $this->assertEquals('checkout.session', $response->object);
        $this->assertEquals('pending', $response->status);
        $this->assertEquals('https://checkout.zip.co/cs_123456', $response->paymentUrl);
        $this->assertEquals('PHP', $response->currency);
        $this->assertEquals(['card', 'gcash', 'maya'], $response->paymentMethodTypes);
    }

    public function testGetSession()
    {
        Http::fake([
            'api.sandbox.zip.co/v2/checkout/sessions/cs_123456' => Http::response([
                'id' => 'cs_123456',
                'object' => 'checkout.session',
                'status' => 'completed',
                'payment_url' => 'https://checkout.zip.co/cs_123456',
            ], 200),
        ]);

        $response = $this->zipCheckoutService->getSession('cs_123456');

        $this->assertEquals('cs_123456', $response->id);
        $this->assertEquals('checkout.session', $response->object);
        $this->assertEquals('completed', $response->status);
    }

    public function testCancelSession()
    {
        Http::fake([
            'api.sandbox.zip.co/v2/checkout/sessions/cs_123456/cancel' => Http::response([
                'id' => 'cs_123456',
                'object' => 'checkout.session',
                'status' => 'canceled',
            ], 200),
        ]);

        $response = $this->zipCheckoutService->cancelSession('cs_123456');

        $this->assertEquals('cs_123456', $response->id);
        $this->assertEquals('checkout.session', $response->object);
        $this->assertEquals('canceled', $response->status);
    }

    public function testExpireSession()
    {
        Http::fake([
            'api.sandbox.zip.co/v2/checkout/sessions/cs_123456/expire' => Http::response([
                'id' => 'cs_123456',
                'object' => 'checkout.session',
                'status' => 'expired',
            ], 200),
        ]);

        $response = $this->zipCheckoutService->expireSession('cs_123456');

        $this->assertEquals('cs_123456', $response->id);
        $this->assertEquals('checkout.session', $response->object);
        $this->assertEquals('expired', $response->status);
    }
}
