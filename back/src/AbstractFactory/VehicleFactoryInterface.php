<?php

namespace App\AbstractFactory;

interface VehicleFactoryInterface
{
    public function createCar(
        int $id,
        string $brand,
        string $model,
        string $imagePath,
        float $price
    ): AbstractCar;

    public function createScooter(
        int $id,
        string $brand,
        string $model,
        string $imagePath,
        float $price
    ): AbstractScooter;
}