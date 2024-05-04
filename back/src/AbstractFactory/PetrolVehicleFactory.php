<?php

namespace App\AbstractFactory;

use App\AbstractFactory\AbstractCar;
use App\AbstractFactory\AbstractScooter;
use App\AbstractFactory\ElectricScooter;

class PetrolVehicleFactory implements VehicleFactoryInterface
{
    public function createCar(int $id, string $brand, array $color, string $power, string $space): AbstractCar
    {
        return new PetrolCar($id, $brand, $color, $power, $space);
    }

    public function createScooter(int $id, string $brand, array $color, string $power): AbstractScooter
    {
        return new PetrolScooter($id, $brand, $color, $power);
    }
}