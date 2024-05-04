<?php

namespace App\AbstractFactory;

use App\AbstractFactory\AbstractCar;

class ElectricCar extends AbstractCar
{
    public function getVehicleInfos(): string
    {
        return json_encode([
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'color' => $this->color,
            'power' => $this->power,
            'space' => $this->space
        ]);
    }
}