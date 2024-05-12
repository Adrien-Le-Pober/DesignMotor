<?php

namespace App\AbstractFactory;

use App\AbstractFactory\AbstractScooter;
use App\Trait\Base64ImageTrait;

class ElectricScooter extends AbstractScooter
{
    use Base64ImageTrait;

    public function getVehicleInfos(): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'color' => $this->color,
            'power' => $this->power,
            'image' => $this->getBase64Image($this->imagePath),
            'price' => $this->price
        ];
    }
}