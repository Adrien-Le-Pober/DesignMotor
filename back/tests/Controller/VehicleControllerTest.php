<?php

namespace App\Tests\Controller;

use App\Service\DiscountRuleService;
use App\Repository\VehicleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VehicleControllerTest extends WebTestCase
{
    private $client;

    private $vehicleRepository;
    private $discountRuleService;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->vehicleRepository = $this->createMock(VehicleRepository::class);
        $this->discountRuleService = $this->createMock(DiscountRuleService::class);

        $this->client->getContainer()->set('App\Repository\VehicleRepository', $this->vehicleRepository);
        $this->client->getContainer()->set('App\Service\DiscountRuleService', $this->discountRuleService);
    }

    public function testShowVehicleFound()
    {
        $id = 1;
        $vehicleData = [
            'id' => $id,
            'model' => 'Test Model',
            'brand' => 'Test Brand',
            'image' => null
        ];

        $this->vehicleRepository->expects($this->once())
            ->method('findDetailsById')
            ->with($id)
            ->willReturn($vehicleData);

        $this->discountRuleService->expects($this->once())
            ->method('applyRules')
            ->with($vehicleData);

        $this->client->request('GET', '/vehicle/' . $id);

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $expectedVehicleData = $vehicleData;

        $this->assertJsonStringEqualsJsonString(
            json_encode($expectedVehicleData),
            $response->getContent()
        );
    }

    public function testSearchVehiclesFound()
    {
        $query = 'test';
        $vehicles = [
            ['id' => 1, 'model' => 'Test Model 1', 'brand' => 'Test Brand'],
            ['id' => 2, 'model' => 'Test Model 2', 'brand' => 'Test Brand']
        ];

        $this->vehicleRepository->expects($this->once())
            ->method('findByModelOrBrand')
            ->with($query, 5)
            ->willReturn($vehicles);

        $this->client->request('GET', '/search-vehicles?q=' . $query);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode($vehicles),
            $response->getContent()
        );
    }
}