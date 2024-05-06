<?php

namespace App\Controller;

use App\Service\VehicleAbstractFactoryService;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CatalogController extends AbstractController
{
    #[Route('/catalog', name: 'catalog')]
    public function index(VehicleAbstractFactoryService $vehicleAbstractFactoryService): JsonResponse
    {
        return $this->json($vehicleAbstractFactoryService->getVehicles());
    }
}
