<?php

namespace App\Interpreter;

class DiscountAction
{
    public function __construct(private $discountPercentage)
    { }

    public function execute(array &$vehicleData)
    {
        if ($vehicleData['soldedPrice']) {
            $vehicleData['soldedPrice'] *= (1 - $this->discountPercentage / 100);
        } else {
            $vehicleData['soldedPrice'] = $vehicleData['price'] * (1 - $this->discountPercentage / 100);
        }
    }
}