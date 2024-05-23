<?php

namespace App\Tests\Entity;

use App\Entity\Brand;
use App\Entity\Model;
use App\Entity\Vehicle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ModelEntityTest extends KernelTestCase
{
    public function getEntity(): Model
	{
		return (new Model())
            ->setName('blue')
            ->setDescription('description')
            ->setBrand(new Brand())
            ->addVehicle(new Vehicle());
	}

    public function testInvalidBlankArgument()
	{
		$this->assertHasErrors($this->getEntity()->setName(''), 1);
        $this->assertHasErrors($this->getEntity()->setDescription(''), 1);
	}

        public function testInvalidNullArgument()
	{
		$this->assertHasErrors($this->getEntity()->setBrand(null), 1);
	}

    public function testInvalidMaxLength()
	{
		$this->assertHasErrors($this->getEntity()->setName(str_repeat('a', 49)), 1);
	}

    public function assertHasErrors(Model $model, int $number = 0): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());

        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($model);
        $messages = [];

        /** @var ConstraintViolation $error */
		foreach($errors as $error) {
			$messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
		}
		$this->assertCount($number, $errors, implode(', ', $messages));
    }
}
