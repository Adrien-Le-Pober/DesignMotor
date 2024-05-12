<?php

namespace App\AbstractFactory;

interface VehicleFactoryInterface
{
    public function createCar(
        int $id,
        string $brand,
        string $model,
        array $color,
        string $power,
        string $space,
        string $imagePath,
        float $price
    ): AbstractCar;

    public function createScooter(
        int $id,
        string $brand,
        string $model,
        array $color,
        string $power,
        string $imagePath,
        float $price
    ): AbstractScooter;
}