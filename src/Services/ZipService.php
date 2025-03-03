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
    protected $convenienceFee;

    public function __construct()
    {
        $this->apiServer = config('zip-sdk.api_server');
        $this->publicKey = config('zip-sdk.public_key');
        $this->secretKey = config('zip-sdk.secret_key');
        $this->version = config('zip-sdk.version');
        $this->defaultCurrency = config('zip-sdk.defaults.currency');
        $this->defaultPaymentMethods = config('zip-sdk.defaults.payment_methods');
        $this->defaultLocale = config('zip-sdk.defaults.locale');
        $this->convenienceFee = config('zip-sdk.convenience_fee');
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
     * Make an HTTP request to the Zip API
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->apiServer . '/' . $this->version . $endpoint;
        
        $response = Http::withBasicAuth($this->publicKey, $this->secretKey)
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
