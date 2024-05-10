<?php

namespace App\AbstractFactory;

use App\Trait\Base64ImageTrait;
use App\AbstractFactory\AbstractCar;

class PetrolCar extends AbstractCar
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
            'space' => $this->space,
            'image' => $this->getBase64Image($this->imagePath)
        ];
    }
}