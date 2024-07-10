<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private ValidatorService $validator,
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $entityManager
    ) {}

    public function createUser(array $userData): User
    {
        $user = (new User())
            ->setEmail($userData['email'] ?? '')
            ->setPassword($userData['password'] ?? '')
            ->setRgpd($userData['rgpd'] ?? false);

        $this->validator::validate($user);

        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}