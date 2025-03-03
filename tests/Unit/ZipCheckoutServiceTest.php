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
            'https://api.zip.ph/v2/sessions' => Http::response([
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
            'https://api.zip.ph/v2/sessions/cs_123456' => Http::response([
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
            'https://api.zip.ph/v2/sessions/cs_123456/cancel' => Http::response([
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
            'https://api.zip.ph/v2/sessions/cs_123456/expire' => Http::response([
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

    public function testListSessions()
    {
        Http::fake([
            'https://api.zip.ph/v2/sessions' => Http::response([
                'data' => [
                    [
                        'id' => 'cs_123456',
                        'object' => 'checkout.session',
                        'status' => 'completed',
                    ],
                    [
                        'id' => 'cs_789012',
                        'object' => 'checkout.session',
                        'status' => 'pending',
                    ],
                ],
                'has_more' => false,
                'total_count' => 2,
            ], 200),
        ]);

        $response = $this->zipCheckoutService->listSessions();

        $this->assertCount(2, $response['data']);
        $this->assertEquals('cs_123456', $response['data'][0]->id);
        $this->assertEquals('cs_789012', $response['data'][1]->id);
        $this->assertEquals('completed', $response['data'][0]->status);
        $this->assertEquals('pending', $response['data'][1]->status);
        $this->assertFalse($response['has_more']);
        $this->assertEquals(2, $response['total_count']);
    }

    public function testCaptureSession()
    {
        Http::fake([
            'https://api.zip.ph/v2/sessions/cs_123456/capture' => Http::response([
                'id' => 'cs_123456',
                'object' => 'checkout.session',
                'status' => 'paid',
            ], 200),
        ]);

        $response = $this->zipCheckoutService->captureSession('cs_123456');

        $this->assertEquals('cs_123456', $response->id);
        $this->assertEquals('checkout.session', $response->object);
        $this->assertEquals('paid', $response->status);
    }

    public function testCaptureSessionWithAmount()
    {
        Http::fake([
            'https://api.zip.ph/v2/sessions/cs_123456/capture' => Http::response([
                'id' => 'cs_123456',
                'object' => 'checkout.session',
                'status' => 'paid',
                'amount_captured' => 5000,
            ], 200),
        ]);

        $response = $this->zipCheckoutService->captureSession('cs_123456', ['amount' => 5000]);

        $this->assertEquals('cs_123456', $response->id);
        $this->assertEquals('checkout.session', $response->object);
        $this->assertEquals('paid', $response->status);
        $this->assertEquals(5000, $response->amountCaptured ?? null);
    }
}
