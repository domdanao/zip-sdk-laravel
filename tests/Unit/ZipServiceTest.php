<?php

namespace Domdanao\ZipSdkLaravel\Tests\Unit;

use Domdanao\ZipSdkLaravel\Tests\TestCase;
use Domdanao\ZipSdkLaravel\Services\ZipService;
use Illuminate\Support\Facades\Http;

class ZipServiceTest extends TestCase
{
    protected ZipService $zipService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->zipService = new ZipService();
    }

    public function testCreateCustomer()
    {
        Http::fake([
            'https://api.zip.ph/v2/customers' => Http::response([
                'id' => 'cus_123456',
                'email' => 'test@example.com',
                'first_name' => 'John',
                'last_name' => 'Doe',
            ], 200),
        ]);

        $customerData = [
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        $response = $this->zipService->createCustomer($customerData);

        $this->assertEquals('cus_123456', $response['id']);
        $this->assertEquals('test@example.com', $response['email']);
        $this->assertEquals('John', $response['first_name']);
        $this->assertEquals('Doe', $response['last_name']);
    }

    public function testCreateSource()
    {
        Http::fake([
            'https://api.zip.ph/v2/sources' => Http::response([
                'id' => 'src_123456',
                'type' => 'card',
                'customer_id' => 'cus_123456',
            ], 200),
        ]);

        $sourceData = [
            'type' => 'card',
            'customer_id' => 'cus_123456',
            'token' => 'tok_123456',
        ];

        $response = $this->zipService->createSource($sourceData);

        $this->assertEquals('src_123456', $response['id']);
        $this->assertEquals('card', $response['type']);
        $this->assertEquals('cus_123456', $response['customer_id']);
    }

    public function testGetSource()
    {
        Http::fake([
            'https://api.zip.ph/v2/sources/src_123456' => Http::response([
                'object' => 'source',
                'id' => 'src_123456',
                'type' => 'card',
                'card' => [
                    'last4' => '4242',
                    'brand' => 'visa',
                ],
                'owner' => [
                    'billing' => [
                        'name' => 'John Doe',
                        'email' => 'john@example.com',
                    ],
                ],
                'vaulted' => true,
                'used' => false,
                'created_at' => '2023-01-01T00:00:00Z',
                'updated_at' => '2023-01-01T00:00:00Z',
                'metadata' => [
                    'order_id' => '12345',
                ],
            ], 200),
        ]);

        $source = $this->zipService->getSource('src_123456');

        // Test that we get a SourceResponseData object
        $this->assertInstanceOf(\Domdanao\ZipSdkLaravel\DTOs\SourceResponseData::class, $source);
        
        // Test the getters
        $this->assertEquals('source', $source->getObject());
        $this->assertEquals('src_123456', $source->getId());
        $this->assertEquals('card', $source->getType());
        $this->assertEquals(['last4' => '4242', 'brand' => 'visa'], $source->getCard());
        $this->assertEquals(['billing' => ['name' => 'John Doe', 'email' => 'john@example.com']], $source->getOwner());
        $this->assertTrue($source->isVaulted());
        $this->assertFalse($source->isUsed());
        $this->assertEquals('2023-01-01T00:00:00Z', $source->getCreatedAt());
        $this->assertEquals('2023-01-01T00:00:00Z', $source->getUpdatedAt());
        $this->assertEquals(['order_id' => '12345'], $source->getMetadata());
        
        // Test the toArray method
        $sourceArray = $source->toArray();
        $this->assertEquals('source', $sourceArray['object']);
        $this->assertEquals('src_123456', $sourceArray['id']);
        $this->assertEquals('card', $sourceArray['type']);
        $this->assertEquals(['last4' => '4242', 'brand' => 'visa'], $sourceArray['card']);
        $this->assertEquals(['billing' => ['name' => 'John Doe', 'email' => 'john@example.com']], $sourceArray['owner']);
        $this->assertTrue($sourceArray['vaulted']);
        $this->assertFalse($sourceArray['used']);
        $this->assertEquals('2023-01-01T00:00:00Z', $sourceArray['created_at']);
        $this->assertEquals('2023-01-01T00:00:00Z', $sourceArray['updated_at']);
        $this->assertEquals(['order_id' => '12345'], $sourceArray['metadata']);
    }
    
    public function testGetSourceWithInvalidId()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid source ID format');
        
        $this->zipService->getSource('invalid_id');
    }
    
    public function testGetSourceNotFound()
    {
        Http::fake([
            'https://api.zip.ph/v2/sources/src_notfound' => Http::response([
                'error' => 'Source not found',
                'code' => 'resource_not_found',
            ], 404),
        ]);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Source with ID src_notfound not found');
        
        $this->zipService->getSource('src_notfound');
    }

    public function testAttachSource()
    {
        Http::fake([
            'https://api.zip.ph/v2/customers/cus_123456/sources' => Http::response([
                'id' => 'src_789012',
                'type' => 'card',
                'customer_id' => 'cus_123456',
            ], 200),
        ]);

        $response = $this->zipService->attachSource('cus_123456', 'src_789012');

        $this->assertEquals('src_789012', $response['id']);
        $this->assertEquals('card', $response['type']);
        $this->assertEquals('cus_123456', $response['customer_id']);
    }

    public function testDetachSource()
    {
        Http::fake([
            'https://api.zip.ph/v2/customers/cus_123456/sources/src_789012' => Http::response([
                'id' => 'src_789012',
                'deleted' => true,
            ], 200),
        ]);

        $response = $this->zipService->detachSource('cus_123456', 'src_789012');

        $this->assertEquals('src_789012', $response['id']);
        $this->assertTrue($response['deleted']);
    }

    public function testCreateCharge()
    {
        Http::fake([
            'https://api.zip.ph/v2/charges' => Http::response([
                'id' => 'ch_123456',
                'amount' => 10000,
                'currency' => 'PHP',
                'customer_id' => 'cus_123456',
                'source_id' => 'src_123456',
                'status' => 'succeeded',
            ], 200),
        ]);

        $chargeData = [
            'amount' => 10000,
            'currency' => 'PHP',
            'customer_id' => 'cus_123456',
            'source_id' => 'src_123456',
        ];

        $response = $this->zipService->createCharge($chargeData);

        $this->assertEquals('ch_123456', $response['id']);
        $this->assertEquals(10000, $response['amount']);
        $this->assertEquals('PHP', $response['currency']);
        $this->assertEquals('cus_123456', $response['customer_id']);
        $this->assertEquals('src_123456', $response['source_id']);
        $this->assertEquals('succeeded', $response['status']);
    }
}
