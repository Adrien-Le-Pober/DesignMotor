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
                    $electricCar["modelName"],
                    [$electricCar["colorName"]],
                    $electricCar["power"],
                    $electricCar["space"]
                )->getVehicleInfos();
            }
        }

        foreach($vehicleRepository->findPetrolCars() as $petrolCar) {
            if(!empty($petrolCar)) {
                $vehicles[] = $petrolVehicleFactory->createCar(
                    $petrolCar["id"],
                    $petrolCar["brandName"],
                    $petrolCar["modelName"],
                    [$petrolCar["colorName"]],
                    $petrolCar["power"],
                    $petrolCar["space"]
                )->getVehicleInfos();
            }
        }

        foreach($vehicleRepository->findElectricScooters() as $electricScooter) {
            if(!empty($eletricScooter)) {
                $vehicles[] = $electricVehicleFactory->createScooter(
                    $electricScooter["id"],
                    $electricScooter["brandName"],
                    $electricScooter["modelName"],
                    [$electricScooter["colorName"]],
                    $electricScooter["power"],
                )->getVehicleInfos();
            }
        }

        foreach($vehicleRepository->findPetrolScooters() as $petrolScooter) {
            if(!empty($petrolScooter)) {
                $vehicles[] = $electricVehicleFactory->createScooter(
                    $petrolScooter["id"],
                    $petrolScooter["brandName"],
                    $petrolScooter["modelName"],
                    [$petrolScooter["colorName"]],
                    $petrolScooter["power"],
                )->getVehicleInfos();
            }
        }

        return $this->json($vehicles);
    }
}
