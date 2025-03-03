<?php

namespace Domdanao\ZipSdkLaravel\Services\ZipCheckoutService;

use Domdanao\ZipSdkLaravel\Services\ZipService;
use Domdanao\ZipSdkLaravel\DTOs\CheckoutSessionData;
use Domdanao\ZipSdkLaravel\DTOs\CheckoutSessionResponse;
use Domdanao\ZipSdkLaravel\Validators\CheckoutSessionValidator;
use Exception;

class ZipCheckoutService
{
    protected $zipService;

    public function __construct(ZipService $zipService)
    {
        $this->zipService = $zipService;
    }

    /**
     * Create a checkout session
     *
     * @param array $data
     * @return CheckoutSessionResponse
     * @throws Exception
     */
    public function createSession(array $data): CheckoutSessionResponse
    {
        $validator = new CheckoutSessionValidator();
        $validatedData = $validator->validate($data);
        
        $sessionData = new CheckoutSessionData($validatedData);
        
        $response = $this->zipService->makeRequest('POST', '/sessions', $sessionData->toArray());
        
        return new CheckoutSessionResponse($response);
    }

    /**
     * Retrieve a checkout session
     *
     * @param string $sessionId
     * @return CheckoutSessionResponse
     * @throws Exception
     */
    public function getSession(string $sessionId): CheckoutSessionResponse
    {
        $response = $this->zipService->makeRequest('GET', "/sessions/{$sessionId}");
        
        return new CheckoutSessionResponse($response);
    }

    /**
     * Cancel a checkout session
     *
     * @param string $sessionId
     * @return CheckoutSessionResponse
     * @throws Exception
     */
    public function cancelSession(string $sessionId): CheckoutSessionResponse
    {
        $response = $this->zipService->makeRequest('POST', "/sessions/{$sessionId}/cancel");
        
        return new CheckoutSessionResponse($response);
    }

    /**
     * Expire a checkout session
     *
     * @param string $sessionId
     * @return CheckoutSessionResponse
     * @throws Exception
     */
    public function expireSession(string $sessionId): CheckoutSessionResponse
    {
        $response = $this->zipService->makeRequest('POST', "/sessions/{$sessionId}/expire");
        
        return new CheckoutSessionResponse($response);
    }
}
