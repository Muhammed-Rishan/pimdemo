<?php

namespace App\Controller;

use App\Service\NewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyLogController extends AbstractController
{
    /**
     * @Route("/new-log", name="new_log")
     */
    public function yourAction(NewService $newService): JsonResponse
    {
        $newService->someMethod();

        return new JsonResponse(['message'=> 'Success']);
    }
}
