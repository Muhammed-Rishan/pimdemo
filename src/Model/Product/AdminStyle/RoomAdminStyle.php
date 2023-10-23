<?php
// RoomAdminStyle.php

namespace App\Model\Product\AdminStyle;

use Pimcore\Model\Element\AdminStyle;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\DataObject\Room;

class RoomAdminStyle extends AdminStyle
{
    protected ElementInterface $element;

    public function __construct(ElementInterface $element)
    {
        parent::__construct($element);

        $this->element = $element;

        if ($element instanceof Room && $element->getId() === 4) {
            $this->elementIcon = '/bundles/pimcoreadmin/img/icon/build.png';
        }
    }
}
