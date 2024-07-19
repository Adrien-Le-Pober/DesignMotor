<?php

namespace App\Tests\Entity;

use App\Entity\DiscountRuleAction;
use App\Entity\DiscountRule;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class DiscountRuleActionEntityTest extends KernelTestCase
{
    public function getEntity(): DiscountRuleAction
    {
        $discountRule = new DiscountRule();

        return (new DiscountRuleAction())
            ->setType('discount')
            ->setValue(50)
            ->setDiscountRule($discountRule);
    }

    public function testValidDiscountRuleAction(): void
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidType(): void
    {
        $this->assertHasErrors($this->getEntity()->setType('invalid_type'), 1);
    }

    public function testInvalidValue(): void
    {
        $this->assertHasErrors($this->getEntity()->setValue(-10), 1);
        $this->assertHasErrors($this->getEntity()->setValue(150), 1);
    }

    public function testInvalidDiscountRule(): void
    {
        $this->assertHasErrors($this->getEntity()->setDiscountRule(null), 1);
    }

    public function assertHasErrors(DiscountRuleAction $discountRuleAction, int $number = 0): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());

        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($discountRuleAction);
        $messages = [];

        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }

        $this->assertCount($number, $errors, implode(', ', $messages));
    }
}