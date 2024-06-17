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
    public function __construct(private EmailVerifier $emailVerifier, private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        ValidatorInterface $validator
    ): JsonResponse {
        $userData = json_decode($request->getContent(), true);

        $user = (new User())
            ->setEmail($userData['email'] ?? '')
            ->setPassword($userData['password'] ?? '')
            ->setRgpd($userData['rgpd'] ?? false);

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

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Envoyer le mail depuis un Event Subscriber
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address($this->getParameter('mailer_from'), 'Design Motor'))
                ->to($user->getEmail())
                ->subject('Confirmation de votre adresse email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        return $this->json(['message' => 'Merci de vérifier votre boîte email, nous vous avons envoyé un email de confirmation'], Response::HTTP_CREATED);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirect($this->getParameter('frontend_base_url') . "/connexion?errorMessage=Aucun identifiant n'a été fournit");
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirect($this->getParameter('frontend_base_url') . '/connexion?errorMessage=Cet utilisateur est introuvable');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $errorMessage = $translator->trans($exception->getReason(), [], 'VerifyEmailBundle');
            return $this->redirect($this->getParameter('frontend_base_url') . '/connexion?errorMessage=' . urlencode($errorMessage));
        }

        return $this->redirect($this->getParameter('frontend_base_url') . '/connexion?successMessage=Merci, votre adresse email a bien été vérifiée.');
    }

    #[Route('/resend-confirmation-email', name: 'app_resend_confirmation_email', methods: ['POST'])]
    public function resendConfirmationEmail(Request $request): JsonResponse
    {
        $userData = json_decode($request->getContent(), true);
        $email = $userData["email"];

        if (!$email) {
            return new JsonResponse(['message' => "L'adresse email est requise"], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['message' => "Cette adresse email n'existe pas dans notre base, vous devez vous inscrire"], Response::HTTP_NOT_FOUND);
        }

        if ($user->isVerified()) {
            return new JsonResponse(['message' => 'Cette adresse email a déjà été vérifié, vous pouvez vous connecter'], Response::HTTP_BAD_REQUEST);
        }

        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address($this->getParameter('mailer_from'), 'Design Motor'))
                ->to($user->getEmail())
                ->subject('Confirmation de votre adresse email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        return new JsonResponse(['message' => 'Un email de confirmation vient de vous être envoyé'], Response::HTTP_OK);
    }
}
