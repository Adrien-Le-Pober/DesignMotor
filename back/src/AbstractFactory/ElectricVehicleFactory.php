<?php

namespace App\AbstractFactory;

use App\AbstractFactory\AbstractCar;
use App\AbstractFactory\AbstractScooter;
use App\AbstractFactory\ElectricScooter;

class ElectricVehicleFactory implements VehicleFactoryInterface
{
    public function createCar(
        int $id,
        string $brand,
        string $model,
        string $imagePath,
        float $price
    ): AbstractCar
    {
        return new ElectricCar($id, $brand, $model, $imagePath, $price);
    }

    public function createScooter(
        int $id,
        string $brand,
        string $model,
        string $imagePath,
        float $price
    ): AbstractScooter
    {
        return new ElectricScooter($id, $brand, $model, $imagePath, $price);
    }
}