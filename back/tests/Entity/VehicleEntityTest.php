<?php

namespace App\Tests\Entity;

use App\Entity\Type;
use App\Entity\Brand;
use App\Entity\Color;
use App\Entity\Model;
use App\Entity\Vehicle;
use App\Entity\Motorization;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VehicleEntityTest extends KernelTestCase
{
    public function getEntity(): Vehicle
	{
		return (new Vehicle())
            ->setPower('55')
            ->setSpace('20')
            ->setBrand(new Brand())
            ->addColor(new Color())
            ->setType(new Type())
            ->setMotorization(new Motorization())
            ->setModel(new Model());
	}

    public function testEntityIsValid()
	{
		$this->assertHasErrors($this->getEntity());
	}

    public function testInvalidBlankArgument()
	{
		$this->assertHasErrors($this->getEntity()->setPower(''), 1);
        $this->assertHasErrors($this->getEntity()->setSpace(''), 1);
	}

    public function testInvalidNullArgument()
	{
		$this->assertHasErrors($this->getEntity()->setBrand(null), 1);
        $this->assertHasErrors($this->getEntity()->setType(null), 1);
        $this->assertHasErrors($this->getEntity()->setMotorization(null), 1);
        $this->assertHasErrors($this->getEntity()->setModel(null), 1);
	}

    public function testInvalidMaxLength()
	{
		$this->assertHasErrors($this->getEntity()->setPower(str_repeat('1', 9)), 1);
        $this->assertHasErrors($this->getEntity()->setSpace(str_repeat('1', 9)), 1);
	}

    public function testInvalidRegex()
	{
		$this->assertHasErrors($this->getEntity()->setPower('a'), 1);
        $this->assertHasErrors($this->getEntity()->setSpace('a'), 1);
	}

    public function assertHasErrors(Vehicle $vehicle, int $number = 0): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());


        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($vehicle);
        $messages = [];

        /** @var ConstraintViolation $error */
		foreach($errors as $error) {
			$messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
		}
		$this->assertCount($number, $errors, implode(', ', $messages));
    }
}
