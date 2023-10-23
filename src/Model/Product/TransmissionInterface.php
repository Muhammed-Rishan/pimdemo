<?php



namespace App\Model\Product;

interface TransmissionInterface
{
    public function getRoomCondition(): ?string;

    public function getRoomNote(): ?string ;
}
