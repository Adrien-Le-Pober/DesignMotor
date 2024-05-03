<?php

namespace App\AbstractFactory;

use App\AbstractFactory\AbstractCar;
use App\AbstractFactory\AbstractScooter;
use App\AbstractFactory\ElectricScooter;

class ElectricVehicleFactory implements VehicleFactoryInterface
{
    public function createCar(string $brand, array $color, string $power, string $space): AbstractCar
    {
        return new ElectricCar($brand, $color, $power, $space);
    }

    public function createScooter(string $brand, array $color, string $power): AbstractScooter
    {
        return new ElectricScooter($brand, $color, $power);
    }
}