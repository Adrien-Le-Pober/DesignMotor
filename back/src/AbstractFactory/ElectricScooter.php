<?php

namespace App\AbstractFactory;

use App\AbstractFactory\AbstractScooter;

class ElectricScooter extends AbstractScooter
{
    public function getVehicleInfos(): string
    {
        return json_encode([
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'color' => $this->color,
            'power' => $this->power,
        ]);
    }
}