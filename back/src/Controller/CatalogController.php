<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Proxy\VideoProxy;
use App\Repository\BrandRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Service\VehicleAbstractFactoryService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CatalogController extends AbstractController
{
    #[Route('/catalog', name: 'catalog')]
    public function index(
        VehicleAbstractFactoryService $vehicleAbstractFactoryService,
        Request $request
    ): JsonResponse {
        $filters = [];

        if ($request->query->has('brand')) {
            $filters['brand'] = $request->query->get('brand');
        }

        return $this->json($vehicleAbstractFactoryService->getVehicles($filters));
    }

    #[Route('vehicle/{vehicle}/video', name: 'vehicule-video')]
    public function getVideo(Vehicle $vehicle): Response
    {
        $videoProxy = new VideoProxy();
        $video = $videoProxy->loadVideo($vehicle->getVideoPath());

        $response = new BinaryFileResponse($video);
        $response->headers->set('Content-Type', 'video/mp4');

        return $response;
    }

    #[Route('/catalog/brands', name: 'catalog-brands')]
    public function getBrands(BrandRepository $brandRepository): JsonResponse
    {
        return $this->json($brandRepository->findAllBrandNames());
    }
}
