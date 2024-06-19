<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\AbstractOAuthAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class GoogleAuthenticator extends AbstractOAuthAuthenticator
{
    protected string $serviceName = 'google';

    public function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner, UserRepository $userRepository): ?User
    {
        if(!($resourceOwner instanceof GoogleUser)) {
            throw new \RuntimeException("expecting google user");
        }

        if (true !== ($resourceOwner->toArray()['email_verified'] ?? null)) {
            throw new AuthenticationException("Cette adresse email n'est pas vérifiée");
        }

        return $userRepository->findOneBy([
            'googleId' => $resourceOwner->getId(),
            'email' => $resourceOwner->getEmail()
        ]);
    }
}