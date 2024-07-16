<?php

namespace App\Tests\Controller;

use App\Entity\Type;
use App\Entity\Brand;
use App\Entity\Model;
use App\Entity\Vehicle;
use App\Proxy\VideoProxy;
use App\Entity\Motorization;
use App\Repository\BrandRepository;
use App\Service\DiscountRuleService;
use App\Service\VehicleAbstractFactoryService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CatalogControllerTest extends WebTestCase
{
    private $vehicleAbstractFactoryService;
    private $discountRuleService;
    private $brandRepository;
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();

        $this->vehicleAbstractFactoryService = $this->createMock(VehicleAbstractFactoryService::class);
        $this->discountRuleService = $this->createMock(DiscountRuleService::class);
        $this->brandRepository = $this->createMock(BrandRepository::class);

        $container->set(VehicleAbstractFactoryService::class, $this->vehicleAbstractFactoryService);
        $container->set(DiscountRuleService::class, $this->discountRuleService);
        $container->set(BrandRepository::class, $this->brandRepository);
    }

    public function testIndex(): void
    {
        $this->vehicleAbstractFactoryService
            ->expects($this->once())
            ->method('getVehicles')
            ->willReturn([]);

        $this->client->request('GET', '/catalog');

        $this->assertResponseIsSuccessful();
    }

    public function testIndexWithBrandFilter(): void
    {
        $brand = new Brand();
        $brand->setName('BrandA');

        $type = new Type();
        $type->setName('TypeA');

        $motorization = new Motorization();
        $motorization->setName('MotorizationA');

        $model = new Model();
        $model->setName('ModelA');

        $vehicle1 = [
            'id' => 1,
            'brandName' => 'BrandA',
            'modelName' => 'ModelA',
            'imagePath' => 'path/to/image1.jpg',
            'price' => 30000.0,
            'soldedPrice' => 30000.0,
        ];

        $vehicle2 = [
            'id' => 2,
            'brandName' => 'BrandA',
            'modelName' => 'ModelA',
            'imagePath' => 'path/to/image2.jpg',
            'price' => 25000.0,
            'soldedPrice' => 25000.0,
        ];

        $vehicles = [$vehicle1, $vehicle2];

        $this->vehicleAbstractFactoryService
            ->expects($this->once())
            ->method('getVehicles')
            ->with(['brand' => 'BrandA'])
            ->willReturn($vehicles);

        $this->discountRuleService
            ->expects($this->exactly(2))
            ->method('applyRules')
            ->withConsecutive(
                [$this->equalTo($vehicle1)],
                [$this->equalTo($vehicle2)]
            );

        $this->client->request('GET', '/catalog', ['brand' => 'BrandA']);

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonString(
            json_encode($vehicles),
            $this->client->getResponse()->getContent()
        );
    }

    public function testGetVideo(): void
    {
        $brand = new Brand();
        $brand->setName('BrandA');

        $type = new Type();
        $type->setName('TypeA');

        $motorization = new Motorization();
        $motorization->setName('MotorizationA');

        $model = new Model();
        $model->setName('ModelA');

        $vehicle = (new Vehicle())
            ->setBrand($brand)
            ->setType($type)
            ->setMotorization($motorization)
            ->setModel($model)
            ->setPower('100')
            ->setSpace('5')
            ->setImagePath('path/to/image.jpg')
            ->setVideoPath('path/to/video.mp4')
            ->setPrice(30000.0)
            ->setDescription('Description of Vehicle');

        $this->client->request('GET', '/vehicle/1/video');

        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertInstanceOf(BinaryFileResponse::class, $response);
        $this->assertEquals('video/mp4', $response->headers->get('Content-Type'));
    }

    public function testGetBrands(): void
    {
        $brands = ['BrandA', 'BrandB', 'BrandC'];

        $this->brandRepository
            ->expects($this->once())
            ->method('findAllBrandNames')
            ->willReturn($brands);

        $this->client->request('GET', '/catalog/brands');

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonString(
            json_encode($brands),
            $this->client->getResponse()->getContent()
        );
    }
}