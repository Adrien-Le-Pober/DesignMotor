<?php

namespace App\Controller;

use App\Entity\DiscountRule;
use function PHPUnit\Framework\isEmpty;
use App\Service\DiscountRuleCrudService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DiscountRuleRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DiscountRuleController extends AbstractController
{
    #[Route('/discount-rules', name: 'app_discount-rules', methods: ['GET'])]
    public function index(DiscountRuleCrudService $discountRuleCrudService): JsonResponse
    {
        return $this->json($discountRuleCrudService->getDiscountRules());
    }

    #[Route('/new-discount-rule', name: 'app_new_discount_rule', methods: ['POST'])]
    public function newDiscountRule(
        Request $request,
        DiscountRuleCrudService $discountRuleCrudService
    ) {
        $data = json_decode($request->getContent(), true);

        if (isset($data['name']) &&
            isset($data['conditions']) && !empty($data['conditions']) &&
            isset($data['actions']) && !empty($data['actions'])
        ) {
            $discountRuleCrudService->createDiscountRule($data);

            return $this->json("La nouvelle règle de réduction vient d'être appliquée");
        } else {
            return $this->json(['error' => 'Invalid data'], 400);
        }
    }

    #[Route('/edit-discount-rule/{discountRule}', name: 'app_edit_discount_rule', methods: ['PUT'])]
    public function editDiscountRule(
        Request $request,
        DiscountRuleCrudService $discountRuleCrudService,
        DiscountRule $discountRule
    ) {
        $data = json_decode($request->getContent(), true);

        if (isset($data['name']) &&
            isset($data['conditions']) &&
            isset($data['actions'])
        ) {
            $discountRuleCrudService->updateDiscountRule($discountRule, $data);

            return $this->json("La nouvelle règle de réduction vient d'être modifiée");
        } else {
            return $this->json(['error' => 'Invalid data'], 400);
        }
    }

    #[Route('/delete-discount-rule/{discountRule}', name: 'app_delete_discount_rule', methods: ['DELETE'])]
    public function deleteDiscountRule(
        DiscountRule $discountRule,
        EntityManagerInterface $entityManager
    ) {
        $entityManager->remove($discountRule);
        $entityManager->flush();

        return $this->json("La nouvelle règle de réduction vient d'être supprimée");
    }
}
