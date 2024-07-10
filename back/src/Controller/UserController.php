<?php

namespace App\Controller;

use App\Service\UserService;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class UserController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserService $userService
    ) {}

    #[Route('/user/get-orders/{username}', methods: ["GET"])]
    public function getUserOrders(
        string $username,
        OrderRepository $orderRepository
    ): JsonResponse {
        $user = $this->userService->getUserByEmail($username);

        $orders = $orderRepository->findByUser($user);

        return $this->json([
            'orders' => $orders
        ]);
    }

    #[Route('/user/get-infos/{username}', methods: ["GET"])]
    public function getUserInfo(string $username): JsonResponse
    {
        $user = $this->userService->getUserByEmail($username);

        return $this->json([
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'phone' => $user->getPhone()
        ]);
    }

    #[Route('/user/{username}/verify-password', methods: ["POST"])]
    public function verifyPassword(Request $request, string $username): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $currentPassword = $data['currentPassword'];

        $user = $this->userService->getUserByEmail($username);

        if ($this->passwordHasher->isPasswordValid($user, $currentPassword)) {
            return $this->json(['validPassword' => true]);
        }

        return $this->json(['validPassword' => false], JsonResponse::HTTP_UNAUTHORIZED);
    }

    #[Route('/user/{username}/change-password', methods: ["POST"])]
    public function changePassword(string $username, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->userService->getUserByEmail($username);

        if (!$this->passwordHasher->isPasswordValid($user, $data['currentPassword'])) {
            return new JsonResponse(['errorMessage' => 'Le mot de passe est incorrect'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->userService->changePassword($user, $data['newPassword']);

        return new JsonResponse(['successMessage' => "Votre mot de passe a été modifié avec succès"], JsonResponse::HTTP_OK);
    }

    #[Route('/user/{username}/edit-profile', methods: ["POST"])]
    public function editProfile(
        string $username,
        Request $request,
        JWTTokenManagerInterface $jwtManager,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $user = $this->userService->getUserByEmail($username);

        if ($this->userService->updateUserProfile($user, $data)) {
            $token = $jwtManager->create($user);
            return new JsonResponse(['successMessage' => 'Les informations ont bien été mise à jour', 'token' => $token], JsonResponse::HTTP_OK);
        }

        return new JsonResponse(['errorMessage' => "Certaines données sont invalides"], JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route('/user/{username}/delete-account', methods: ["DELETE"])]
    public function deleteUserAccount(string $username): JsonResponse
    {
        $user = $this->userService->getUserByEmail($username);

        $this->userService->deleteUser($user);

        return new JsonResponse(['successMessage' => 'Votre compte a bien été supprimé'], JsonResponse::HTTP_OK);
    }
}