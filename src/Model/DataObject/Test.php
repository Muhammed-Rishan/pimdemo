<?php

namespace App\Model\DataObject;

class Test extends \Pimcore\Model\DataObject\Test
{

    protected ?string $CustomAttribute;

    public function getAttribute(): ?string
    {
        return $this->CustomAttribute;
    }

    public function setAttribute(string $value): void
    {
        $this->CustomAttribute = $value;
    }
}

