<?php
// src/ConnectBundle/Service/SmsService.php
namespace ConnectBundle\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SmsService
{
    private HttpClientInterface $httpClient;
    private string $smsApiUrl;
    private string $apiKey;

    public function __construct(HttpClientInterface $httpClient, string $smsApiUrl, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->smsApiUrl = $smsApiUrl;
        $this->apiKey = $apiKey;
    }

    public function sendSms(string $phoneNumber, string $message): void
    {
        try {
            $response = $this->httpClient->request('POST', $this->smsApiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'phone_number' => $phoneNumber,
                    'message' => $message,
                ],
            ]);

            // Handle the response as needed
            $statusCode = $response->getStatusCode();
            // Add further logic based on the response, if required
        } catch (\Exception $e) {
            // Handle exceptions appropriately (e.g., log or rethrow)
            // Log the error using the logger or rethrow the exception
        } catch (TransportExceptionInterface $e) {
        }
    }
}
