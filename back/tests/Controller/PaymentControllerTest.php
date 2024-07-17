<?php

namespace App\Tests\Controller;

use App\Entity\Order;
use App\Service\EmailService;
use App\Service\OrderService;
use App\Service\StripeService;
use App\Payment\PaymentGatewayInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PaymentControllerTest extends WebTestCase
{
    private $client;
    private $emailService;
    private $orderService;
    private $paymentGateway;
    private $stripeService;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->emailService = $this->createMock(EmailService::class);
        $this->orderService = $this->createMock(OrderService::class);
        $this->paymentGateway = $this->createMock(PaymentGatewayInterface::class);
        $this->stripeService = $this->createMock(StripeService::class);

        $this->client->getContainer()->set('App\Service\EmailService', $this->emailService);
        $this->client->getContainer()->set('App\Service\OrderService', $this->orderService);
        $this->client->getContainer()->set('App\Payment\PaymentGatewayInterface', $this->paymentGateway);
        $this->client->getContainer()->set('App\Service\StripeService', $this->stripeService);
    }

    public function testCreatePayment()
    {
        $this->orderService->expects($this->once())
            ->method('createOrder')
            ->willReturn($order = new Order());

        $this->paymentGateway->expects($this->once())
            ->method('createPayment')
            ->with($order)
            ->willReturn('https://payment.url');

        $this->emailService->expects($this->once())
            ->method('sendInvoiceEmail')
            ->with($order);

        $this->client->request(
            'POST',
            '/payment/create',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['some' => 'data'])
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['paymentUrl' => 'https://payment.url']),
            $this->client->getResponse()->getContent()
        );
    }

    public function testStripeWebhook()
    {
        $payload = 'test_payload';
        $sigHeader = 'test_signature';

        $this->stripeService->expects($this->once())
            ->method('handleWebhook')
            ->with($payload, $sigHeader);

        $this->client->request(
            'POST',
            '/stripe/webhook',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_stripe-signature' => $sigHeader],
            $payload
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Webhook handled', $this->client->getResponse()->getContent());
    }

    public function testStripeWebhookThrowsException()
    {
        $payload = 'test_payload';
        $sigHeader = 'test_signature';

        $this->stripeService->expects($this->once())
            ->method('handleWebhook')
            ->with($payload, $sigHeader)
            ->will($this->throwException(new \Exception('Webhook error')));

        $this->client->request(
            'POST',
            '/stripe/webhook',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_stripe-signature' => $sigHeader],
            $payload
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Webhook error', $this->client->getResponse()->getContent());
    }
}