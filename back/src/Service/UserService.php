<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private ValidatorService $validator,
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository
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

    public function getUserByEmail(string $email): User
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new \Exception('User not found');
        }

        return $user;
    }

    public function changePassword(User $user, string $newPassword): void
    {
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $newPassword));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function updateUserProfile(User $user, array $data): bool
    {
        if ($data['email'] && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $user
                ->setEmail($data['email'])
                ->setFirstname(trim(ucfirst(strtolower($data['firstname']))))
                ->setLastname(trim(ucfirst(strtolower($data['lastname']))))
                ->setPhone($data['phone']);
    
            if ($this->validator::validate($user)) {
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                return true;
            }
        }
        return false;
    }

    public function deleteUser(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}