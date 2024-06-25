<?php

namespace App\AbstractFactory;

use App\AbstractFactory\AbstractCar;
use App\Trait\Base64ImageTrait;

class ElectricCar extends AbstractCar
{
    use Base64ImageTrait;

    public function getVehicleInfos(): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'image' => $this->getBase64Image($this->imagePath),
            'price' => $this->price,
            'soldedPrice' => $this->soldedPrice
        ];
    }
}