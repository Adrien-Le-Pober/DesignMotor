<?php

namespace App\Controller\Admin;

use App\Entity\DiscountRule;
use App\Service\DiscountRuleCrudService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        DiscountRuleCrudService $discountRuleCrudService,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $discountRuleCount = $entityManager->getRepository(DiscountRule::class)->count([]);
        if ($discountRuleCount >= 3) {
            return new JsonResponse(['errorMessage' => 'Le nombre limite de règles actives a été atteint'], JsonResponse::HTTP_BAD_REQUEST);
        };

        if (isset($data['name']) &&
            isset($data['conditions']) && !empty($data['conditions']) &&
            isset($data['actions']) && !empty($data['actions'])
        ) {
            $discountRuleCrudService->createDiscountRule($data);

            return new JsonResponse(['successMessage' => "La nouvelle règle de réduction vient d'être appliquée"], JsonResponse::HTTP_OK);
        } else {
            return new JsonResponse(['errorMessage' => 'Les données sont invalides'], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/edit-discount-rule/{discountRule}', name: 'app_edit_discount_rule', methods: ['PUT'])]
    public function editDiscountRule(
        Request $request,
        DiscountRuleCrudService $discountRuleCrudService,
        DiscountRule $discountRule
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (isset($data['name']) &&
            isset($data['conditions']) &&
            isset($data['actions'])
        ) {
            $discountRuleCrudService->updateDiscountRule($discountRule, $data);

            return new JsonResponse(['successMessage' => "La règle de réduction vient d'être modifiée"], JsonResponse::HTTP_OK);
        } else {
            return new JsonResponse(['errorMessage' => 'Les données sont invalides'], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/delete-discount-rule/{discountRule}', name: 'app_delete_discount_rule', methods: ['DELETE'])]
    public function deleteDiscountRule(
        DiscountRule $discountRule,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $entityManager->remove($discountRule);
        $entityManager->flush();

        return new JsonResponse(['successMessage' => "La règle de réduction vient d'être supprimée"], JsonResponse::HTTP_OK);
    }
}
