<?php

namespace App\Service;

use App\Repository\VehicleRepository;
use App\AbstractFactory\PetrolVehicleFactory;
use App\AbstractFactory\ElectricVehicleFactory;

class VehicleAbstractFactoryService
{
    private array $vehicles = [];
    private array $filters = [];
    private int $vehiclesCount = 0;

    public function __construct(
        private PetrolVehicleFactory $petrolVehicleFactory,
        private ElectricVehicleFactory $electricVehicleFactory,
        private VehicleRepository $vehicleRepository
    ) { }

    public function getVehicles(array $filters): array
    {
        $this->filters = $filters;
        $this->vehicles = [];
        $this->vehiclesCount = 0;

        $this->makeElectricCars();
        $this->makePetrolCars();
        $this->makeElectricScooters();
        $this->makePetrolScooters();

        return [
            'vehicles' => $this->vehicles,
            'total' => $this->vehiclesCount,
        ];
    }

    private function makeElectricCars(): void
    {
        $result = $this->vehicleRepository->findElectricCars($this->filters);

        $this->vehiclesCount += $result['total'];

        foreach($result['vehicles'] as $electricCar) {
            if(!empty($electricCar)) {
                $this->vehicles[] = $this->electricVehicleFactory->createCar(
                    $electricCar["id"],
                    $electricCar["brandName"],
                    $electricCar["modelName"],
                    $electricCar["imagePath"],
                    $electricCar["price"],
                    $electricCar["soldedPrice"],
                )->getVehicleInfos();
            }
        }
    }

    private function makePetrolCars(): void
    {
        $result = $this->vehicleRepository->findPetrolCars($this->filters);

        $this->vehiclesCount += $result['total'];

        foreach($result['vehicles'] as $petrolCar) {
            if(!empty($petrolCar)) {
                $this->vehicles[] = $this->petrolVehicleFactory->createCar(
                    $petrolCar["id"],
                    $petrolCar["brandName"],
                    $petrolCar["modelName"],
                    $petrolCar["imagePath"],
                    $petrolCar["price"],
                    $petrolCar["soldedPrice"],
                )->getVehicleInfos();
            }
        }
    }

    private function makeElectricScooters(): void
    {
        $result = $this->vehicleRepository->findElectricScooters($this->filters);

        $this->vehiclesCount += $result['total'];

        foreach($result['vehicles'] as $electricScooter) {
            if(!empty($eletricScooter)) {
                $this->vehicles[] = $this->electricVehicleFactory->createScooter(
                    $electricScooter["id"],
                    $electricScooter["brandName"],
                    $electricScooter["modelName"],
                    $electricScooter["imagePath"],
                    $electricScooter["price"],
                    $electricScooter["soldedPrice"],
                )->getVehicleInfos();
            }
        }
    }

    private function makePetrolScooters(): void
    {
        $result = $this->vehicleRepository->findPetrolScooters($this->filters);

        $this->vehiclesCount += $result['total'];

        foreach($result['vehicles'] as $petrolScooter) {
            if(!empty($petrolScooter)) {
                $this->vehicles[] = $this->electricVehicleFactory->createScooter(
                    $petrolScooter["id"],
                    $petrolScooter["brandName"],
                    $petrolScooter["modelName"],
                    $petrolScooter["imagePath"],
                    $petrolScooter["price"],
                    $petrolScooter["soldedPrice"],
                )->getVehicleInfos();
            }
        }
    }
}