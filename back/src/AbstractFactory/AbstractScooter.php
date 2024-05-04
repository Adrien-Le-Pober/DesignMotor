<?php

namespace App\AbstractFactory;

abstract class AbstractScooter
{
    public function __construct(
        protected int $id,
        protected string $brand,
        protected array $color,
        protected string $power,
    ) { }

    abstract public function getInfos(): string;
}