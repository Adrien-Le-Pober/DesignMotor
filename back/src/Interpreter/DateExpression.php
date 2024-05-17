<?php

namespace App\Interpreter;

use App\Interpreter\ConditionInterface;

class DateExpression implements ConditionInterface
{
    public function __construct(
        private string $operator,
        private array $value
    ) { }

    public function interpret(array $context): bool
    {
        if (!isset($context['date'])) {
            throw new \Exception("Date is not provided in the context");
        }

        $today = $context['date'];

        if ($this->operator === 'day_of_week') {
            return in_array($today->format('l'), $this->value);
        }
    }
}