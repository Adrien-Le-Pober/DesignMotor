<?php

namespace App\Interpreter;

use App\Interpreter\ConditionInterface;

class BrandExpression implements ConditionInterface
{
    public function __construct(
        private string $operator,
        private array $value
    ) { }

    public function interpret(array $context): bool
    {
        if (!isset($context['brand'])) {
            throw new \Exception("Brand is not provided in the context");
        }

        $brand = $context['brand'];

        if ($this->operator === 'brand') {
            return in_array($brand, $this->value);
        }
    }
}