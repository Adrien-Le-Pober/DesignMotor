<?php

namespace App\AbstractFactory;

abstract class AbstractCar
{
    public function __construct(
        protected string $brand,
        protected array $color,
        protected string $power,
        protected string $space
    ) { }

    abstract public function getInfos(): string;
}