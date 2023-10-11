<?php

namespace App\Website\LinkGenerator;



use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Tool\Text;


class RoomLinkGenerator implements LinkGeneratorInterface
{
    public function generate($object, array $params = []): string
    {
        if (!($object instanceof DataObject\Room)) {
            throw new \InvalidArgumentException('Given object is not a Room');
        }

        $customUrl = $this->generateCustomUrl($object);

        return $customUrl;
    }

    protected function generateCustomUrl(DataObject\Room $object): string
    {
        $roomNumber = $object->getRoomnumber();




        $customUrl = '/room/'.$roomNumber ;

        return $customUrl;

//        return $this->router->generate('room_preview', ['roomNumber' => $roomNumber]);
    }
}

