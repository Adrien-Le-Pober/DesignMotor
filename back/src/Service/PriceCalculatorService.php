<?php

namespace App\Service;

class PriceCalculatorService
{
    public function calculateTotalPrice(array $products): float
    {
        $totalPrice = 0;
        foreach ($products as $product) {
            $totalPrice += $product['price'] * $product['quantity'];
        }
        return $totalPrice;
    }
}