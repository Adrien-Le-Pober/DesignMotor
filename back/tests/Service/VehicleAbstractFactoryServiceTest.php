<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Repository\VehicleRepository;
use App\AbstractFactory\PetrolVehicleFactory;
use App\Service\VehicleAbstractFactoryService;
use App\AbstractFactory\ElectricVehicleFactory;

class VehicleAbstractFactoryServiceTest extends TestCase
{
    public function testGetVehicles(): void
    {
        $vehicleRepositoryMock = $this->createMock(VehicleRepository::class);
        $petrolVehicleFactoryMock = $this->createMock(PetrolVehicleFactory::class);
        $electricVehicleFactoryMock = $this->createMock(ElectricVehicleFactory::class);

        $methods = [
            'findElectricCars',
            'findPetrolCars',
            'findElectricScooters',
            'findPetrolScooters',
        ];

        foreach($methods as $method) {
            $vehicleRepositoryMock
                ->expects($this->once())
                ->method($method)
                ->willReturn([
                    [
                        'id' => 1,
                        'brandName' => 'Renault',
                        'modelName' => 'Megane E-Tech',
                        'colorName' => ['blue'],
                        'power'=> '4',
                        'space' => '5',
                    ]
                ]);
        }

        $vehicleService = new VehicleAbstractFactoryService(
            $petrolVehicleFactoryMock,
            $electricVehicleFactoryMock,
            $vehicleRepositoryMock
        );

        $vehicles = $vehicleService->getVehicles();

        $this->assertIsArray($vehicles);
        $this->assertNotEmpty($vehicles);
    }

}
