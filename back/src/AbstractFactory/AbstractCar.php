<?php

namespace App\AbstractFactory;

abstract class AbstractCar
{
    public function __construct(
        protected int $id,
        protected string $brand,
        protected string $model,
        protected string $imagePath,
        protected float $price
    ) { }

    abstract public function getVehicleInfos(): array;
}