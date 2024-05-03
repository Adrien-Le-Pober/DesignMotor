<?php

namespace App\AbstractFactory;

use App\AbstractFactory\AbstractCar;

class PetrolCar extends AbstractCar
{
    public function getInfos(): string
    {
        return json_encode([
            'brand' => $this->brand,
            'color' => $this->color,
            'power' => $this->power,
            'space' => $this->space
        ]);
    }
}