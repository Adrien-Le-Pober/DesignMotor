<?php

namespace App\AbstractFactory;

use App\AbstractFactory\AbstractScooter;

class PetrolScooter extends AbstractScooter
{
    public function getVehicleInfos(): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'color' => $this->color,
            'power' => $this->power,
        ];
    }
}