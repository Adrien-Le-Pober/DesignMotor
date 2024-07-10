<?php

namespace App\Controller;

use App\Service\EmailService;
use App\Service\StripeService;
use App\Payment\PaymentGatewayInterface;
use App\Service\OrderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    #[Route('/payment/create', name: 'create_payment', methods: ['POST'])]
    public function createPayment(
        Request $request,
        EmailService $emailService,
        OrderService $orderService,
        PaymentGatewayInterface $paymentGateway,
    ): JsonResponse {
        $cartData = json_decode($request->getContent(), true);

        $order = $orderService->createOrder($cartData);

        $paymentUrl = $paymentGateway->createPayment($order);

        $emailService->sendInvoiceEmail($order);

        return new JsonResponse(['paymentUrl' => $paymentUrl]);
    }

    #[Route('/stripe/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function stripeWebhook(Request $request, StripeService $stripeService): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');

        try {
            $stripeService->handleWebhook($payload, $sigHeader);
            return new Response('Webhook handled', Response::HTTP_OK);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}