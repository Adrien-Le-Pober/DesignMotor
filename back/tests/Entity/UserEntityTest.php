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
            ->setVerified(true)
            ->setRgpd(true)
            ->setLastname('Doe')
            ->setFirstname('John')
            ->setPhone('1234567890');
	}

    public function testInvalidBlankArgument()
	{
		$this->assertHasErrors($this->getEntity()->setEmail(''), 1);
        $this->assertHasErrors($this->getEntity()->setPassword(''), 1);
        $this->assertHasErrors($this->getEntity()->setLastname(''), 1);
        $this->assertHasErrors($this->getEntity()->setFirstname(''), 1);
        $this->assertHasErrors($this->getEntity()->setPhone(''), 1);
	}

    public function testInvalidEmail(): void
    {
        $this->assertHasErrors($this->getEntity()->setEmail('invalid-email'), 1);
    }

    public function testInvalidPassword(): void
    {
        $this->assertHasErrors($this->getEntity()->setPassword('weakpass'), 1);
        $this->assertHasErrors($this->getEntity()->setPassword('NoNumbers!'), 1);
        $this->assertHasErrors($this->getEntity()->setPassword('nocapitals123!'), 1);
        $this->assertHasErrors($this->getEntity()->setPassword('Nocapitals123'), 1);
    }

    public function testValidRoles(): void
    {
        $this->assertHasErrors($this->getEntity()->setRoles(['ROLE_ADMIN']), 0);
    }

    public function testInvalidRgpd(): void
    {
        $this->assertHasErrors($this->getEntity()->setRgpd(false), 1);
    }

    public function testInvalidLastname(): void
    {
        $this->assertHasErrors($this->getEntity()->setLastname('Doe123'), 1);
        $this->assertHasErrors($this->getEntity()->setLastname(str_repeat('a', 81)), 1);
    }

    public function testInvalidFirstname(): void
    {
        $this->assertHasErrors($this->getEntity()->setFirstname('John123'), 1);
        $this->assertHasErrors($this->getEntity()->setFirstname(str_repeat('a', 81)), 1);
    }

    public function testInvalidPhone(): void
    {
        $this->assertHasErrors($this->getEntity()->setPhone('invalid-phone'), 1);
        $this->assertHasErrors($this->getEntity()->setPhone(str_repeat('1', 17)), 1);
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
