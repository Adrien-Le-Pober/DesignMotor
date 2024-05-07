<?php

namespace App\Tests\Entity;

use App\Entity\Brand;
use App\Entity\Model;
use App\Entity\Vehicle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BrandEntityTest extends KernelTestCase
{
    public function getEntity(): Brand
	{
		return (new Brand())
            ->setName('Renault')
            ->addModel(new Model())
            ->addVehicle(new Vehicle());
	}

    public function testEntityIsValid()
	{
		$this->assertHasErrors($this->getEntity());
	}

    public function testInvalidBlankArgument()
	{
		$this->assertHasErrors($this->getEntity()->setName(''), 1);
	}

    public function testInvalidMaxLength()
	{
		$this->assertHasErrors($this->getEntity()->setName(str_repeat('a', 49)), 1);
	}

    public function assertHasErrors(Brand $brand, int $number = 0): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());

        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($brand);
        $messages = [];

        /** @var ConstraintViolation $error */
		foreach($errors as $error) {
			$messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
		}
		$this->assertCount($number, $errors, implode(', ', $messages));
    }
}
