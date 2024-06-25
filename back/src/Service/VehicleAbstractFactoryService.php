<?php

namespace App\Service;

use App\Repository\VehicleRepository;
use App\AbstractFactory\PetrolVehicleFactory;
use App\AbstractFactory\ElectricVehicleFactory;

class VehicleAbstractFactoryService
{
    private array $vehicles = [];
    private array $filters = [];

    public function __construct(
        private PetrolVehicleFactory $petrolVehicleFactory,
        private ElectricVehicleFactory $electricVehicleFactory,
        private VehicleRepository $vehicleRepository
    ) { }

    public function getVehicles(array $filters): array
    {
        $this->filters = $filters;
        $this->makeElectricCars();
        $this->makePetrolCars();
        $this->makeElectricScooters();
        $this->makePetrolScooters();
        return $this->vehicles;
    }

    private function makeElectricCars(): void
    {
        foreach($this->vehicleRepository->findElectricCars($this->filters) as $electricCar) {
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
        foreach($this->vehicleRepository->findPetrolCars($this->filters) as $petrolCar) {
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
        foreach($this->vehicleRepository->findElectricScooters($this->filters) as $electricScooter) {
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
        foreach($this->vehicleRepository->findPetrolScooters($this->filters) as $petrolScooter) {
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