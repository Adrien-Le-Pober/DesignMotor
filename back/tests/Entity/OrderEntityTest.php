<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Order;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderEntityTest extends KernelTestCase
{
    public function getEntity(): Order
    {
        $user = (new User())
            ->setEmail('test' . uniqid() . '@test.com')
            ->setPassword('Password123!')
            ->setRoles(["ROLE_USER"])
            ->setVerified(true)
            ->setRgpd(true)
            ->setLastname('Doe')
            ->setFirstname('John')
            ->setPhone('1234567890');

        return (new Order())
            ->setStatus('P')
            ->setReference('550e8400-e29b-41d4-a716-446655440000')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable())
            ->setPrice(99.99)
            ->setUser($user)
            ->setProducts(['product1', 'product2'])
            ->setTva(20.0)
            ->setStripeSessionId('sess_1234567890');
    }

    public function testIsValid(): void
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testInvalidBlankArgument(): void
    {
        $this->assertHasErrors($this->getEntity()->setReference(''), 1);
        $this->assertHasErrors($this->getEntity()->setUser(null), 1);
    }

    public function testInvalidStatus(): void
    {
        $this->assertHasErrors($this->getEntity()->setStatus('X'), 1);
        $this->assertHasErrors($this->getEntity()->setStatus(1), 1);
    }

    public function testInvalidReference(): void
    {
        $this->assertHasErrors($this->getEntity()->setReference('invalid-uuid'), 1);
    }

    public function testInvalidPrice(): void
    {
        $this->assertHasErrors($this->getEntity()->setPrice(-10.0), 1);
        $this->assertHasErrors($this->getEntity()->setPrice(99.999), 1);
    }

    public function testInvalidUser(): void
    {
        $this->assertHasErrors($this->getEntity()->setUser(null), 1);
    }

    public function testInvalidTva(): void
    {
        $this->assertHasErrors($this->getEntity()->setTva(-5.0), 1);
    }

    public function testInvalidStripeSessionId(): void
    {
        $this->assertHasErrors($this->getEntity()->setStripeSessionId(str_repeat('a', 256)), 1);
    }

    public function assertHasErrors(Order $order, int $number = 0): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());

        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($order);
        $messages = [];

        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }

        $this->assertCount($number, $errors, implode(', ', $messages));
    }
}