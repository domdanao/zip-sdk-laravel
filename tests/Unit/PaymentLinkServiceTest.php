<?php

namespace Domdanao\ZipSdkLaravel\Tests\Unit;

use Domdanao\ZipSdkLaravel\Services\PaymentLinkService\PaymentLinkService;
use Domdanao\ZipSdkLaravel\Services\ZipService;
use Domdanao\ZipSdkLaravel\Tests\TestCase;
use Exception;
use Mockery;

class PaymentLinkServiceTest extends TestCase
{
    protected $zipService;
    protected $paymentLinkService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->zipService = Mockery::mock(ZipService::class);
        $this->paymentLinkService = new PaymentLinkService($this->zipService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateLink()
    {
        $linkData = [
            'name' => 'Test Product',
            'description' => 'Test payment link',
            'amount' => 1000,
            'currency' => 'PHP',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
        ];

        $responseData = [
            'id' => 'link_123456',
            'object' => 'link',
            'name' => 'Test Product',
            'description' => 'Test payment link',
            'amount' => 1000,
            'currency' => 'PHP',
            'active' => true,
            'url' => 'https://pay.zip.ph/link/link_123456',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
            'created_at' => '2025-03-03T12:00:00Z',
        ];

        $this->zipService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', '/links', Mockery::type('array'))
            ->andReturn($responseData);

        $response = $this->paymentLinkService->createLink($linkData);

        $this->assertEquals('link_123456', $response->id);
        $this->assertEquals('Test Product', $response->name);
        $this->assertEquals(1000, $response->amount);
        $this->assertEquals('PHP', $response->currency);
        $this->assertTrue($response->active);
        $this->assertEquals('https://pay.zip.ph/link/link_123456', $response->url);
    }

    public function testGetLink()
    {
        $linkId = 'link_123456';
        $responseData = [
            'id' => 'link_123456',
            'object' => 'link',
            'name' => 'Test Product',
            'description' => 'Test payment link',
            'amount' => 1000,
            'currency' => 'PHP',
            'active' => true,
            'url' => 'https://pay.zip.ph/link/link_123456',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
            'created_at' => '2025-03-03T12:00:00Z',
        ];

        $this->zipService->shouldReceive('makeRequest')
            ->once()
            ->with('GET', "/links/{$linkId}")
            ->andReturn($responseData);

        $response = $this->paymentLinkService->getLink($linkId);

        $this->assertEquals('link_123456', $response->id);
        $this->assertEquals('Test Product', $response->name);
        $this->assertTrue($response->active);
    }

    public function testListLinks()
    {
        $responseData = [
            'data' => [
                [
                    'id' => 'link_123456',
                    'object' => 'link',
                    'name' => 'Test Product 1',
                    'description' => 'Test payment link 1',
                    'amount' => 1000,
                    'currency' => 'PHP',
                    'active' => true,
                ],
                [
                    'id' => 'link_789012',
                    'object' => 'link',
                    'name' => 'Test Product 2',
                    'description' => 'Test payment link 2',
                    'amount' => 2000,
                    'currency' => 'PHP',
                    'active' => false,
                ],
            ],
            'has_more' => false,
            'total_count' => 2,
        ];

        $this->zipService->shouldReceive('makeRequest')
            ->once()
            ->with('GET', '/links', [])
            ->andReturn($responseData);

        $response = $this->paymentLinkService->listLinks();

        $this->assertCount(2, $response['data']);
        $this->assertEquals('link_123456', $response['data'][0]->id);
        $this->assertEquals('link_789012', $response['data'][1]->id);
        $this->assertTrue($response['data'][0]->active);
        $this->assertFalse($response['data'][1]->active);
        $this->assertFalse($response['has_more']);
        $this->assertEquals(2, $response['total_count']);
    }

    public function testUpdateLink()
    {
        $linkId = 'link_123456';
        $updateData = [
            'name' => 'Updated Product',
            'description' => 'Updated payment link',
            'amount' => 1500,
        ];

        $responseData = [
            'id' => 'link_123456',
            'object' => 'link',
            'name' => 'Updated Product',
            'description' => 'Updated payment link',
            'amount' => 1500,
            'currency' => 'PHP',
            'active' => true,
            'url' => 'https://pay.zip.ph/link/link_123456',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
            'created_at' => '2025-03-03T12:00:00Z',
        ];

        $this->zipService->shouldReceive('makeRequest')
            ->once()
            ->with('PATCH', "/links/{$linkId}", Mockery::type('array'))
            ->andReturn($responseData);

        $response = $this->paymentLinkService->updateLink($linkId, $updateData);

        $this->assertEquals('link_123456', $response->id);
        $this->assertEquals('Updated Product', $response->name);
        $this->assertEquals('Updated payment link', $response->description);
        $this->assertEquals(1500, $response->amount);
    }

    public function testDeactivateLink()
    {
        $linkId = 'link_123456';
        $responseData = [
            'id' => 'link_123456',
            'object' => 'link',
            'name' => 'Test Product',
            'description' => 'Test payment link',
            'amount' => 1000,
            'currency' => 'PHP',
            'active' => false,
            'url' => 'https://pay.zip.ph/link/link_123456',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
            'created_at' => '2025-03-03T12:00:00Z',
        ];

        $this->zipService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', "/links/{$linkId}/deactivate")
            ->andReturn($responseData);

        $response = $this->paymentLinkService->deactivateLink($linkId);

        $this->assertEquals('link_123456', $response->id);
        $this->assertFalse($response->active);
        $this->assertFalse($response->isActive());
    }

    public function testActivateLink()
    {
        $linkId = 'link_123456';
        $responseData = [
            'id' => 'link_123456',
            'object' => 'link',
            'name' => 'Test Product',
            'description' => 'Test payment link',
            'amount' => 1000,
            'currency' => 'PHP',
            'active' => true,
            'url' => 'https://pay.zip.ph/link/link_123456',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
            'created_at' => '2025-03-03T12:00:00Z',
        ];

        $this->zipService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', "/links/{$linkId}/activate")
            ->andReturn($responseData);

        $response = $this->paymentLinkService->activateLink($linkId);

        $this->assertEquals('link_123456', $response->id);
        $this->assertTrue($response->active);
        $this->assertTrue($response->isActive());
    }
}
