<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserEntityTest extends KernelTestCase
{
    public function getEntity(): User
	{
		return (new User())
            ->setEmail('test' . uniqid() . '@test.com')
            ->setPassword('Password123!')
            ->setRoles(["ROLE_USER"])
            ->setVerified(true);
	}

    public function testInvalidBlankArgument()
	{
		$this->assertHasErrors($this->getEntity()->setEmail(''), 1);
        $this->assertHasErrors($this->getEntity()->setPassword(''), 1);
	}

    public function testInvalidEmail(): void
    {
        $this->assertHasErrors($this->getEntity()->setEmail('invalid-email'), 1);
    }

    public function testInvalidPassword(): void
    {
        $this->assertHasErrors($this->getEntity()->setPassword('weakpass'), 1);
    }

    public function testValidRoles(): void
    {
        $this->assertHasErrors($this->getEntity()->setRoles(['ROLE_ADMIN']), 0);
    }

    public function assertHasErrors(User $user, int $number = 0): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());

        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($user);
        $messages = [];

        /** @var ConstraintViolation $error */
		foreach($errors as $error) {
			$messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
		}
		$this->assertCount($number, $errors, implode(', ', $messages));
    }
}
