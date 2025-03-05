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
     * Retrieve a customer by email from Zip
     *
     * @param string $email
     * @return array
     * @throws Exception
     */
    public function getCustomerByEmail(string $email): array
    {
        $response = $this->makeRequest('GET', "/customers/by_email/{$email}");
        
        return $response;
    }
    
    /**
     * Update a customer in Zip
     *
     * @param string $customerId
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function updateCustomer(string $customerId, array $data): array
    {
        $response = $this->makeRequest('PUT', "/customers/{$customerId}", $data);
        
        return $response;
    }

    /**
     * Create a payment source in Zip
     *
     * @param array $data
     * @param bool $usePublicKey Whether to use the public key for authentication (default: true)
     * @return array
     * @throws Exception
     */
    public function createSource(array $data, bool $usePublicKey = true): array
    {
        // Log the input parameters for debugging
        \Illuminate\Support\Facades\Log::info('ZipService::createSource called', [
            'data' => $data,
            'usePublicKey' => $usePublicKey,
            'publicKey' => $this->publicKey,
            'secretKey' => substr($this->secretKey, 0, 5) . '...',
        ]);
        
        $validator = new SourceValidator();
        $validatedData = $validator->validate($data);
        
        $sourceData = new SourceData($validatedData);
        
        \Illuminate\Support\Facades\Log::info('ZipService::createSource validated data', [
            'sourceData' => $sourceData->toArray(),
        ]);
        
        if ($usePublicKey) {
            \Illuminate\Support\Facades\Log::info('ZipService::createSource using public key');
            $response = $this->makePublicKeyRequest('POST', '/sources', $sourceData->toArray());
        } else {
            \Illuminate\Support\Facades\Log::info('ZipService::createSource using secret key');
            $response = $this->makeRequest('POST', '/sources', $sourceData->toArray());
        }
        
        \Illuminate\Support\Facades\Log::info('ZipService::createSource response', [
            'response' => $response,
        ]);
        
        return $response;
    }
    
    /**
     * Create a card payment source in Zip
     *
     * @param string $type Payment source type (e.g., 'card')
     * @param array $cardDetails Card details including name, number, exp_month, exp_year, cvc, etc.
     * @param array $redirectUrls Required redirect URLs for success, fail, and notify
     * @param array $ownerDetails Optional owner details including billing and shipping information
     * @param string|null $customerId Optional customer ID to attach the source to
     * @param array $metadata Optional metadata
     * @return array
     * @throws Exception
     */
    public function createCardSource(
        string $type,
        array $cardDetails,
        array $redirectUrls,
        array $ownerDetails = [],
        ?string $customerId = null,
        array $metadata = []
    ): array {
        // Validate that type is 'card'
        if ($type !== 'card') {
            throw new Exception('Type must be "card" when using createCardSource method');
        }
        
        // Validate that redirectUrls contains required fields
        if (!isset($redirectUrls['success']) || !isset($redirectUrls['fail'])) {
            throw new Exception('Redirect URLs must include "success" and "fail" URLs');
        }
        
        $data = [
            'type' => $type,
            'card' => $cardDetails,
            'redirect' => $redirectUrls
        ];
        
        if (!empty($ownerDetails)) {
            $data['owner'] = $ownerDetails;
        }
        
        if ($customerId) {
            $data['customer_id'] = $customerId;
        }
        
        if (!empty($metadata)) {
            $data['metadata'] = $metadata;
        }
        
        return $this->createSource($data, true); // Always use public key for redirect sources
    }
    
    /**
     * Create a bank account payment source in Zip
     *
     * @param string $type Payment source type (e.g., 'bpi', 'unionbank', etc.)
     * @param array $bankAccountDetails Bank account details
     * @param array $ownerDetails Optional owner details including billing and shipping information
     * @param array $redirectUrls Optional redirect URLs for success, fail, and notify
     * @param string|null $customerId Optional customer ID to attach the source to
     * @param array $metadata Optional metadata
     * @return array
     * @throws Exception
     */
    public function createBankAccountSource(
        string $type,
        array $bankAccountDetails,
        array $ownerDetails = [],
        array $redirectUrls = [],
        ?string $customerId = null,
        array $metadata = []
    ): array {
        $data = [
            'type' => $type,
            'bank_account' => $bankAccountDetails
        ];
        
        if (!empty($ownerDetails)) {
            $data['owner'] = $ownerDetails;
        }
        
        if (!empty($redirectUrls)) {
            $data['redirect'] = $redirectUrls;
        }
        
        if ($customerId) {
            $data['customer_id'] = $customerId;
        }
        
        if (!empty($metadata)) {
            $data['metadata'] = $metadata;
        }
        
        return $this->createSource($data, true); // Always use public key for bank account sources
    }
    
    /**
     * Create a token-based payment source in Zip
     *
     * @param string $type Payment source type
     * @param string $token Token representing the payment source
     * @param string|null $customerId Optional customer ID to attach the source to
     * @param array $metadata Optional metadata
     * @return array
     * @throws Exception
     */
    public function createTokenSource(
        string $type,
        string $token,
        ?string $customerId = null,
        array $metadata = []
    ): array {
        $data = [
            'type' => $type,
            'token' => $token
        ];
        
        if ($customerId) {
            $data['customer_id'] = $customerId;
        }
        
        if (!empty($metadata)) {
            $data['metadata'] = $metadata;
        }
        
        return $this->createSource($data, true); // Always use public key for redirect sources
    }
    
    /**
     * Create a redirect-only payment source in Zip (for types like gcash, paymaya, etc.)
     *
     * @param string $type Payment source type (e.g., 'gcash', 'paymaya', 'wechat', etc.)
     * @param array $redirectUrls Required redirect URLs for success, fail, and notify
     * @param array $ownerDetails Optional owner details including billing and shipping information
     * @param string|null $customerId Optional customer ID to attach the source to
     * @param array $metadata Optional metadata
     * @return array
     * @throws Exception
     */
    public function createRedirectSource(
        string $type,
        array $redirectUrls,
        array $ownerDetails = [],
        ?string $customerId = null,
        array $metadata = []
    ): array {
        // Validate that redirectUrls contains required fields
        if (!isset($redirectUrls['success']) || !isset($redirectUrls['fail'])) {
            throw new Exception('Redirect URLs must include "success" and "fail" URLs');
        }
        
        // Validate that type is one of the redirect-only types
        $redirectOnlyTypes = ['gcash', 'paymaya', 'wechat', 'alipay', 'unionpay', 'grabpay', 'instapay', 'qrph', 'bpi'];
        if (!in_array($type, $redirectOnlyTypes)) {
            throw new Exception('Type must be one of the redirect-only payment types when using createRedirectSource method');
        }
        
        $data = [
            'type' => $type,
            'redirect' => $redirectUrls
        ];
        
        if (!empty($ownerDetails)) {
            $data['owner'] = $ownerDetails;
        }
        
        if ($customerId) {
            $data['customer_id'] = $customerId;
        }
        
        if (!empty($metadata)) {
            $data['metadata'] = $metadata;
        }
        
        return $this->createSource($data, true); // Always use public key for redirect sources
    }

    /**
     * Retrieve a specific source from Zip
     *
     * This method retrieves an existing source object from the Zip API using the source ID.
     * The response includes details about the source such as its type, status, and payment details.
     * 
     * Note: This method uses the public key for authentication, as required by the Zip API.
     *
     * The returned SourceResponseData object contains the following properties:
     * - object: String representing the object's type
     * - id: A unique ID for this source (starts with 'src_')
     * - type: The source type (card, gcash, paymaya, wechat, alipay, unionpay, grabpay, instapay, qrph, bpi, etc.)
     * - card: Card details (if type is 'card')
     * - bank_account: Bank account details (if applicable)
     * - redirect: Redirect URLs (if applicable)
     * - owner: Owner details (if provided)
     * - vaulted: Whether this source has been securely saved for later reuse
     * - used: Whether this source has already been used
     * - created_at: Time at which the object was created (ISO 8601 format)
     * - updated_at: Time at which the object was updated (ISO 8601 format)
     * - metadata: Additional metadata (if any)
     *
     * @param string $sourceId The unique identifier of the source to retrieve (starts with 'src_')
     * @return \Domdanao\ZipSdkLaravel\DTOs\SourceResponseData
     * @throws Exception If the source doesn't exist or there's an error with the API request
     */
    public function getSource(string $sourceId): \Domdanao\ZipSdkLaravel\DTOs\SourceResponseData
    {
        try {
            // Validate the source ID format
            if (!preg_match('/^src_[a-zA-Z0-9]+$/', $sourceId)) {
                throw new Exception('Invalid source ID format. Source ID should start with "src_" followed by alphanumeric characters.');
            }
            
            // Use public key for authentication as required by the Zip API
            $response = $this->makePublicKeyRequest('GET', "/sources/{$sourceId}");
            
            return new \Domdanao\ZipSdkLaravel\DTOs\SourceResponseData($response);
        } catch (Exception $e) {
            // Check if this is a 404 error (source not found)
            if (strpos($e->getMessage(), '404') !== false) {
                throw new Exception("Source with ID {$sourceId} not found.", 404, $e);
            }
            
            // Re-throw the original exception with more context
            throw new Exception("Error retrieving source with ID {$sourceId}: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Attach a source to a customer in Zip
     * 
     * Note: Only card sources can be attached to customers.
     *
     * @param string $customerId The ID of the customer to attach the source to
     * @param string $sourceId The ID of the source to attach (must be a card source)
     * @return array The attached source data
     * @throws Exception If the source is not a card source or if there's an error with the API request
     */
    public function attachSource(string $customerId, string $sourceId): array
    {
        // Validate that the source is a card source
        try {
            $source = $this->getSource($sourceId);
            
            if ($source->getType() !== 'card') {
                throw new Exception('Only card sources can be attached to customers. The provided source is of type: ' . $source->getType());
            }
            
            $response = $this->makeRequest('POST', "/customers/{$customerId}/sources", [
                'source' => $sourceId
            ]);
            
            return $response;
        } catch (Exception $e) {
            // If the source doesn't exist, getSource will throw an exception
            if (strpos($e->getMessage(), 'not found') !== false) {
                throw new Exception("Source with ID {$sourceId} not found or is not accessible.", 404, $e);
            }
            
            // Re-throw any other exceptions
            throw $e;
        }
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
     * Make an HTTP request to the Zip API using the secret key
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
        
        \Illuminate\Support\Facades\Log::info('ZipService::makeRequest', [
            'method' => $method,
            'url' => $url,
            'data' => $data,
            'auth' => 'secret_key',
        ]);
        
        $response = Http::withBasicAuth($this->secretKey, '')
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->{strtolower($method)}($url, $data);
        
        \Illuminate\Support\Facades\Log::info('ZipService::makeRequest response', [
            'status' => $response->status(),
            'successful' => $response->successful(),
            'body' => $response->body(),
        ]);
        
        return $this->handleResponse($response);
    }
    
    /**
     * Make an HTTP request to the Zip API using the public key
     * This is used for operations that require the public key, such as creating and retrieving sources
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function makePublicKeyRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->apiServer . '/' . $this->version . $endpoint;
        
        \Illuminate\Support\Facades\Log::info('ZipService::makePublicKeyRequest', [
            'method' => $method,
            'url' => $url,
            'data' => $data,
            'auth' => 'public_key',
            'publicKey' => $this->publicKey,
        ]);
        
        $response = Http::withBasicAuth($this->publicKey, '')
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->{strtolower($method)}($url, $data);
        
        \Illuminate\Support\Facades\Log::info('ZipService::makePublicKeyRequest response', [
            'status' => $response->status(),
            'successful' => $response->successful(),
            'body' => $response->body(),
        ]);
        
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
        
        // Log the full response for debugging
        \Illuminate\Support\Facades\Log::error('ZipService::handleResponse error', [
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json(),
            'headers' => $response->headers(),
        ]);
        
        $errorMessage = $response->json('message') ?? 'Unknown error';
        $errorCode = $response->json('code') ?? $response->status();
        
        // Try to extract more detailed error information
        $errorDetails = '';
        if ($response->json('error')) {
            if (is_string($response->json('error'))) {
                $errorMessage = $response->json('error');
            } elseif (is_array($response->json('error'))) {
                $errorDetails = json_encode($response->json('error'), JSON_PRETTY_PRINT);
                $errorMessage = $errorDetails;
            }
        }
        
        // Check for 'detail' field which is used in some API error responses
        if ($response->json('detail')) {
            $errorMessage = $response->json('detail');
        }
        
        throw new Exception("Zip API Error ({$errorCode}): {$errorMessage}");
    }
}
