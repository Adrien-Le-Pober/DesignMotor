<?php

namespace App\AbstractFactory;

use App\AbstractFactory\AbstractCar;
use App\AbstractFactory\AbstractScooter;

class PetrolVehicleFactory implements VehicleFactoryInterface
{
    public function createCar(
        int $id,
        string $brand,
        string $model,
        array $color,
        string $power,
        string $space,
        string $imagePath
    ): AbstractCar
    {
        return new PetrolCar($id, $brand, $model, $color, $power, $space, $imagePath);
    }

    public function createScooter(
        int $id,
        string $brand,
        string $model,
        array $color, 
        string $power,
        string $imagePath
    ): AbstractScooter
    {
        return new PetrolScooter($id, $brand, $model, $color, $power, $imagePath);
    }
}