<?php

namespace App\AbstractFactory;

use App\AbstractFactory\AbstractCar;
use App\AbstractFactory\AbstractScooter;
use App\AbstractFactory\ElectricScooter;

class PetrolVehicleFactory implements VehicleFactoryInterface
{
    public function createCar(string $brand, array $color, string $power, string $space): AbstractCar
    {
        return new PetrolCar($brand, $color, $power, $space);
    }

    public function createScooter(string $brand, array $color, string $power): AbstractScooter
    {
        return new PetrolScooter($brand, $color, $power);
    }
}