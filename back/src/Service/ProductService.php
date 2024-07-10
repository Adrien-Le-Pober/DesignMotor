<?php

namespace App\Service;

use App\Service\DiscountRuleService;
use App\Repository\VehicleRepository;

class ProductService
{
    public function __construct(
        private VehicleRepository $vehicleRepository,
        private DiscountRuleService $discountRuleService
    ) {}

    public function getProductDetails(array $cartItems): array
    {
        $products = [];
        foreach ($cartItems as $item) {
            $product = $this->vehicleRepository->findDetailsById($item['productId']);
            $this->discountRuleService->applyRules($product);
            $productPrice = $product['soldedPrice'] ?? $product['price'];
            $products[] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'price' => $productPrice
            ];
        }
        return $products;
    }
}