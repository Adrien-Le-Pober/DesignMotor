<?php

namespace App\AbstractFactory;

use App\Trait\Base64ImageTrait;
use App\AbstractFactory\AbstractScooter;

class PetrolScooter extends AbstractScooter
{
    use Base64ImageTrait;

    public function getVehicleInfos(): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'image' => $this->getBase64Image($this->imagePath),
            'price' => $this->price
        ];
    }
}