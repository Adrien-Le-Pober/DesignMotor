<?php

namespace App\Tests\Entity;

use App\Entity\DiscountRuleCondition;
use App\Entity\DiscountRule;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class DiscountRuleConditionEntityTest extends KernelTestCase
{
    public function getEntity(): DiscountRuleCondition
    {
        $discountRule = new DiscountRule();

        return (new DiscountRuleCondition())
            ->setType('day_of_week')
            ->setValue(['Monday'])
            ->setDiscountRule($discountRule);
    }

    public function testValidDiscountRuleCondition(): void
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidType(): void
    {
        $this->assertHasErrors($this->getEntity()->setType('invalid_type'), 1);
    }

    public function testInvalidDiscountRule(): void
    {
        $this->assertHasErrors($this->getEntity()->setDiscountRule(null), 1);
    }

    public function assertHasErrors(DiscountRuleCondition $discountRuleCondition, int $number = 0): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());

        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($discountRuleCondition);
        $messages = [];

        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }

        $this->assertCount($number, $errors, implode(', ', $messages));
    }
}