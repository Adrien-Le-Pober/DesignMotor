<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Service\UserService;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class UserControllerTest extends WebTestCase
{
    private $client;

    private $username;
    private $user;

    private $userService;
    private $orderRepository;
    private $passwordHasher;
    private $jwtManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->username = 'test@example.com';
        $this->user = (new User())->setEmail($this->username);

        $this->userService = $this->createMock(UserService::class);
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->jwtManager = $this->createMock(JWTTokenManagerInterface::class);

        $this->client->getContainer()->set('App\Service\UserService', $this->userService);
        $this->client->getContainer()->set('App\Repository\OrderRepository', $this->orderRepository);
        $this->client->getContainer()->set('Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface', $this->passwordHasher);
        $this->client->getContainer()->set('Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface', $this->jwtManager);
    }

    public function testGetUserOrders()
    {
        $this->userService->expects($this->once())
            ->method('getUserByEmail')
            ->with($this->username)
            ->willReturn($this->user);

        $orders = [/* Array of order data */];
        $this->orderRepository->expects($this->once())
            ->method('findByUser')
            ->with($this->user)
            ->willReturn($orders);

        $this->client->request('GET', '/user/get-orders/' . $this->username);

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['orders' => $orders]),
            $response->getContent()
        );
    }

    public function testGetUserInfo()
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn($this->username);
        $user->method('getFirstname')->willReturn('John');
        $user->method('getLastname')->willReturn('Doe');
        $user->method('getPhone')->willReturn('1234567890');

        $this->userService->expects($this->once())
            ->method('getUserByEmail')
            ->with($this->username)
            ->willReturn($user);

        $this->client->request('GET', '/user/get-infos/' . $this->username);

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'email' => $this->username,
                'firstname' => 'John',
                'lastname' => 'Doe',
                'phone' => '1234567890'
            ]),
            $response->getContent()
        );
    }

    public function testVerifyPassword()
    {
        $currentPassword = 'password';

        $this->userService->expects($this->once())
            ->method('getUserByEmail')
            ->with($this->username)
            ->willReturn($this->user);

        $this->passwordHasher->expects($this->once())
            ->method('isPasswordValid')
            ->with($this->user, $currentPassword)
            ->willReturn(true);

        $this->client->request(
            'POST',
            '/user/' . $this->username . '/verify-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['currentPassword' => $currentPassword])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['validPassword' => true]),
            $response->getContent()
        );
    }

    public function testVerifyPasswordIncorrect()
    {
        $currentPassword = 'wrong_password';

        $this->userService->expects($this->once())
            ->method('getUserByEmail')
            ->with($this->username)
            ->willReturn($this->user);

        $this->passwordHasher->expects($this->once())
            ->method('isPasswordValid')
            ->with($this->user, $currentPassword)
            ->willReturn(false);

        $this->client->request(
            'POST',
            '/user/' . $this->username . '/verify-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['currentPassword' => $currentPassword])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['validPassword' => false]),
            $response->getContent()
        );
    }

    public function testChangePassword()
    {
        $currentPassword = 'currentPassword';
        $newPassword = 'newPassword';

        $this->userService->expects($this->once())
            ->method('getUserByEmail')
            ->with($this->username)
            ->willReturn($this->user);

        $this->passwordHasher->expects($this->once())
            ->method('isPasswordValid')
            ->with($this->user, $currentPassword)
            ->willReturn(true);

        $this->userService->expects($this->once())
            ->method('changePassword')
            ->with($this->user, $newPassword);

        $this->client->request(
            'POST',
            '/user/' . $this->username . '/change-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['currentPassword' => $currentPassword, 'newPassword' => $newPassword])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['successMessage' => 'Votre mot de passe a été modifié avec succès']),
            $response->getContent()
        );
    }

    public function testChangePasswordIncorrect()
    {
        $currentPassword = 'wrong_password';
        $newPassword = 'new_password';

        $this->userService->expects($this->once())
            ->method('getUserByEmail')
            ->with($this->username)
            ->willReturn($this->user);

        $this->passwordHasher->expects($this->once())
            ->method('isPasswordValid')
            ->with($this->user, $currentPassword)
            ->willReturn(false);

        $this->client->request(
            'POST',
            '/user/' . $this->username . '/change-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['currentPassword' => $currentPassword, 'newPassword' => $newPassword])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['errorMessage' => 'Le mot de passe est incorrect']),
            $response->getContent()
        );
    }

    public function testEditProfile()
    {
        $this->userService->expects($this->once())
            ->method('getUserByEmail')
            ->with($this->username)
            ->willReturn($this->user);

        $this->userService->expects($this->once())
            ->method('updateUserProfile')
            ->with($this->user, $this->anything())
            ->willReturn(true);

        $this->jwtManager->expects($this->once())
            ->method('create')
            ->with($this->user)
            ->willReturn('jwt_token');

        $this->client->request(
            'POST',
            '/user/' . $this->username . '/edit-profile',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['data' => 'value'])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['successMessage' => 'Les informations ont bien été mise à jour', 'token' => 'jwt_token']),
            $response->getContent()
        );
    }

    public function testEditProfileInvalidData()
    {
        $this->userService->expects($this->once())
            ->method('getUserByEmail')
            ->with($this->username)
            ->willReturn($this->user);

        $this->userService->expects($this->once())
            ->method('updateUserProfile')
            ->with($this->user, $this->anything())
            ->willReturn(false); // Simulate invalid data scenario

        $this->client->request(
            'POST',
            '/user/' . $this->username . '/edit-profile',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['data' => 'invalid_value'])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['errorMessage' => 'Certaines données sont invalides']),
            $response->getContent()
        );
    }

    public function testDeleteUserAccount()
    {
        $this->userService->expects($this->once())
            ->method('getUserByEmail')
            ->with($this->username)
            ->willReturn($this->user);
    
        $this->userService->expects($this->once())
            ->method('deleteUser')
            ->with($this->user);
    
        $this->client->request('DELETE', '/user/' . $this->username . '/delete-account');
    
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['successMessage' => 'Votre compte a bien été supprimé']),
            $response->getContent()
        );
    }
}