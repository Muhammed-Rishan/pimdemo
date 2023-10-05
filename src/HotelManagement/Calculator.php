<?php

namespace App\HotelManagement;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;

class Calculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if ($context->getFieldname() == "sum") {
            $language = $context->getPosition();
//            return $object->get($language) + $object->getYValue($language);
        } else {
            return "Error: Unknown field";
        }
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        $language = $context->getPosition();
        $result = "";
        return $result;
    }
}
