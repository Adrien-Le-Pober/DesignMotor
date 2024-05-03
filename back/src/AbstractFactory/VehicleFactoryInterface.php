<?php

namespace App\AbstractFactory;

interface VehicleFactoryInterface
{
    public function createCar(
        string $brand,
        array $color,
        string $power,
        string $space
    ): AbstractCar;

    public function createScooter(
        string $brand,
        array $color,
        string $power
    ): AbstractScooter;
}