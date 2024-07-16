<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationControllerTest extends WebTestCase
{
    private $entityManager;
    private $emailVerifier;
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();

        $this->entityManager = $container->get('doctrine')->getManager();
        $this->emailVerifier = $this->createMock(EmailVerifier::class);

        $container->set('App\Security\EmailVerifier', $this->emailVerifier);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
        $this->emailVerifier = null;
        $this->client = null;
    }

    public function testRegister(): void
    {
        $email = 'test' . uniqid() . '@example.com';

        $this->client->request(
            'POST',
            '/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $email, 'password' => 'TestPassword123!', 'rgpd' => true])
        );

        $response = $this->client->getResponse();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Merci de vérifier votre boîte email, nous vous avons envoyé un email de confirmation']),
            $response->getContent()
        );
    }

    public function testVerifyUserEmail(): void
    {
        $user = new User();
        $user->setEmail('test' . uniqid() . '@example.com');
        $user->setPassword('TestPassword123!');
        $user->setVerified(false);
        $user->setRgpd(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->emailVerifier
            ->expects($this->once())
            ->method('handleEmailConfirmation')
            ->with($this->anything(), $this->callback(function($u) use ($user) {
                $user->setVerified(true);
                return $u === $user;
            }));

        $userId = $user->getId();
        $this->client->request('GET', '/verify/email', ['id' => $userId, 'token' => 'mocked_token']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertStringContainsString(
            'http://localhost:4200/connexion?successMessage=Merci, votre adresse email a bien été vérifiée.',
            $this->client->getResponse()->headers->get('Location')
        );

        $this->entityManager->refresh($user);
        $this->assertTrue($user->isVerified());
    }

    public function testVerifyUserEmailInvalidId(): void
    {
        $this->client->request('GET', '/verify/email', ['id' => 9999]); // ID qui n'existe pas

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertStringContainsString(
            'http://localhost:4200/connexion?errorMessage=Cet utilisateur est introuvable',
            $this->client->getResponse()->headers->get('Location')
        );
    }

    public function testVerifyUserEmailMissingId(): void
    {
        $this->client->request('GET', '/verify/email'); // Pas d'ID

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertStringContainsString(
            'http://localhost:4200/connexion?errorMessage=Aucun identifiant n\'a été fournit',
            $this->client->getResponse()->headers->get('Location')
        );
    }

    public function testVerifyUserEmailException(): void
    {
        // Créez un utilisateur de test
        $user = new User();
        $user->setEmail('test' . uniqid() . '@example.com');
        $user->setPassword('TestPassword123!');
        $user->setVerified(false);
        $user->setRgpd(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userId = $user->getId();

        $exception = $this->createMock(VerifyEmailExceptionInterface::class);
        $exception->method('getReason')->willReturn('Une erreur est survenue, veuillez réessayer plus tard');

        // Configurez le mock pour lever une exception
        $this->emailVerifier
            ->expects($this->once())
            ->method('handleEmailConfirmation')
            ->will($this->throwException($exception));

        $this->client->request('GET', '/verify/email', ['id' => $userId, 'token' => 'mocked_token']);

        // Vérifiez la redirection
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertStringContainsString(
            'http://localhost:4200/connexion?errorMessage=Une erreur est survenue, veuillez réessayer plus tard',
            $this->client->getResponse()->headers->get('Location')
        );
    }

    public function testResendConfirmationEmail(): void
    {
        $email = 'test' . uniqid() . '@example.com';

        $user = new User();
        $user->setEmail($email);
        $user->setPassword('TestPassword123!');
        $user->setRgpd(true);
        $user->setVerified(false);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->request(
            'POST',
            '/resend-confirmation-email',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $email])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Un email de confirmation vient de vous être envoyé']),
            $this->client->getResponse()->getContent()
        );
    }

    public function testResendConfirmationEmailWithoutEmail(): void
    {
        $this->client->request(
            'POST',
            '/resend-confirmation-email',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => null])
        );

        $response = $this->client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => "L'adresse email est requise"]),
            $response->getContent()
        );
    }

    public function testResendConfirmationEmailUserNotFound(): void
    {
        $email = 'test' . uniqid() . '@example.com';

        $this->client->request(
            'POST',
            '/resend-confirmation-email',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $email])
        );

        $response = $this->client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => "Cette adresse email n'existe pas dans notre base, vous devez vous inscrire"]),
            $response->getContent()
        );
    }

    public function testResendConfirmationEmailAlreadyVerified(): void
    {
        $email = 'verified' . uniqid() . '@example.com';

        $user = new User();
        $user->setEmail($email);
        $user->setPassword('TestPassword123!');
        $user->setRgpd(true);
        $user->setVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->request(
            'POST',
            '/resend-confirmation-email',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $email])
        );

        $response = $this->client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Cette adresse email a déjà été vérifié, vous pouvez vous connecter']),
            $response->getContent()
        );
    }
}