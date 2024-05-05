<?php

namespace App\AbstractFactory;

use App\AbstractFactory\AbstractCar;

class PetrolCar extends AbstractCar
{
    public function getVehicleInfos(): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'color' => $this->color,
            'power' => $this->power,
            'space' => $this->space
        ];
    }
}