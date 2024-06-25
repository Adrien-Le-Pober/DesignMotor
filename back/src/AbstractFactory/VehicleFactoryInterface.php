<?php

namespace App\AbstractFactory;

interface VehicleFactoryInterface
{
    public function createCar(
        int $id,
        string $brand,
        string $model,
        string $imagePath,
        float $price,
        float|null $soldedPrice
    ): AbstractCar;

    public function createScooter(
        int $id,
        string $brand,
        string $model,
        string $imagePath,
        float $price,
        float|null $soldedPrice
    ): AbstractScooter;
}