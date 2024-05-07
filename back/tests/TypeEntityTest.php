<?php

namespace App\Tests;

use App\Entity\Type;
use App\Entity\Vehicle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TypeEntityTest extends KernelTestCase
{
    public function getEntity(): Type
	{
		return (new Type())
            ->setName('Car')
            ->addVehicle(new Vehicle());
	}

    public function testInvalidBlankArgument()
	{
		$this->assertHasErrors($this->getEntity()->setName(''), 1);
	}

    public function testInvalidMaxLength()
	{
		$this->assertHasErrors($this->getEntity()->setName(str_repeat('a', 49)), 1);
	}

    public function assertHasErrors(Type $type, int $number = 0): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());

        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($type);
        $messages = [];

        /** @var ConstraintViolation $error */
		foreach($errors as $error) {
			$messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
		}
		$this->assertCount($number, $errors, implode(', ', $messages));
    }
}
