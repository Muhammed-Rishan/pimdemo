<?php

namespace App\EventListener;

use Pimcore\Model\DataObject\Room;

class AdminStyleListener
{
    public function onResolveElementAdminStyle(\Pimcore\Bundle\AdminBundle\Event\ElementAdminStyleEvent $event): void
    {
        $element = $event->getElement();
//        if ($element instanceof Room) {
//            $event->setAdminStyle(new \ProductBundle\Model\Product\AdminStyle\RoomAdminStyle($element));
//        }
    }
}
