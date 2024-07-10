<?php

namespace App\Service;

use App\Entity\Tva;
use App\Entity\User;
use App\Entity\Order;
use App\Enum\PaymentStatus;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductService $productService,
        private PriceCalculatorService $priceCalculatorService
    ) {}

    public function createOrder(array $cartData): Order
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $cartData['userEmail']]);
        $products = $this->productService->getProductDetails($cartData['cartItems']);
        $totalPrice = $this->priceCalculatorService->calculateTotalPrice($products);
        $tva = $this->entityManager->getRepository(Tva::class)->find(1)->getValue();

        $order = (new Order)
            ->setStatus(PaymentStatus::PENDING->value)
            ->setUser($user)
            ->setProducts($products)
            ->setPrice($totalPrice)
            ->setTva($tva);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }
}