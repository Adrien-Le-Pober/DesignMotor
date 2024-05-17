<?php

namespace App\Service;

use App\Entity\DiscountRule;
use App\Interpreter\DateExpression;
use App\Interpreter\DiscountAction;
use App\Interpreter\BrandExpression;
use Doctrine\ORM\EntityManagerInterface;

class DiscountRuleService
{
    private array $context = [];
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function applyRules(array &$vehicleData)
    {
        $this->makeContext($vehicleData);
        $this->checkRulesAndExecute($vehicleData);
    }

    public function makeContext(array $vehicleData)
    {
        $this->context['date'] = new \DateTime();
        $this->context['id'] = $vehicleData['id'];
        $this->context['price'] = $vehicleData['price'];
        $this->context['brand'] = $vehicleData['brand'];
    }

    public function checkRulesAndExecute(array &$vehicleData)
    {
        foreach ($this->getAllRules() as $rule) {
            $conditionsMet = true;
            foreach ($rule['conditions'] as $condition) {
                if (!$condition->interpret($this->context)) {
                    $conditionsMet = false;
                    break;
                }
            }
        
            if ($conditionsMet) {
                foreach ($rule['actions'] as $action) {
                    $action->execute($vehicleData);
                }
            }
        }
    }

    private function getAllRules()
    {
        $rules = $this->entityManager->getRepository(DiscountRule::class)->findAll();
        $parsedRules = [];

        foreach ($rules as $rule) {
            $conditions = [];
            foreach ($rule->getDiscountRuleConditions() as $condition) {
                $conditions[] = $this->parseCondition($condition);
            }

            $actions = [];
            foreach ($rule->getDiscountRuleActions() as $action) {
                $actions[] = $this->parseAction($action);
            }

            $parsedRules[] = [
                'conditions' => $conditions,
                'actions' => $actions,
            ];
        }

        return $parsedRules;
    }

    private function parseCondition($condition)
    {
        switch ($condition->getType()) {
            case 'day_of_week':
                return new DateExpression('day_of_week', $condition->getValue());
            case 'brand':
                return new BrandExpression('brand', $condition->getValue());
            default:
                throw new \Exception("Unknown condition type: " . $condition->getType());
        }
    }

    private function parseAction($action)
    {
        switch ($action->getType()) {
            case 'discount':
                return new DiscountAction($action->getValue());
            // Ajoutez d'autres types d'actions ici
            default:
                throw new \Exception("Unknown action type: " . $action->getType());
        }
    }
}