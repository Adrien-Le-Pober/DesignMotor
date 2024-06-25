<?php

namespace App\AbstractFactory;

abstract class AbstractScooter
{
    public function __construct(
        protected int $id,
        protected string $brand,
        protected string $model,
        protected string $imagePath,
        protected float $price,
        protected float|null $soldedPrice,
    ) { }

    abstract public function getVehicleInfos(): array;
}