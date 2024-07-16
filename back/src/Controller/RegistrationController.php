<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Repository\UserRepository;
use App\Service\EmailService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private EmailService $emailService
    ) {}

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserService $userService
    ): JsonResponse {
        $userData = json_decode($request->getContent(), true);

        $user = $userService->createUser($userData);

        $this->emailService->sendEmailConfirmation($user);

        return $this->json(['message' => 'Merci de vérifier votre boîte email, nous vous avons envoyé un email de confirmation'], Response::HTTP_CREATED);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        UserRepository $userRepository,
        EmailVerifier $emailVerifier,
    ): Response {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirect($this->getParameter('frontend_base_url') . "/connexion?errorMessage=Aucun identifiant n'a été fournit");
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirect($this->getParameter('frontend_base_url') . '/connexion?errorMessage=Cet utilisateur est introuvable');
        }

        try {
            $emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            return $this->redirect($this->getParameter('frontend_base_url') . '/connexion?errorMessage=Une erreur est survenue, veuillez réessayer plus tard');
        }

        return $this->redirect($this->getParameter('frontend_base_url') . '/connexion?successMessage=Merci, votre adresse email a bien été vérifiée.');
    }

    #[Route('/resend-confirmation-email', name: 'app_resend_confirmation_email', methods: ['POST'])]
    public function resendConfirmationEmail(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $userData = json_decode($request->getContent(), true);
        $email = $userData["email"] ?? null;

        if (!$email) {
            return new JsonResponse(['message' => "L'adresse email est requise"], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['message' => "Cette adresse email n'existe pas dans notre base, vous devez vous inscrire"], Response::HTTP_NOT_FOUND);
        }

        if ($user->isVerified()) {
            return new JsonResponse(['message' => 'Cette adresse email a déjà été vérifié, vous pouvez vous connecter'], Response::HTTP_BAD_REQUEST);
        }

        $this->emailService->sendEmailConfirmation($user);

        return new JsonResponse(['message' => 'Un email de confirmation vient de vous être envoyé'], Response::HTTP_OK);
    }
}
