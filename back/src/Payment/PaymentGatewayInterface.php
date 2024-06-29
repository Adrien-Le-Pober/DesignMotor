<?php

namespace App\Payment;

use App\Entity\Order;

interface PaymentGatewayInterface
{
    public function createPayment(Order $order, array $products): string;
    public function handlePaymentSuccess(string $paymentId): void;
    public function handlePaymentFailure(string $paymentId): void;
}