<?php

namespace App\Controller;

use App\Entity\Tva;
use App\Entity\User;
use App\Entity\Order;
use App\Enum\PaymentStatus;
use App\Service\EmailService;
use App\Service\DiscountRuleService;
use App\Repository\VehicleRepository;
use App\Payment\PaymentGatewayInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    private $paymentGateway;

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    #[Route('/payment/create', name: 'create_payment', methods: ['POST'])]
    public function createPayment(
        Request $request,
        EntityManagerInterface $entityManager,
        DiscountRuleService $discountRuleService,
        VehicleRepository $vehicleRepository,
        EmailService $emailService
    ): JsonResponse {
        $cartData = json_decode($request->getContent(), true);

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $cartData['userEmail']]);
        $cartItems = $cartData['cartItems'];
        $totalPrice = 0;
        $products = [];

        foreach ($cartItems as $item) {
            $product = $vehicleRepository->findDetailsById($item['productId']);
            $discountRuleService->applyRules($product);
            $productPrice = $product['soldedPrice'] ?? $product['price'];
            $products[] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'price' => $productPrice
            ];
            $totalPrice += $productPrice * $item['quantity'];
        }

        $order = (new Order)
            ->setStatus(PaymentStatus::PENDING->value)
            ->setUser($user)
            ->setProducts($products)
            ->setPrice($totalPrice)
            ->setTva($entityManager->getRepository(Tva::class)->find(1)->getValue());

        $entityManager->persist($order);
        $entityManager->flush();

        $paymentUrl = $this->paymentGateway->createPayment($order, $products);

        $emailService->sendInvoiceEmail($order);

        return new JsonResponse(['paymentUrl' => $paymentUrl]);
    }
}