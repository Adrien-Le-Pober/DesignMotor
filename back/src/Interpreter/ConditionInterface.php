<?php

namespace App\Interpreter;

interface ConditionInterface
{
    /**
     * Interprète la condition dans le contexte donné.
     *
     * @param array $context Le contexte contenant les informations nécessaires pour évaluer la condition.
     * @return bool Retourne true si la condition est remplie, sinon false.
     */
    public function interpret(array $context): bool;
}