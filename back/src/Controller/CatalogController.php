<?php

namespace App\Controller;

use App\AbstractFactory\ElectricVehicleFactory;
use App\AbstractFactory\PetrolVehicleFactory;
use App\Repository\VehicleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class CatalogController extends AbstractController
{
    #[Route('/catalog', name: 'catalog')]
    public function index(VehicleRepository $vehicleRepository): JsonResponse
    {
        $petrolVehicleFactory = new PetrolVehicleFactory();
        $electricVehicleFactory = new ElectricVehicleFactory();

        $vehicles = [];

        foreach($vehicleRepository->findElectricCars() as $electricCar) {
            if(!empty($electricCar)) {
                $vehicles[] = $electricVehicleFactory->createCar(
                    $electricCar["id"],
                    $electricCar["brandName"],
                    [$electricCar["colorName"]],
                    $electricCar["power"],
                    $electricCar["space"]
                );
            }
        }

        foreach($vehicleRepository->findPetrolCars() as $petrolCar) {
            if(!empty($petrolCar)) {
                $vehicles[] = $petrolVehicleFactory->createCar(
                    $petrolCar["id"],
                    $petrolCar["brandName"],
                    [$petrolCar["colorName"]],
                    $petrolCar["power"],
                    $petrolCar["space"]
                );
            }
        }

        foreach($vehicleRepository->findElectricScooters() as $electricScooter) {
            if(!empty($eletricScooter)) {
                $vehicles[] = $electricVehicleFactory->createScooter(
                    $electricScooter["id"],
                    $electricScooter["brandName"],
                    [$electricScooter["colorName"]],
                    $electricScooter["power"],
                );
            }
        }

        foreach($vehicleRepository->findPetrolScooters() as $petrolScooter) {
            if(!empty($petrolScooter)) {
                $vehicles[] = $electricVehicleFactory->createScooter(
                    $petrolScooter["id"],
                    $petrolScooter["brandName"],
                    [$petrolScooter["colorName"]],
                    $petrolScooter["power"],
                );
            }
        }


        return $this->json($vehicles);
    }
}
