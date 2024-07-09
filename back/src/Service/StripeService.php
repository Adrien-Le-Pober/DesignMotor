<?php

namespace App\Service;

use Stripe\Webhook;
use App\Enum\PaymentStatus;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class StripeService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private EntityManagerInterface $entityManager,
        private string $endpointSecret
    ) {}

    public function handleWebhook($payload, $sigHeader)
    {
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $this->endpointSecret);
        } catch(\UnexpectedValueException $e) {
            throw new \Exception('Invalid payload');
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            throw new \Exception('Invalid signature');
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentFailed($paymentIntent);
                break;
            default:
                throw new \Exception('Event type not handled');
        }
    }

    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        $order = $this->orderRepository->findOneBy(['stripeSessionId' => $paymentIntent->id]);

        if (!$order) {
            throw new \Exception('Order not found');
        }

        $order->setStatus(PaymentStatus::COMPLETED->value);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    private function handlePaymentIntentFailed($paymentIntent)
    {
        $order = $this->orderRepository->findOneBy(['stripeSessionId' => $paymentIntent->id]);

        if (!$order) {
            throw new \Exception('Order not found');
        }

        $order->setStatus(PaymentStatus::FAILED->value);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}