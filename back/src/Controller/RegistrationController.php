<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse {
        $userData = json_decode($request->getContent(), true);

        $user = (new User())
            ->setEmail($userData['email'] ?? '')
            ->setPassword($userData['password'] ?? '');

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );

        $entityManager->persist($user);
        $entityManager->flush();

        // Envoyer le mail depuis un Event Subscriber
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('adrien.le.pober@gmail.com', 'Design Motor'))
                ->to($user->getEmail())
                ->subject('Merci de confirmer votre adresse email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        return $this->json(['message' => 'Vous avez reçu un email de validation'], Response::HTTP_CREATED);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirect("http://localhost:4200/verification-email?message=Aucun identifiant n'a été fournit");
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirect('http://localhost:4200/verification-email?message=Cet utilisateur est introuvable');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $errorMessage = $translator->trans($exception->getReason(), [], 'VerifyEmailBundle');
            return $this->redirect('http://your-frontend-app-url.com/verification-email?message=' . urlencode($errorMessage));
        }

        return $this->redirect('http://localhost:4200/verification-email?message=Votre adresse email à bien été vérifiée.');
    }
}
