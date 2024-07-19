<?php

namespace App\Tests\Entity;

use App\Entity\DiscountRule;
use App\Entity\DiscountRuleCondition;
use App\Entity\DiscountRuleAction;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class DiscountRuleEntityTest extends KernelTestCase
{
    private $discountRuleCondition;
    private $discountRuleAction;

    public function getEntity(): DiscountRule
    {
        $this->discountRuleCondition = (new DiscountRuleCondition)
            ->setType('day_of_week')
            ->setValue(['Monday']);

        $this->discountRuleAction = (new DiscountRuleAction)
            ->setType('discount')
            ->setValue(50);

        return (new DiscountRule)
            ->setName('Valid Name')
            ->setDescription('Valid Description')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable())
            ->addDiscountRuleCondition($this->discountRuleCondition)
            ->addDiscountRuleAction($this->discountRuleAction);
    }

    public function testValidDiscountRule(): void
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidName(): void
    {
        $this->assertHasErrors($this->getEntity()->setName(''), 1);
        $this->assertHasErrors($this->getEntity()->setName(str_repeat('a', 256)), 1);
    }

    public function testInvalidDescription(): void
    {
        $this->assertHasErrors($this->getEntity()->setDescription(str_repeat('a', 256)), 1);
    }

    public function testEmptyDiscountRuleConditions(): void
    {
        $this->assertHasErrors($this->getEntity()->removeDiscountRuleCondition($this->discountRuleCondition), 1);
    }

    public function testEmptyDiscountRuleActions(): void
    {
        $this->assertHasErrors($this->getEntity()->removeDiscountRuleAction($this->discountRuleAction), 1);
    }

    public function assertHasErrors(DiscountRule $discountRule, int $number = 0): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());

        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($discountRule);
        $messages = [];

        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }

        $this->assertCount($number, $errors, implode(', ', $messages));
    }
}