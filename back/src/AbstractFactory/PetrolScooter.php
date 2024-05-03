<?php

namespace App\AbstractFactory;

use App\AbstractFactory\AbstractScooter;

class PetrolScooter extends AbstractScooter
{
    public function getInfos(): string
    {
        return json_encode([
            'brand' => $this->brand,
            'color' => $this->color,
            'power' => $this->power,
        ]);
    }
}