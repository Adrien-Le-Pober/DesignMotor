<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class OAuthRegistrationService
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) { }

    public function persist(ResourceOwnerInterface $resourceOwner): User
    {
        $user = match (true) {
            $resourceOwner instanceof GoogleUser => (new User())
                ->setEmail($resourceOwner->getEmail())
                ->setGoogleId($resourceOwner->getId())
/*             $resourceOwner instanceof LinkedInClient => (new User())
                ->setEmail($resourceOwner->getEmail())
                ->setLinkedInId($resourceOwner->getId()), */
        };

        $user->setPassword($this->passwordHasher->hashPassword($user, bin2hex(random_bytes(16))))
            ->setRgpd(true)
            ->setVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }
}