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
        string $imagePath,
        float $price
    ): AbstractCar
    {
        return new PetrolCar($id, $brand, $model, $imagePath, $price);
    }

    public function createScooter(
        int $id,
        string $brand,
        string $model,
        string $imagePath,
        float $price
    ): AbstractScooter
    {
        return new PetrolScooter($id, $brand, $model, $imagePath, $price);
    }
}