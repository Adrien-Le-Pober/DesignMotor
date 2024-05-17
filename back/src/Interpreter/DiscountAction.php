<?php

namespace App\Interpreter;

class DiscountAction
{
    
    public function __construct(private $discountPercentage)
    { }

    public function execute(array &$vehicleData)
    {
        $vehicleData['price'] *= (1 - $this->discountPercentage / 100);
    }
}