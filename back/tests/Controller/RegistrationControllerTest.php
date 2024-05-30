<?php

namespace App\Tests\Controller;

use App\Controller\RegistrationController;
use App\Entity\User;
use App\Security\EmailVerifier;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Translation\DataCollectorTranslator;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationControllerTest extends WebTestCase
{
    private $entityManager;
    private $emailVerifier;
    private $translator;
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();

        $this->entityManager = $container->get('doctrine')->getManager();
        $this->emailVerifier = $this->createMock(EmailVerifier::class);
        $this->translator = $this->getMockBuilder(DataCollectorTranslator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container->set('App\Security\EmailVerifier', $this->emailVerifier);
        $container->set(TranslatorInterface::class, $this->translator);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }

    public function testVerifyUserEmail(): void
    {
        // Créez un utilisateur de test
        $user = new User();
        $user->setEmail('test' . uniqid() . '@example.com');
        $user->setPassword('TestPassword123!');
        $user->setVerified(false);

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

        // Vérifiez la redirection
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertStringContainsString(
            'http://localhost:4200/connection?message=Merci, votre adresse email a bien été vérifiée.',
            $this->client->getResponse()->headers->get('Location')
        );

        // Vérifiez que l'utilisateur est maintenant vérifié
        $this->entityManager->refresh($user);
        $this->assertTrue($user->isVerified());
    }

    public function testVerifyUserEmailInvalidId(): void
    {
        $this->client->request('GET', '/verify/email', ['id' => 9999]); // ID qui n'existe pas

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertStringContainsString(
            'http://localhost:4200/connection?message=Cet utilisateur est introuvable',
            $this->client->getResponse()->headers->get('Location')
        );
    }

    public function testVerifyUserEmailMissingId(): void
    {
        $this->client->request('GET', '/verify/email'); // Pas d'ID

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertStringContainsString(
            'http://localhost:4200/connection?message=Aucun identifiant n\'a été fournit',
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

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userId = $user->getId();

        $exception = $this->createMock(VerifyEmailExceptionInterface::class);
        $exception->method('getReason')->willReturn('Invalid token');

        // Configurez le mock pour lever une exception
        $this->emailVerifier
            ->expects($this->once())
            ->method('handleEmailConfirmation')
            ->will($this->throwException($exception));

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('Invalid token', [], 'VerifyEmailBundle')
            ->willReturn('Invalid token');

        $this->client->request('GET', '/verify/email', ['id' => $userId, 'token' => 'mocked_token']);

        // Vérifiez la redirection
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertStringContainsString(
            'http://localhost:4200/connection?message=Invalid+token',
            $this->client->getResponse()->headers->get('Location')
        );
    }

    public function testRegister(): void
    {
        $this->client->request('POST', '/register', [], [], [], json_encode(['email' => 'test' . uniqid() . '@example.com', 'password' => 'TestPassword123!']));
        
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testResendConfirmationEmail(): void
    {
        $email = 'test@example.com';

        $user = new User();
        $user->setEmail($email);
        $user->setPassword('TestPassword123!');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->request('POST', '/resend-confirmation-email', [], [], [], json_encode(['email' => $email]));
        
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testResendConfirmationEmailWithoutEmail(): void
    {
        $this->client->request('POST', '/resend-confirmation-email', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['email' => null]));

        $response = $this->client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => "L'adresse email est requise"]),
            $response->getContent()
        );
    }

    public function testResendConfirmationEmailUserNotFound(): void
    {
        $email = 'test@example.com';

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $email])
            ->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($userRepository);

        $controller = new RegistrationController($this->emailVerifier, $entityManager);

        $request = new Request([], [], [], [], [], [], json_encode(['email' => $email]));
        $response = $controller->resendConfirmationEmail($request);
    
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    
        $expectedMessage = json_encode(['message' => "Cette adresse email n'existe pas dans notre base"]);
        $this->assertJsonStringEqualsJsonString($expectedMessage, $response->getContent());
    }

}