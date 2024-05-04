<?php

namespace App\AbstractFactory;

use App\AbstractFactory\AbstractScooter;

class ElectricScooter extends AbstractScooter
{
    public function getInfos(): string
    {
        return json_encode([
            'id' => $this->id,
            'brand' => $this->brand,
            'color' => $this->color,
            'power' => $this->power,
        ]);
    }
}