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

    /**
     * List all checkout sessions
     *
     * @param array $params Optional query parameters (limit, starting_after, ending_before)
     * @return array
     * @throws Exception
     */
    public function listSessions(array $params = []): array
    {
        $response = $this->zipService->makeRequest('GET', '/sessions', $params);
        
        $sessions = [];
        foreach ($response['data'] ?? [] as $sessionData) {
            $sessions[] = new CheckoutSessionResponse($sessionData);
        }
        
        return [
            'data' => $sessions,
            'has_more' => $response['has_more'] ?? false,
            'total_count' => $response['total_count'] ?? count($sessions),
        ];
    }

    /**
     * Capture a checkout session
     *
     * @param string $sessionId
     * @param array $data Optional data for capture (amount)
     * @return CheckoutSessionResponse
     * @throws Exception
     */
    public function captureSession(string $sessionId, array $data = []): CheckoutSessionResponse
    {
        $response = $this->zipService->makeRequest('POST', "/sessions/{$sessionId}/capture", $data);
        
        return new CheckoutSessionResponse($response);
    }
}
