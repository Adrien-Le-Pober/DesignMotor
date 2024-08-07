<?php

namespace App\Tests\Entity;

use App\Entity\Type;
use App\Entity\Brand;
use App\Entity\Color;
use App\Entity\Model;
use DateTimeImmutable;
use App\Entity\Vehicle;
use App\Entity\Motorization;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VehicleEntityTest extends KernelTestCase
{
    private string $imagePath;
    private string $videoPath;

    public function setUp(): void
    {
        self::bootKernel();
        $kernel = self::$kernel;

        $this->imagePath = $kernel->getProjectDir() . '/public/images';
        $this->videoPath = $kernel->getProjectDir() . '/public/videos';
    }

    public function getEntity(): Vehicle
	{
		return (new Vehicle())
            ->setPower('55')
            ->setSpace('20')
            ->setImagePath($this->imagePath . 'SomePath.jpg')
            ->setVideoPath($this->videoPath . 'SomePath.mp4')
            ->setPrice(10_000)
            ->setDescription('description')
            ->setCreatedAt(new DateTimeImmutable())
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
        $this->assertHasErrors($this->getEntity()->setImagePath(''), 1);
        $this->assertHasErrors($this->getEntity()->setVideoPath(''), 1);
        $this->assertHasErrors($this->getEntity()->setDescription(''), 1);
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
        $this->assertHasErrors($this->getEntity()->setImagePath(str_repeat('p', 256)), 1);
        $this->assertHasErrors($this->getEntity()->setVideoPath(str_repeat('p', 256)), 1);
	}

    public function testInvalidRegex()
	{
		$this->assertHasErrors($this->getEntity()->setPower('a'), 1);
        $this->assertHasErrors($this->getEntity()->setSpace('a'), 1);
	}

    public function testInvalidNumber()
    {
        $this->assertHasErrors($this->getEntity()->setPrice(-10,), 1);
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
