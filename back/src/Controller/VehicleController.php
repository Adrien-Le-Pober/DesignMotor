<?php

namespace App\Controller;

use App\Trait\Base64ImageTrait;
use App\Service\DiscountRuleService;
use App\Repository\VehicleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VehicleController extends AbstractController
{
    use Base64ImageTrait;

    #[Route('/vehicle/{id}', name: 'app_show_vehicle')]
    public function show(
        int $id,
        VehicleRepository $vehicleRepository,
        DiscountRuleService $discountRuleService
    ): JsonResponse {
        $vehicle = $vehicleRepository->findDetailsById($id);

        if ($vehicle['image']) {
            $vehicle['image'] = $this->getBase64Image($vehicle['image']);
        }

        $discountRuleService->applyRules($vehicle);
        
        return $this->json($vehicle);
    }

    #[Route('/search-vehicles', methods: ['GET'])]
    public function search(Request $request, VehicleRepository $vehicleRepository): JsonResponse
    {
        $query = $request->query->get('q', '');
        $vehicles = $vehicleRepository->findByModelOrBrand($query, 5);

        return $this->json($vehicles);
    }
}
