<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('/user/get-infos/{username}', methods: ["GET"])]
    public function getUserInfo(string $username): JsonResponse
    {
        $user = $this->getCurrentUser($username);

        return $this->json([
            'email' => $user->getEmail()
        ]);
    }

    #[Route('/user/{username}/verify-password', methods: ["POST"])]
    public function verifyPassword(Request $request, string $username): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $currentPassword = $data['currentPassword'];

        $user = $this->getCurrentUser($username);

        if ($this->passwordHasher->isPasswordValid($user, $currentPassword)) {
            return $this->json(['validPassword' => true]);
        }

        return $this->json(['validPassword' => false], JsonResponse::HTTP_UNAUTHORIZED);
    }

    #[Route('/user/{username}/change-password', methods: ["POST"])]
    public function changePassword(string $username, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->getCurrentUser($username);

        if (!$this->passwordHasher->isPasswordValid($user, $data['currentPassword'])) {
            return new JsonResponse(['errorMessage' => 'Le mot de passe est incorrect'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $data['newPassword']));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['successMessage' => "Votre mot de passe a été modifié avec succès"], JsonResponse::HTTP_OK);
    }

    #[Route('/user/{username}/edit-profile', methods: ["POST"])]
    public function editProfile(string $username, Request $request, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->getCurrentUser($username);

        if ($data['email'] && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $user->setEmail($data['email']);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $token = $jwtManager->create($user);
    
            return new JsonResponse(['successMessage' => 'Les informations ont bien été mise à jour', 'token' => $token], JsonResponse::HTTP_OK);
        }

        return new JsonResponse(['errorMessage' => "Le format de l'adresse email est invalide"], JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route('/user/{username}/delete-account', methods: ["DELETE"])]
    public function deleteUserAccount(string $username): JsonResponse
    {
        $user = $this->getCurrentUser($username);

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(['successMessage' => 'Votre compte a bien été supprimé'], JsonResponse::HTTP_OK);
    }

    private function getCurrentUser(string $username)
    {
        $user = $this->userRepository->findOneBy(['email' => $username]);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        return $user;
    }
}