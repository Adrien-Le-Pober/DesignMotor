<?php

namespace App\Tests\Entity;

use App\Entity\Discount;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class DiscountEntityTest extends KernelTestCase
{
    public function getEntity(): Discount
    {
        return (new Discount())
            ->setStorageDuration(30)
            ->setRate(0.5);
    }

    public function testValidDiscount(): void
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testInvalidStorageDuration(): void
    {
        $this->assertHasErrors($this->getEntity()->setStorageDuration(-1), 1);
    }

    public function testInvalidRate(): void
    {
        $this->assertHasErrors($this->getEntity()->setRate(-0.5), 1);
        $this->assertHasErrors($this->getEntity()->setRate(1.5), 1);
    }

    public function assertHasErrors(Discount $discount, int $number = 0): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());

        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($discount);
        $messages = [];

        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }

        $this->assertCount($number, $errors, implode(', ', $messages));
    }
}