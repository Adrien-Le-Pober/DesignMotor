<?php

namespace App\Payment;

use App\Entity\Order;

interface PaymentGatewayInterface
{
    public function createPayment(Order $order): string;
}