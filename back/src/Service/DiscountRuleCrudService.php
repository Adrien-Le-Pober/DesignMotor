<?php

namespace App\Service;

use App\Entity\DiscountRule;
use App\Entity\DiscountRuleAction;
use App\Entity\DiscountRuleCondition;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DiscountRuleRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class DiscountRuleCrudService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DiscountRuleRepository $discountRuleRepository
    ) { }

    public function getDiscountRules(): array
    {
        $serializer = new Serializer([new ObjectNormalizer()]);
        $discountRules = $this->discountRuleRepository->findAll();

        $serializedDiscountRules = $serializer->normalize(
            $discountRules,
            null,
            [
                AbstractNormalizer::GROUPS => 'discount_rule',
                AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'name',
                    'description',
                    'discountRuleConditions' => ['id', 'type', 'value'],
                    'discountRuleActions' => ['id', 'type', 'value']
                ]
            ]
        );

        return $this->transformDiscountRules($serializedDiscountRules);
    }

    private function transformDiscountRules(array $discountRules): array
    {
        return array_map(function ($rule) {
            $rule['conditions'] = $rule['discountRuleConditions'];
            unset($rule['discountRuleConditions']);
            $rule['actions'] = $rule['discountRuleActions'];
            unset($rule['discountRuleActions']);
            return $rule;
        }, $discountRules);
    }

    public function createDiscountRule(array $data): DiscountRule
    {
        $discountRule = (new DiscountRule())->setName($data['name']);

        if (isset($data['description'])) {
            $discountRule->setDescription($data['description']);
        }

        $this->handleConditions($discountRule, $data['conditions']);
        $this->handleActions($discountRule, $data['actions']);

        $this->entityManager->persist($discountRule);
        $this->entityManager->flush();

        return $discountRule;
    }

    public function updateDiscountRule(DiscountRule $discountRule, array $data): DiscountRule
    {
        $discountRule->setName($data['name']);

        if (isset($data['description'])) {
            $discountRule->setDescription($data['description']);
        }

        $this->handleConditions($discountRule, $data['conditions']);
        $this->handleActions($discountRule, $data['actions']);

        $this->entityManager->persist($discountRule);
        $this->entityManager->flush();

        return $discountRule;
    }

    private function handleConditions(DiscountRule $discountRule, array $conditions)
    {
        $existingConditions = $discountRule->getDiscountRuleConditions();
        $existingConditionIds = [];
        foreach ($existingConditions as $condition) {
            $existingConditionIds[$condition->getId()] = $condition;
        }

        foreach ($conditions as $conditionData) {
            $conditionId = $conditionData['id'] ?? null;

            if ($conditionId && isset($existingConditionIds[$conditionId])) {
                $condition = $existingConditionIds[$conditionId]
                    ->setType($conditionData['type'])
                    ->setValue($conditionData['value']);
                
                $this->entityManager->persist($condition);
                unset($existingConditionIds[$conditionId]);
            } else {
                $newCondition = (new DiscountRuleCondition())
                    ->setType($conditionData['type'])
                    ->setValue($conditionData['value']);
                
                $discountRule->addDiscountRuleCondition($newCondition);
                $this->entityManager->persist($newCondition);
            }
        }

        foreach ($existingConditionIds as $condition) {
            $discountRule->removeDiscountRuleCondition($condition);
            $this->entityManager->remove($condition);
        }
    }

    private function handleActions(DiscountRule $discountRule, array $actions)
    {
        $existingActions = $discountRule->getDiscountRuleActions();
        $existingActionIds = [];
        foreach ($existingActions as $action) {
            $existingActionIds[$action->getId()] = $action;
        }

        foreach ($actions as $actionData) {
            $actionId = $actionData['id'] ?? null;

            if ($actionId && isset($existingActionIds[$actionId])) {
                $action = $existingActionIds[$actionId]
                    ->setType($actionData['type'])
                    ->setValue($actionData['value']);
                
                $this->entityManager->persist($action);
                unset($existingActionIds[$actionId]);
            } else {
                $newAction = (new DiscountRuleAction())
                    ->setType($actionData['type'])
                    ->setValue($actionData['value']);
                
                $discountRule->addDiscountRuleAction($newAction);
                $this->entityManager->persist($newAction);
            }
        }

        foreach ($existingActionIds as $action) {
            $discountRule->removeDiscountRuleAction($action);
            $this->entityManager->remove($action);
        }
    }
}