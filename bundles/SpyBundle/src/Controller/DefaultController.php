<?php

namespace SpyBundle\Controller;

use SpyBundle\Message\DPE;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @Route("/spy")
     */
    public function index(MessageBusInterface $bus): JsonResponse
    {
        $message = 'some message !';

        $bus->dispatch(new DPE($message));


        return new JsonResponse(['message' => $message]);
    }
}
