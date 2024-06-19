<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController
{
    public const SCOPES = [
        'google' => []
    ];

    #[Route(path: '/login', name: 'app_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        return new JsonResponse(['message' => 'Connexion en cours'], JsonResponse::HTTP_OK);
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): void
    {
        throw new \LogicException('This should never be reached!');
    }

    #[Route("/oauth/connect/{service}", name: 'auth_oauth_connect', methods: ['GET'])]
    public function connect(string $service, ClientRegistry $clientRegistry): RedirectResponse
    {
        if (! in_array($service, array_keys(self::SCOPES), true)) {
            throw $this->createNotFoundException();
        }

        return $clientRegistry
            ->getClient($service)
            ->redirect(self::SCOPES[$service], []);
    }

    #[Route('/oauth/check/{service}', name: 'auth_oauth_check')]
    public function check(): JsonResponse
    {
        return new JsonResponse(['message' => 'Authentication check passed'], JsonResponse::HTTP_OK);
    }
}
