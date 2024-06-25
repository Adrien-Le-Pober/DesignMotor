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
        float $price,
        float|null $soldedPrice,
    ): AbstractCar
    {
        return new PetrolCar($id, $brand, $model, $imagePath, $price, $soldedPrice);
    }

    public function createScooter(
        int $id,
        string $brand,
        string $model,
        string $imagePath,
        float $price,
        float|null $soldedPrice,
    ): AbstractScooter
    {
        return new PetrolScooter($id, $brand, $model, $imagePath, $price, $soldedPrice);
    }
}