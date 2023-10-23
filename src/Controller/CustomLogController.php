<?php
namespace App\Controller;

use App\Service\CustomService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomLogController extends AbstractController
{
    /**
     * @Route("/custom-log", name="custom_log")
     */
    public function someAction(CustomService $customService): JsonResponse
    {

        $customService->someMethod();

        return new JsonResponse(['message'=> 'Success']);

    }
}
