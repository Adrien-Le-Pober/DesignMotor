<?php

namespace App\Tests\Repository;

use App\Repository\VehicleRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VehicleRepositoryTest extends KernelTestCase
{
    public function testCount(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());

        $vehicles = static::getContainer()->get(VehicleRepository::class)->count([]);

        $this->assertEquals(4, $vehicles);
    }

    public function testFindEletricCars(): void
    {
        $kernel = self::bootKernel();

        $this->makeAssertions(
            method: 'findElectricCars',
            count: 1,
            motorizationName: 'Electric',
            typeName: 'Car'
        );
    }

    public function testFindPetrolCars(): void
    {
        $kernel = self::bootKernel();

        $this->makeAssertions(
            method: 'findPetrolCars',
            count: 2,
            motorizationName: 'Petrol',
            typeName: 'Car'
        );
    }

    public function testFindEletricScooters(): void
    {
        $kernel = self::bootKernel();

        $this->makeAssertions(
            method: 'findElectricScooters',
            count: 0,
            motorizationName: 'Electric',
            typeName: 'Scooter',
            isEmpty: true,
            space: false
        );
    }

    public function testFindPetrolScooters(): void
    {
        $kernel = self::bootKernel();

        $this->makeAssertions(
            method: 'findPetrolScooters',
            count: 1,
            motorizationName: 'Petrol',
            typeName: 'Scooter',
            space: false
        );
    }

    private function makeAssertions(
        string $method,
        int $count,
        string $motorizationName,
        string $typeName,
        bool $isEmpty = false,
        bool $space = true
    ) {
        $vehicles = static::getContainer()
        ->get(VehicleRepository::class)
        ->$method();

        $this->assertIsArray($vehicles);
        !$isEmpty ? $this->assertNotEmpty($vehicles) : $this->assertEmpty($vehicles);
        $this->assertCount($count, $vehicles);

        foreach ($vehicles as $vehicle) {
            $this->assertArrayHasKey('id', $vehicle);
            $this->assertArrayHasKey('brandName', $vehicle);
            $this->assertArrayHasKey('modelName', $vehicle);
            $this->assertArrayHasKey('colorName', $vehicle);
            $this->assertArrayHasKey('power', $vehicle);
            $space ?? $this->assertArrayHasKey('space', $vehicle);
            $this->assertArrayHasKey('motorizationName', $vehicle);
            $this->assertArrayHasKey('typeName', $vehicle);
        }

        foreach ($vehicles as $vehicle) {
            $this->assertNotEmpty($vehicle['id']);
            $this->assertNotEmpty($vehicle['brandName']);
            $this->assertNotEmpty($vehicle['modelName']);
            $this->assertNotEmpty($vehicle['colorName']);
            $this->assertNotEmpty($vehicle['power']);
            $space ?? $this->assertNotEmpty($vehicle['space']);
            $this->assertNotEmpty($vehicle['motorizationName']);
            $this->assertNotEmpty($vehicle['typeName']);
        }

        foreach ($vehicles as $vehicle) {
            $this->assertEquals($motorizationName, $vehicle['motorizationName']);
            $this->assertEquals($typeName, $vehicle['typeName']);
        }
    }
}
