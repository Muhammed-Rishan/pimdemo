<?php



namespace App\Traits;

trait TransmissionTrait
{
    public function getRoomCondition(): ?string
    {
        return "good";
    }

    public function getRoomNote(): ?string
    {
        return "suite room";
    }
}
