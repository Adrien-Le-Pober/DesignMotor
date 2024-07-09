<?php

namespace App\Payment;

use Stripe\Stripe;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class StripePaymentGateway implements PaymentGatewayInterface
{
    private string $stripeSecretKey;
    private $frontendBaseUrl;

    public function __construct(
        string $stripeSecretKey,
        string $frontendBaseUrl,
        private EntityManagerInterface $entityManager
    ) {
        $this->stripeSecretKey = $stripeSecretKey;
        $this->frontendBaseUrl = $frontendBaseUrl;

        Stripe::setApiKey($this->stripeSecretKey);
    }

    public function createPayment(Order $order, array $products): string
    {
        try {
            $lineItems = [];

            foreach ($products as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $item['product']['brand'] . ' ' . $item['product']['model'],
                        ],
                        'unit_amount' => $item['price'] * 100, // Stripe expects amount in cents
                    ],
                    'quantity' => $item['quantity'],
                ];
            }

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $this->frontendBaseUrl . '/paiement-reussi?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $this->frontendBaseUrl . '/paiement-echec?session_id={CHECKOUT_SESSION_ID}',
            ]);

            $order->setStripeSessionId($session->id);
            $this->entityManager->persist($order);
            $this->entityManager->flush();

            return $session->url;
        } catch (ApiErrorException $e) {
            throw new \Exception('Could not create Stripe payment session: ' . $e->getMessage());
        }
    }
}