<?php

namespace ConnectBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ConnectBundle\Messenger\SmsNotification;
use Symfony\Component\Messenger\MessageBusInterface;

class DefaultController extends FrontendController
{
//    /**
//     * @Route("/connect")
//     */
//    public function indexAction(Request $request, MessageBusInterface $bus): Response
//    {
//        $bus->dispatch(new SmsNotification('Look! I created a message!'));
//
//        return new Response('Hello world from connect');
//    }


    public function sendSmsAction(Request $request, MessageBusInterface $bus): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        if (isset($content['phone_number']) && isset($content['message'])) {
            $phoneNumber = $content['phone_number'];
            $messageContent = $content['message'];

            $bus->dispatch(new SmsNotification($messageContent));

            return new JsonResponse(['status' => 'success', 'message' => 'SMS dispatched']);
        }

        return new JsonResponse(['status' => 'error', 'message' => 'Invalid request data'], 400);
    }
}
