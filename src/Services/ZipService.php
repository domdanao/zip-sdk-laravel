<?php

namespace Domdanao\ZipSdkLaravel\Services;

use Domdanao\ZipSdkLaravel\DTOs\ChargeData;
use Domdanao\ZipSdkLaravel\DTOs\CustomerData;
use Domdanao\ZipSdkLaravel\DTOs\SourceData;
use Domdanao\ZipSdkLaravel\Validators\ChargeValidator;
use Domdanao\ZipSdkLaravel\Validators\CustomerValidator;
use Domdanao\ZipSdkLaravel\Validators\SourceValidator;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ZipService
{
    protected $apiServer;
    protected $publicKey;
    protected $secretKey;
    protected $version;
    protected $defaultCurrency;
    protected $defaultPaymentMethods;
    protected $defaultLocale;

    public function __construct()
    {
        $this->apiServer = \app('config')->get('zip-sdk.api_server');
        $this->publicKey = \app('config')->get('zip-sdk.public_key');
        $this->secretKey = \app('config')->get('zip-sdk.secret_key');
        $this->version = \app('config')->get('zip-sdk.version');
        $this->defaultCurrency = \app('config')->get('zip-sdk.defaults.currency');
        $this->defaultPaymentMethods = \app('config')->get('zip-sdk.defaults.payment_methods');
        $this->defaultLocale = \app('config')->get('zip-sdk.defaults.locale');
    }

    /**
     * Create a customer in Zip
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function createCustomer(array $data): array
    {
        $validator = new CustomerValidator();
        $validatedData = $validator->validate($data);
        
        $customerData = new CustomerData($validatedData);
        
        $response = $this->makeRequest('POST', '/customers', $customerData->toArray());
        
        return $response;
    }

    /**
     * Retrieve a specific customer from Zip
     *
     * @param string $customerId
     * @return array
     * @throws Exception
     */
    public function getCustomer(string $customerId): array
    {
        $response = $this->makeRequest('GET', "/customers/{$customerId}");
        
        return $response;
    }

    /**
     * List all customers from Zip
     *
     * @param array $params Optional query parameters
     * @return array
     * @throws Exception
     */
    public function listCustomers(array $params = []): array
    {
        $response = $this->makeRequest('GET', '/customers', $params);
        
        return $response;
    }

    /**
     * Create a payment source in Zip
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function createSource(array $data): array
    {
        $validator = new SourceValidator();
        $validatedData = $validator->validate($data);
        
        $sourceData = new SourceData($validatedData);
        
        $response = $this->makeRequest('POST', '/sources', $sourceData->toArray());
        
        return $response;
    }

    /**
     * Retrieve a specific source from Zip
     *
     * @param string $sourceId
     * @return array
     * @throws Exception
     */
    public function getSource(string $sourceId): array
    {
        $response = $this->makeRequest('GET', "/sources/{$sourceId}");
        
        return $response;
    }

    /**
     * Attach a source to a customer in Zip
     *
     * @param string $customerId
     * @param string $source
     * @return array
     * @throws Exception
     */
    public function attachSource(string $customerId, string $source): array
    {
        $response = $this->makeRequest('POST', "/customers/{$customerId}/sources", [
            'source' => $source
        ]);
        
        return $response;
    }

    /**
     * Detach a source from a customer in Zip
     *
     * @param string $customerId
     * @param string $sourceId
     * @return array
     * @throws Exception
     */
    public function detachSource(string $customerId, string $sourceId): array
    {
        $response = $this->makeRequest('DELETE', "/customers/{$customerId}/sources/{$sourceId}");
        
        return $response;
    }

    /**
     * Create a charge in Zip
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function createCharge(array $data): array
    {
        $validator = new ChargeValidator();
        $validatedData = $validator->validate($data);
        
        $chargeData = new ChargeData($validatedData);
        
        $response = $this->makeRequest('POST', '/charges', $chargeData->toArray());
        
        return $response;
    }

    /**
     * Retrieve a specific charge from Zip
     *
     * @param string $chargeId
     * @return array
     * @throws Exception
     */
    public function getCharge(string $chargeId): array
    {
        $response = $this->makeRequest('GET', "/charges/{$chargeId}");
        
        return $response;
    }

    /**
     * List all charges from Zip
     *
     * @param array $params Optional query parameters
     * @return array
     * @throws Exception
     */
    public function listCharges(array $params = []): array
    {
        $response = $this->makeRequest('GET', '/charges', $params);
        
        return $response;
    }

    /**
     * Capture a charge in Zip
     *
     * @param string $chargeId
     * @param array $data Optional data for capture
     * @return array
     * @throws Exception
     */
    public function captureCharge(string $chargeId, array $data = []): array
    {
        $response = $this->makeRequest('POST', "/charges/{$chargeId}/capture", $data);
        
        return $response;
    }

    /**
     * Refund a charge in Zip
     *
     * @param string $chargeId
     * @param array $data Optional data for refund
     * @return array
     * @throws Exception
     */
    public function refundCharge(string $chargeId, array $data = []): array
    {
        $response = $this->makeRequest('POST', "/charges/{$chargeId}/refund", $data);
        
        return $response;
    }

    /**
     * Void a charge in Zip
     *
     * @param string $chargeId
     * @param array $data Optional data for voiding
     * @return array
     * @throws Exception
     */
    public function voidCharge(string $chargeId, array $data = []): array
    {
        $response = $this->makeRequest('POST', "/charges/{$chargeId}/void", $data);
        
        return $response;
    }

    /**
     * Verify a charge in Zip
     *
     * @param string $chargeId
     * @param array $data Optional data for verification
     * @return array
     * @throws Exception
     */
    public function verifyCharge(string $chargeId, array $data = []): array
    {
        $response = $this->makeRequest('POST', "/charges/{$chargeId}/verify", $data);
        
        return $response;
    }

    /**
     * Make an HTTP request to the Zip API
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->apiServer . '/' . $this->version . $endpoint;
        
        $response = Http::withBasicAuth($this->secretKey, '')
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->{strtolower($method)}($url, $data);
        
        return $this->handleResponse($response);
    }

    /**
     * Handle the API response
     *
     * @param Response $response
     * @return array
     * @throws Exception
     */
    protected function handleResponse(Response $response): array
    {
        if ($response->successful()) {
            return $response->json();
        }
        
        $errorMessage = $response->json('message') ?? 'Unknown error';
        $errorCode = $response->json('code') ?? $response->status();
        
        throw new Exception("Zip API Error ({$errorCode}): {$errorMessage}");
    }
}
