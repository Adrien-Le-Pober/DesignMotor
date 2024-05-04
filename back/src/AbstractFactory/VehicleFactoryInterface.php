<?php

namespace App\AbstractFactory;

interface VehicleFactoryInterface
{
    public function createCar(
        int $id,
        string $brand,
        array $color,
        string $power,
        string $space
    ): AbstractCar;

    public function createScooter(
        int $id,
        string $brand,
        array $color,
        string $power
    ): AbstractScooter;
}