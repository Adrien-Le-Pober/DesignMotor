<?php

namespace App\Interpreter;

interface ActionInterface
{
    public function execute(array &$context);
}