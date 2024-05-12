<?php

namespace App\Service;

use App\Repository\VehicleRepository;
use App\AbstractFactory\PetrolVehicleFactory;
use App\AbstractFactory\ElectricVehicleFactory;

class VehicleAbstractFactoryService
{
    private array $vehicles = [];

    public function __construct(
        private PetrolVehicleFactory $petrolVehicleFactory,
        private ElectricVehicleFactory $electricVehicleFactory,
        private VehicleRepository $vehicleRepository
    ) { }

    public function getVehicles(): array
    {
        $this->makeElectricCars();
        $this->makePetrolCars();
        $this->makeElectricScooters();
        $this->makePetrolScooters();
        return $this->vehicles;
    }

    private function makeElectricCars(): void
    {
        foreach($this->vehicleRepository->findElectricCars() as $electricCar) {
            if(!empty($electricCar)) {
                $this->vehicles[] = $this->electricVehicleFactory->createCar(
                    $electricCar["id"],
                    $electricCar["brandName"],
                    $electricCar["modelName"],
                    [$electricCar["colorName"]],
                    $electricCar["power"],
                    $electricCar["space"],
                    $electricCar["imagePath"],
                    $electricCar["price"]
                )->getVehicleInfos();
            }
        }
    }

    private function makePetrolCars(): void
    {
        foreach($this->vehicleRepository->findPetrolCars() as $petrolCar) {
            if(!empty($petrolCar)) {
                $this->vehicles[] = $this->petrolVehicleFactory->createCar(
                    $petrolCar["id"],
                    $petrolCar["brandName"],
                    $petrolCar["modelName"],
                    [$petrolCar["colorName"]],
                    $petrolCar["power"],
                    $petrolCar["space"],
                    $petrolCar["imagePath"],
                    $petrolCar["price"]
                )->getVehicleInfos();
            }
        }
    }

    private function makeElectricScooters(): void
    {
        foreach($this->vehicleRepository->findElectricScooters() as $electricScooter) {
            if(!empty($eletricScooter)) {
                $this->vehicles[] = $this->electricVehicleFactory->createScooter(
                    $electricScooter["id"],
                    $electricScooter["brandName"],
                    $electricScooter["modelName"],
                    [$electricScooter["colorName"]],
                    $electricScooter["power"],
                    $electricScooter["imagePath"],
                    $electricScooter["price"],
                )->getVehicleInfos();
            }
        }
    }

    private function makePetrolScooters(): void
    {
        foreach($this->vehicleRepository->findPetrolScooters() as $petrolScooter) {
            if(!empty($petrolScooter)) {
                $this->vehicles[] = $this->electricVehicleFactory->createScooter(
                    $petrolScooter["id"],
                    $petrolScooter["brandName"],
                    $petrolScooter["modelName"],
                    [$petrolScooter["colorName"]],
                    $petrolScooter["power"],
                    $petrolScooter["imagePath"],
                    $petrolScooter["price"]
                )->getVehicleInfos();
            }
        }
    }
}