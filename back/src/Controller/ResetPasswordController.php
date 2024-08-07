<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager,
        private TranslatorInterface $translator,
        private EmailService $emailService
    ) {
    }

    #[Route('', name: 'app_forgot_password_request', methods: ['POST'])]
    public function request(Request $request, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return new JsonResponse(['error' => "L'adresse email est requise"], Response::HTTP_BAD_REQUEST);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['error' => "L'adresse email n'est pas valide"], Response::HTTP_BAD_REQUEST);
        }

        return $this->processSendingPasswordResetEmail($email, $mailer, $this->translator);
    }

    #[Route('/reset/{token}', name: 'app_reset_password', methods: ['POST'])]
    public function reset(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ?string $token = null
    ): JsonResponse {
        if (null === $token) {
            return new JsonResponse(['error' => 'Le token est introuvable'], Response::HTTP_NOT_FOUND);
        }

        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse(['error' => $this->translator->trans($e->getReason(), [], 'ResetPasswordBundle')], Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);
        $plainPassword = $data['plainPassword'] ?? null;

        if (!$plainPassword) {
            return new JsonResponse(['error' => 'Le mot de passe est requis'], Response::HTTP_BAD_REQUEST);
        }

        $this->resetPasswordHelper->removeResetRequest($token);

        $encodedPassword = $passwordHasher->hashPassword(
            $user,
            $plainPassword
        );

        $user->setPassword($encodedPassword);
        $this->entityManager->flush();

        return new JsonResponse(['message' => "Le mot de passe vient d'être modifié"]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        if (!$user) {
            return new JsonResponse(['message' => 'Si cette adresse email est dans notre base, vous recevrez sous peu un email pour réinitialiser votre mot de passe'], Response::HTTP_OK);
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse(['error' => $this->translator->trans($e->getReason(), [], 'ResetPasswordBundle')], Response::HTTP_BAD_REQUEST);
        }

        $this->emailService->sendPasswordResetEmail($user, $resetToken, $this->getParameter('frontend_base_url'));

        return new JsonResponse(['message' => 'Si cette adresse email est dans notre base, vous recevrez sous peu un email pour réinitialiser votre mot de passe'], Response::HTTP_OK);
    }
}
