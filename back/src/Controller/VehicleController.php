<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VehicleController extends AbstractController
{
    #[Route('/vehicle/{vehicle}', name: 'app_show_vehicle')]
    public function show(
        Vehicle $vehicle,
        VehicleRepository $vehicleRepository
    ): JsonResponse {
        return $this->json('');
    }
}
