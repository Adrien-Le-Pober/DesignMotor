<?php

namespace App\Tests\Entity;

use App\Entity\Type;
use App\Entity\Brand;
use App\Entity\Color;
use App\Entity\Model;
use DateTimeImmutable;
use App\Entity\Vehicle;
use App\Entity\Motorization;
use PHPUnit\Framework\TestCase;

class VehicleUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $brand = new Brand();
        $color = new Color();
        $type = new Type();
        $model = new Model();
        $motorization = new Motorization();
        $createdAt = new DateTimeImmutable();

        $vehicle = (new Vehicle)
            ->setPower('12')
            ->setSpace('15')
            ->setImagePath('somePath')
            ->setVideoPath('somePath')
            ->setPrice(10_000.50)
            ->setDescription('description')
            ->setBrand($brand)
            ->addColor($color)
            ->setType($type)
            ->setModel($model)
            ->setMotorization($motorization)
            ->setCreatedAt($createdAt);

        $this->assertTrue($vehicle->getPower() === '12');
        $this->assertTrue($vehicle->getSpace() === '15');
        $this->assertTrue($vehicle->getImagePath() === 'somePath');
        $this->assertTrue($vehicle->getVideoPath() === 'somePath');
        $this->assertTrue($vehicle->getPrice() === 10_000.50);
        $this->assertTrue($vehicle->getDescription() === 'description');
        $this->assertTrue($vehicle->getBrand() === $brand);
        $this->assertContains($color, $vehicle->getColor());
        $this->assertTrue($vehicle->getType() === $type);
        $this->assertTrue($vehicle->getModel() === $model);
        $this->assertTrue($vehicle->getMotorization() === $motorization);
        $this->assertTrue($vehicle->getCreatedAt() === $createdAt);
    }

    public function testIsFalse(): void
    {
        $vehicle = (new Vehicle)
            ->setPower('12')
            ->setSpace('15')
            ->setImagePath('somePath')
            ->setImagePath('somePath')
            ->setPrice(10_000)
            ->setDescription('description')
            ->setBrand(new Brand())
            ->addColor(new Color())
            ->setType(new Type())
            ->setModel(new Model())
            ->setMotorization(new Motorization())
            ->setCreatedAt(new DateTimeImmutable());

        $this->assertFalse($vehicle->getPower() === '8');
        $this->assertFalse($vehicle->getSpace() === '16');
        $this->assertFalse($vehicle->getImagePath() === 'someOtherPath');
        $this->assertFalse($vehicle->getVideoPath() === 'someOtherPath');
        $this->assertFalse($vehicle->getPrice() === 20_000);
        $this->assertFalse($vehicle->getDescription() === 'another description');
        $this->assertFalse($vehicle->getBrand() === new Brand());
        $this->assertNotContains(new Color(), $vehicle->getColor());
        $this->assertFalse($vehicle->getType() === new Type());
        $this->assertFalse($vehicle->getModel() === new Model());
        $this->assertFalse($vehicle->getMotorization() === new Motorization());
        $this->assertFalse($vehicle->getCreatedAt() === new DateTimeImmutable());
    }

    public function testIsEmpty(): void
    {
        $vehicle = new Vehicle();

        $this->assertEmpty($vehicle->getPower());
        $this->assertEmpty($vehicle->getSpace());
        $this->assertEmpty($vehicle->getBrand());
        $this->assertEmpty($vehicle->getColor());
        $this->assertEmpty($vehicle->getImagePath());
        $this->assertEmpty($vehicle->getVideoPath());
        $this->assertEmpty($vehicle->getPrice());
        $this->assertEmpty($vehicle->getDescription());
        $this->assertEmpty($vehicle->getType());
        $this->assertEmpty($vehicle->getModel());
        $this->assertEmpty($vehicle->getMotorization());
        $this->assertEmpty($vehicle->getCreatedAt());
    }

    public function testRemoveColor(): void
    {
        $color = new Color();
        $vehicle = (new Vehicle())
            ->addColor($color);

        $this->assertContains($color, $vehicle->getColor());

        $vehicle->removeColor($color);
        $this->assertEmpty($vehicle->getColor());
    }
}
