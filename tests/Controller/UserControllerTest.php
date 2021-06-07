<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Tests\Controller\WebCaseTestTrait;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    use WebCaseTestTrait;

    /**
     * - Testing user list
     */
    public function testIndex(): void
    {
        $client = static::createClient();

        $loginRedirectMessage = 'Redirecting to /login';
        $crawler = $client->request('GET', '/user/');
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'Not redirected to login page');
        $this->assertStringContainsString($loginRedirectMessage, $client->getResponse()->getContent());

        $this->login($client, 'user_role_user_edit@app.locale');
        $crawler = $client->request('GET', '/user/');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');
        // $this->assertResponseIsSuccessful();

        $this->login($client, 'user_role_user_view@app.locale');
        $crawler = $client->request('GET', '/user/');
        $this->assertResponseIsSuccessful('unable to access page with required permission');
    }

    /**
     * - Testing user creation
     */
    public function testNew(): void
    {
        $client = static::createClient();

        /**
         * - Not login user
         * - Should redirect to the login page
         * - this test it
         */
        $loginRedirectMessage = 'Redirecting to /login';
        $crawler = $client->request('GET', '/user/new');
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'Not redirected to login page');
        $this->assertStringContainsString($loginRedirectMessage, $client->getResponse()->getContent());

        /**
         * Should failed test
         */
        $this->login($client, 'user_role_user_delete@app.locale');
        $crawler = $client->request('GET', '/user/new');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        /**
         * - Normal test, user have good permission and role
         * - User can access page
         */
        $this->login($client, 'user_role_user_add@app.locale');
        $crawler = $client->request('GET', '/user/new');
        $this->assertResponseIsSuccessful();
    }

    /**
     * - Testing detail user page
     */
    public function testShow(): void
    {
        $client = static::createClient();

        $this->login($client, 'user_role_user_delete@app.locale');
        $crawler = $client->request('GET', $this->getUserUrl());
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        $this->login($client, 'user_role_user_view@app.locale');
        $crawler = $client->request('GET', $this->getUserUrl());
        $this->assertResponseIsSuccessful();
    }

    /**
     * - Testing user edition
     */
    public function testEdit(): void
    {
        $client = static::createClient();

        $this->login($client, 'user_role_user_delete@app.locale');
        $crawler = $client->request('GET', $this->getUserUrl('/edit'));
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        $this->login($client, 'user_role_user_edit@app.locale');
        $crawler = $client->request('GET', $this->getUserUrl('/edit'));
        $this->assertResponseIsSuccessful();
    }

    /**
     * - Testing user delete
     */
    public function testDelete(): void
    {
        $client = static::createClient();
        
        $this->login($client, 'user_role_user_add@app.locale');
        $deleteUrl = $this->getUserUrl('/delete', 'user_role_user_delete_1_@app.locale');
        $crawler = $client->request('POST', $deleteUrl);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        $this->login($client, 'user_role_user_delete@app.locale');
        $crawler = $client->request('POST', $deleteUrl);
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'Delete success');
    }

    public function testResetPassword(): void
    {
        $client = static::createClient();
        
        $this->login($client, 'user_role_user_delete@app.locale');
        $resetPasswordUrl = $this->getuserUrl('/reset-password');
        $crawler = $client->request('POST', $resetPasswordUrl);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        $this->login($client, 'user_role_user_edit@app.locale');
        $crawler = $client->request('POST', $resetPasswordUrl);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode(), 'Delete success');
    }

    public function getUserUrl($urlSufix = '', $email = null)
    {
        $userId = $this->getUserId($email);

        return '/user/' .$userId. '' .$urlSufix; 
    }

    public function getUserId($email = null)
    {
        /** store the user id */
        $userId = null;
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use self::$container to access the service container
        $container = self::$container;

        /** Get userRepository */
        $userRepository = $container->get(UserRepository::class);

        if ($email) {
            $user = $userRepository->findOneByEmail($email);
            $userId = $user->getid();
        } else {
            $users = $userRepository->findAll();
            $userId = $users[0]->getId();
        }

        return $userId;
    }
}