<?php

namespace Domdanao\ZipSdkLaravel\Services\PaymentRequestService;

use Domdanao\ZipSdkLaravel\Services\ZipService;
use Domdanao\ZipSdkLaravel\DTOs\PaymentRequestData;
use Domdanao\ZipSdkLaravel\DTOs\PaymentRequestResponse;
use Domdanao\ZipSdkLaravel\Validators\PaymentRequestValidator;
use Exception;

class PaymentRequestService
{
    protected $zipService;

    public function __construct(ZipService $zipService)
    {
        $this->zipService = $zipService;
    }

    /**
     * Create a payment request
     *
     * @param array $data
     * @return PaymentRequestResponse
     * @throws Exception
     */
    public function createRequest(array $data): PaymentRequestResponse
    {
        $validator = new PaymentRequestValidator();
        $validatedData = $validator->validate($data);
        
        $requestData = new PaymentRequestData($validatedData);
        
        $response = $this->zipService->makeRequest('POST', '/requests', $requestData->toArray());
        
        return new PaymentRequestResponse($response);
    }

    /**
     * Retrieve a payment request
     *
     * @param string $requestId
     * @return PaymentRequestResponse
     * @throws Exception
     */
    public function getRequest(string $requestId): PaymentRequestResponse
    {
        $response = $this->zipService->makeRequest('GET', "/requests/{$requestId}");
        
        return new PaymentRequestResponse($response);
    }

    /**
     * List all payment requests
     *
     * @param array $params Optional query parameters (limit, starting_after, ending_before)
     * @return array
     * @throws Exception
     */
    public function listRequests(array $params = []): array
    {
        $response = $this->zipService->makeRequest('GET', '/requests', $params);
        
        $requests = [];
        foreach ($response['data'] ?? [] as $requestData) {
            $requests[] = new PaymentRequestResponse($requestData);
        }
        
        return [
            'data' => $requests,
            'has_more' => $response['has_more'] ?? false,
            'total_count' => $response['total_count'] ?? count($requests),
        ];
    }

    /**
     * Resend a payment request
     *
     * @param string $requestId
     * @return PaymentRequestResponse
     * @throws Exception
     */
    public function resendRequest(string $requestId): PaymentRequestResponse
    {
        $response = $this->zipService->makeRequest('POST', "/requests/{$requestId}/resend");
        
        return new PaymentRequestResponse($response);
    }

    /**
     * Void a payment request
     *
     * @param string $requestId
     * @return PaymentRequestResponse
     * @throws Exception
     */
    public function voidRequest(string $requestId): PaymentRequestResponse
    {
        $response = $this->zipService->makeRequest('POST', "/requests/{$requestId}/void");
        
        return new PaymentRequestResponse($response);
    }
}
