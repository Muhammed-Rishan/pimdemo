<?php

namespace App\Controller;

use Pimcore\Model\DataObject\Room;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RoomController extends AbstractController
{
    /**
     * @Route("/room/{roomNumber}", name="room_preview")
     */
    public function show($roomNumber): JsonResponse|Response
    {
        // Fetch the room based on roomNumber
        $room = Room::getByProperty('roomNumber', $roomNumber);

        if ($room instanceof Room) {
            $roomNumber = $room->getRoomnumber();
            $roomDescription = $room->getDescription();

            return $this->render('room/show.html.twig', [
                'roomDetails' => $room,
            ]);
        } else {
            return new JsonResponse(['message' => 'Room not found'], 404);
        }
    }
}
