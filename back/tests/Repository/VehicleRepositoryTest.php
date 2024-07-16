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
        self::bootKernel();

        $this->makeAssertions(
            method: 'findElectricCars',
            count: 1,
            motorizationName: 'Electrique',
            typeName: 'Voiture'
        );
    }

    public function testFindPetrolCars(): void
    {
        self::bootKernel();

        $this->makeAssertions(
            method: 'findPetrolCars',
            count: 2,
            motorizationName: 'Essence',
            typeName: 'Voiture'
        );
    }

    public function testFindEletricScooters(): void
    {
        self::bootKernel();

        $this->makeAssertions(
            method: 'findElectricScooters',
            count: 0,
            motorizationName: 'Electrique',
            typeName: 'Scooter',
            isEmpty: true
        );
    }

    public function testFindPetrolScooters(): void
    {
        $kernel = self::bootKernel();

        $this->makeAssertions(
            method: 'findPetrolScooters',
            count: 1,
            motorizationName: 'Essence',
            typeName: 'Scooter'
        );
    }

    public function testFindDetailsById(): void
    {
        $kernel = self::bootKernel();

        $vehicle = static::getContainer()
            ->get(VehicleRepository::class)
            ->findDetailsById(1);
        
        $this->assertIsArray($vehicle);
        $this->assertNotEmpty($vehicle);

        $this->assertArrayHasKey('id', $vehicle);
        $this->assertArrayHasKey('price', $vehicle);
        $this->assertArrayHasKey('power', $vehicle);
        $this->assertArrayHasKey('space', $vehicle);
        $this->assertArrayHasKey('image', $vehicle);
        $this->assertArrayHasKey('description', $vehicle);
        $this->assertArrayHasKey('brand', $vehicle);
        $this->assertArrayHasKey('brandDescription', $vehicle);
        $this->assertArrayHasKey('model', $vehicle);
        $this->assertArrayHasKey('modelDescription', $vehicle);
        $this->assertArrayHasKey('motorization', $vehicle);
        $this->assertArrayHasKey('color', $vehicle);

        $this->assertNotEmpty($vehicle['id']);
        $this->assertNotEmpty($vehicle['price']);
        $this->assertNotEmpty($vehicle['power']);
        $this->assertNotEmpty($vehicle['brand']);
        $this->assertNotEmpty($vehicle['model']);
        $this->assertNotEmpty($vehicle['motorization']);
        $this->assertNotEmpty($vehicle['color']);
    }

    private function makeAssertions(
        string $method,
        int $count,
        string $motorizationName,
        string $typeName,
        bool $isEmpty = false
    ) {
        $vehicles = static::getContainer()
            ->get(VehicleRepository::class)
            ->$method($filters = []);

        $this->assertIsArray($vehicles);
        !$isEmpty ? $this->assertNotEmpty($vehicles) : $this->assertEmpty($vehicles);
        $this->assertCount($count, $vehicles);

        foreach ($vehicles as $vehicle) {
            $this->assertArrayHasKey('id', $vehicle);
            $this->assertArrayHasKey('price', $vehicle);
            $this->assertArrayHasKey('imagePath', $vehicle);
            $this->assertArrayHasKey('brandName', $vehicle);
            $this->assertArrayHasKey('modelName', $vehicle);
            $this->assertArrayHasKey('motorizationName', $vehicle);
            $this->assertArrayHasKey('typeName', $vehicle);
        }

        foreach ($vehicles as $vehicle) {
            $this->assertNotEmpty($vehicle['id']);
            $this->assertNotEmpty($vehicle['price']);
            $this->assertNotEmpty($vehicle['brandName']);
            $this->assertNotEmpty($vehicle['modelName']);
            $this->assertNotEmpty($vehicle['motorizationName']);
            $this->assertNotEmpty($vehicle['typeName']);
        }

        foreach ($vehicles as $vehicle) {
            $this->assertEquals($motorizationName, $vehicle['motorizationName']);
            $this->assertEquals($typeName, $vehicle['typeName']);
        }
    }
}
