<?php

namespace App\Tests\Entity;

use App\Entity\Color;
use App\Entity\Vehicle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ColorEntityTest extends KernelTestCase
{
    public function getEntity(): Color
	{
		return (new Color())
            ->setName('blue')
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

    public function assertHasErrors(Color $color, int $number = 0): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());

        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($color);
        $messages = [];

        /** @var ConstraintViolation $error */
		foreach($errors as $error) {
			$messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
		}
		$this->assertCount($number, $errors, implode(', ', $messages));
    }
}
