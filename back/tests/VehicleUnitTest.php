<?php

namespace App\Tests;

use App\Entity\Type;
use App\Entity\Brand;
use App\Entity\Color;
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
        $motorization = new Motorization();

        $vehicle = (new Vehicle)
            ->setPower('12')
            ->setSpace('15')
            ->setBrand($brand)
            ->addColor($color)
            ->setType($type)
            ->setMotorization($motorization);

        $this->assertTrue($vehicle->getPower() === '12');
        $this->assertTrue($vehicle->getSpace() === '15');
        $this->assertTrue($vehicle->getBrand() === $brand);
        $this->assertContains($color, $vehicle->getColor());
        $this->assertTrue($vehicle->getType() === $type);
        $this->assertTrue($vehicle->getMotorization() === $motorization);
    }

    public function testIsFalse(): void
    {
        $brand = new Brand();
        $color = new Color();
        $type = new Type();
        $motorization = new Motorization();

        $vehicle = (new Vehicle)
            ->setPower('12')
            ->setSpace('15')
            ->setBrand($brand)
            ->addColor($color)
            ->setType($type)
            ->setMotorization($motorization);

        $this->assertFalse($vehicle->getPower() === '8');
        $this->assertFalse($vehicle->getSpace() === '16');
        $this->assertFalse($vehicle->getBrand() === new Brand());
        $this->assertNotContains(new Color(), $vehicle->getColor());
        $this->assertFalse($vehicle->getType() === new Type());
        $this->assertFalse($vehicle->getMotorization() === new Motorization());
    }

    public function testIsEmpty(): void
    {
        $vehicle = new Vehicle();

        $this->assertEmpty($vehicle->getPower());
        $this->assertEmpty($vehicle->getSpace());
        $this->assertEmpty($vehicle->getBrand());
        $this->assertEmpty($vehicle->getColor());
        $this->assertEmpty($vehicle->getType());
        $this->assertEmpty($vehicle->getMotorization());
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
