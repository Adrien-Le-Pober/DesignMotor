<?php

namespace App\AbstractFactory;

abstract class AbstractScooter
{
    public function __construct(
        protected int $id,
        protected string $brand,
        protected string $model,
        protected array $color,
        protected string $power
    ) { }

    abstract public function getVehicleInfos(): string;
}