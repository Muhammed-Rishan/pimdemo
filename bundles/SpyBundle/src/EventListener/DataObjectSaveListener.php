<?php

namespace SpyBundle\EventListener;

use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;
use Pimcore\Model\DataObject\Custom;
use SpyBundle\Service\ApiService;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Pimcore\Event\Model\DataObjectEvent;

class DataObjectSaveListener
{
    private ApiService $apiService;
    private HttpClientInterface $httpClient;
//    private LoggerInterface $logger;

    private ApplicationLogger $logger;

//    public function __construct(ApplicationLogger $logger)
//    {
//        $this->logger = $logger;
//    }

    public function __construct(ApiService $apiService, HttpClientInterface $httpClient, ApplicationLogger $logger)
    {
        $this->apiService = $apiService;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }


    /**
     */
    public function onPostUpdate(DataObjectEvent $event): void
    {
        $dataObject = $event->getObject();

        if ($dataObject instanceof Custom && $dataObject->isPublished()) {
            $objectId = $dataObject->getId();
            $name = 'Rishan';

            try {
                $response = $this->httpClient->request('POST', 'https://codilar-pimcore-test.free.beeceptor.com/dataobjects/test', [
//                    'headers' => [
//                        'Content-Type' => 'application/x-www-form-urlencoded',
//                    ],
                    'json' => [
                        'objectId' => $objectId,
                        'name' => $name,
                    ],
                ]);

                $statusCode = $response->getStatusCode();

                if ($statusCode >= 200 && $statusCode < 300) {
                    $this->logger->info('API request successful - objectId: ' . $objectId);
                } else {
                    $this->logger->error('API request failed with status code: ' . $statusCode);
                }
            }
            catch (TransportExceptionInterface $e) {
                $this->logger->error('TransportException: ' . $e->getMessage());
            }
        }
    }
}

