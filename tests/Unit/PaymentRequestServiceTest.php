<?php

namespace Domdanao\ZipSdkLaravel\Tests\Unit;

use Domdanao\ZipSdkLaravel\Services\PaymentRequestService\PaymentRequestService;
use Domdanao\ZipSdkLaravel\Services\ZipService;
use Domdanao\ZipSdkLaravel\Tests\TestCase;
use Exception;
use Mockery;

class PaymentRequestServiceTest extends TestCase
{
    protected $zipService;
    protected $paymentRequestService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->zipService = Mockery::mock(ZipService::class);
        $this->paymentRequestService = new PaymentRequestService($this->zipService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateRequest()
    {
        $requestData = [
            'amount' => 1000,
            'currency' => 'PHP',
            'description' => 'Test payment request',
            'customer_email' => 'customer@example.com',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
        ];

        $responseData = [
            'id' => 'req_123456',
            'object' => 'request',
            'amount' => 1000,
            'currency' => 'PHP',
            'description' => 'Test payment request',
            'customer_email' => 'customer@example.com',
            'status' => 'pending',
            'created_at' => '2025-03-03T12:00:00Z',
            'expires_at' => '2025-03-10T12:00:00Z',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
            'payment_url' => 'https://pay.zip.ph/request/req_123456',
        ];

        $this->zipService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', '/requests', Mockery::type('array'))
            ->andReturn($responseData);

        $response = $this->paymentRequestService->createRequest($requestData);

        $this->assertEquals('req_123456', $response->id);
        $this->assertEquals('pending', $response->status);
        $this->assertEquals(1000, $response->amount);
        $this->assertEquals('PHP', $response->currency);
        $this->assertEquals('https://pay.zip.ph/request/req_123456', $response->paymentUrl);
    }

    public function testGetRequest()
    {
        $requestId = 'req_123456';
        $responseData = [
            'id' => 'req_123456',
            'object' => 'request',
            'amount' => 1000,
            'currency' => 'PHP',
            'description' => 'Test payment request',
            'customer_email' => 'customer@example.com',
            'status' => 'pending',
            'created_at' => '2025-03-03T12:00:00Z',
            'expires_at' => '2025-03-10T12:00:00Z',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
            'payment_url' => 'https://pay.zip.ph/request/req_123456',
        ];

        $this->zipService->shouldReceive('makeRequest')
            ->once()
            ->with('GET', "/requests/{$requestId}")
            ->andReturn($responseData);

        $response = $this->paymentRequestService->getRequest($requestId);

        $this->assertEquals('req_123456', $response->id);
        $this->assertEquals('pending', $response->status);
    }

    public function testListRequests()
    {
        $responseData = [
            'data' => [
                [
                    'id' => 'req_123456',
                    'object' => 'request',
                    'amount' => 1000,
                    'currency' => 'PHP',
                    'description' => 'Test payment request 1',
                    'customer_email' => 'customer1@example.com',
                    'status' => 'pending',
                ],
                [
                    'id' => 'req_789012',
                    'object' => 'request',
                    'amount' => 2000,
                    'currency' => 'PHP',
                    'description' => 'Test payment request 2',
                    'customer_email' => 'customer2@example.com',
                    'status' => 'paid',
                ],
            ],
            'has_more' => false,
            'total_count' => 2,
        ];

        $this->zipService->shouldReceive('makeRequest')
            ->once()
            ->with('GET', '/requests', [])
            ->andReturn($responseData);

        $response = $this->paymentRequestService->listRequests();

        $this->assertCount(2, $response['data']);
        $this->assertEquals('req_123456', $response['data'][0]->id);
        $this->assertEquals('req_789012', $response['data'][1]->id);
        $this->assertEquals('pending', $response['data'][0]->status);
        $this->assertEquals('paid', $response['data'][1]->status);
        $this->assertFalse($response['has_more']);
        $this->assertEquals(2, $response['total_count']);
    }

    public function testResendRequest()
    {
        $requestId = 'req_123456';
        $responseData = [
            'id' => 'req_123456',
            'object' => 'request',
            'amount' => 1000,
            'currency' => 'PHP',
            'description' => 'Test payment request',
            'customer_email' => 'customer@example.com',
            'status' => 'pending',
            'created_at' => '2025-03-03T12:00:00Z',
            'expires_at' => '2025-03-10T12:00:00Z',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
            'payment_url' => 'https://pay.zip.ph/request/req_123456',
        ];

        $this->zipService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', "/requests/{$requestId}/resend")
            ->andReturn($responseData);

        $response = $this->paymentRequestService->resendRequest($requestId);

        $this->assertEquals('req_123456', $response->id);
        $this->assertEquals('pending', $response->status);
    }

    public function testVoidRequest()
    {
        $requestId = 'req_123456';
        $responseData = [
            'id' => 'req_123456',
            'object' => 'request',
            'amount' => 1000,
            'currency' => 'PHP',
            'description' => 'Test payment request',
            'customer_email' => 'customer@example.com',
            'status' => 'canceled',
            'created_at' => '2025-03-03T12:00:00Z',
            'expires_at' => '2025-03-10T12:00:00Z',
            'canceled_at' => '2025-03-04T12:00:00Z',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
            'payment_url' => 'https://pay.zip.ph/request/req_123456',
        ];

        $this->zipService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', "/requests/{$requestId}/void")
            ->andReturn($responseData);

        $response = $this->paymentRequestService->voidRequest($requestId);

        $this->assertEquals('req_123456', $response->id);
        $this->assertEquals('canceled', $response->status);
        $this->assertTrue($response->isCanceled());
    }
}
