<?php

namespace SpyBundle\Controller\Api;

use Pimcore\Controller\FrontendController;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\Custom;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SpyBundle\Service\ApiService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiController extends FrontendController
{
    private LoggerInterface $logger;
    private ApiService $apiService;
//    private HttpClientInterface $httpClient;
    private mixed $eventDispatcher;

    public function __construct(LoggerInterface $logger, ApiService $apiService)
    {
        $this->logger = $logger;
        $this->apiService = $apiService;
//        $this->httpClient = $httpClient;
    }

    public function getDataObject(Request $request, $id): JsonResponse
    {
        $apiKey = $request->headers->get('Authorization');

        if ($this->apiService->authenticate($apiKey)) {
            $dataObject = Custom::getById($id);

            if ($dataObject instanceof Custom) {
                $data = [
                    'name' => $dataObject->getName(),
                ];
                return new JsonResponse($data);
            } else {
                return new JsonResponse(['error' => 'Data object not found'], Response::HTTP_NOT_FOUND);
            }
        } else {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
    }


//    public function createDataObject(Request $request): JsonResponse
//    {
//        $apiKey = $request->headers->get('Authorization');
//
//        if ($this->apiService->authenticate($apiKey)) {
//            $requestData = json_decode($request->getContent(), true);
//
//            if (!$requestData || !isset($requestData['name'])) {
//                return new JsonResponse(['error' => 'Invalid request data'], Response::HTTP_BAD_REQUEST);
//            }
//
//            $dataObject = new Custom();
//            $dataObject->setName($requestData['name']);
//            $dataObject->save();
//
//            // Dispatch the event after the data object is created
//            $event = new DataObjectEvent($dataObject);
//            $this->eventDispatcher->dispatch($event, 'pimcore.dataobject.postUpdate');
//
//            return new JsonResponse(['success' => 'Data object created'], Response::HTTP_CREATED);
//        } else {
//            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
//        }
//    }

    public function createDataObject(Request $request, $name, EventDispatcherInterface $eventDispatcher): JsonResponse
    {
        $apikey = $request->headers->get('Authorization');

        $this->logger->info('API Key received for createDataObject', ['api_key' => $apikey]);

        $dataObject = new Custom();
        $dataObject->setKey($name);
        $dataObject->setParentId(33);

        $requestData = json_decode($request->getContent(), true);

        foreach ($requestData as $property => $value) {
            if (property_exists(Custom::class, $property)) {
                $setterMethod = 'set' . ucfirst($property);

                if (method_exists($dataObject, $setterMethod)) {
                    $dataObject->$setterMethod($value);
                }
            }
        }

        try {
            $dataObject->save();

            if ($dataObject->getId()) {
                $event = new DataObjectEvent($dataObject);
                $eventDispatcher->dispatch($event, "pimcore.dataobject.postUpdate");

                $requestMethod = $request->getMethod();

                return new JsonResponse(['message' => 'created successfully', 'id' => $dataObject->getId(), 'messages' => "You hit a $requestMethod request"]);
            } else {
                return $this->json(['error' => 'Error creating Custom object'], 500);
            }
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error creating Custom object: ' . $e->getMessage()], 500);
        }
    }




}
