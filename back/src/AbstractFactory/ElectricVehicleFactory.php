<?php

namespace App\AbstractFactory;

use App\AbstractFactory\AbstractCar;
use App\AbstractFactory\AbstractScooter;
use App\AbstractFactory\ElectricScooter;

class ElectricVehicleFactory implements VehicleFactoryInterface
{
    public function createCar(int $id, string $brand, string $model, array $color, string $power, string $space): AbstractCar
    {
        return new ElectricCar($id, $brand, $model, $color, $power, $space);
    }

    public function createScooter(int $id, string $brand, string $model, array $color, string $power): AbstractScooter
    {
        return new ElectricScooter($id, $brand, $model, $color, $power);
    }
}