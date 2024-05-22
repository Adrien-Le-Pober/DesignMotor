<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use App\Trait\Base64ImageTrait;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VehicleController extends AbstractController
{
    use Base64ImageTrait;

    #[Route('/vehicle/{id}', name: 'app_show_vehicle')]
    public function show(
        int $id,
        VehicleRepository $vehicleRepository
    ): JsonResponse {
        $vehicle = $vehicleRepository->findDescriptionById($id);

        if($vehicle['image']) {
            $vehicle['image'] = $this->getBase64Image($vehicle['image']);
        }
        
        return $this->json($vehicle);
    }
}
