<?php

namespace Domdanao\ZipSdkLaravel\Services\PaymentLinkService;

use Domdanao\ZipSdkLaravel\Services\ZipService;
use Domdanao\ZipSdkLaravel\DTOs\PaymentLinkData;
use Domdanao\ZipSdkLaravel\DTOs\PaymentLinkResponse;
use Domdanao\ZipSdkLaravel\Validators\PaymentLinkValidator;
use Exception;

class PaymentLinkService
{
    protected $zipService;

    public function __construct(ZipService $zipService)
    {
        $this->zipService = $zipService;
    }

    /**
     * Create a payment link
     *
     * @param array $data
     * @return PaymentLinkResponse
     * @throws Exception
     */
    public function createLink(array $data): PaymentLinkResponse
    {
        $validator = new PaymentLinkValidator();
        $validatedData = $validator->validate($data);
        
        $linkData = new PaymentLinkData($validatedData);
        
        $response = $this->zipService->makeRequest('POST', '/links', $linkData->toArray());
        
        return new PaymentLinkResponse($response);
    }

    /**
     * Retrieve a payment link
     *
     * @param string $linkId
     * @return PaymentLinkResponse
     * @throws Exception
     */
    public function getLink(string $linkId): PaymentLinkResponse
    {
        $response = $this->zipService->makeRequest('GET', "/links/{$linkId}");
        
        return new PaymentLinkResponse($response);
    }

    /**
     * List all payment links
     *
     * @param array $params Optional query parameters (limit, starting_after, ending_before, active)
     * @return array
     * @throws Exception
     */
    public function listLinks(array $params = []): array
    {
        $response = $this->zipService->makeRequest('GET', '/links', $params);
        
        $links = [];
        foreach ($response['data'] ?? [] as $linkData) {
            $links[] = new PaymentLinkResponse($linkData);
        }
        
        return [
            'data' => $links,
            'has_more' => $response['has_more'] ?? false,
            'total_count' => $response['total_count'] ?? count($links),
        ];
    }

    /**
     * Update a payment link
     *
     * @param string $linkId
     * @param array $data
     * @return PaymentLinkResponse
     * @throws Exception
     */
    public function updateLink(string $linkId, array $data): PaymentLinkResponse
    {
        $validator = new PaymentLinkValidator();
        $validatedData = $validator->validateUpdate($data);
        
        $linkData = new PaymentLinkData($validatedData);
        
        $response = $this->zipService->makeRequest('PATCH', "/links/{$linkId}", $linkData->toArray());
        
        return new PaymentLinkResponse($response);
    }

    /**
     * Deactivate a payment link
     *
     * @param string $linkId
     * @return PaymentLinkResponse
     * @throws Exception
     */
    public function deactivateLink(string $linkId): PaymentLinkResponse
    {
        $response = $this->zipService->makeRequest('POST', "/links/{$linkId}/deactivate");
        
        return new PaymentLinkResponse($response);
    }

    /**
     * Activate a payment link
     *
     * @param string $linkId
     * @return PaymentLinkResponse
     * @throws Exception
     */
    public function activateLink(string $linkId): PaymentLinkResponse
    {
        $response = $this->zipService->makeRequest('POST', "/links/{$linkId}/activate");
        
        return new PaymentLinkResponse($response);
    }
}
