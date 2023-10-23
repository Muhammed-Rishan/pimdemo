<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;

class Room extends Concrete
{
    protected ?string $newCustomAttribute;

    public function getNewAttribute(): ?string
    {
        return $this->newCustomAttribute;
    }

    public function setNewAttribute(string $value): void
    {
        $this->newCustomAttribute = $value;
    }
}
