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
        $this->assertStringNotContainsString("form-error-message", $client->getResponse()->getContent());

        /**
         * Test for asserting real user data
         */
        $buttonCrawlerNode = $crawler->filter('button[type="submit"]');

        // - Select the form that contains this button
        $form = $buttonCrawlerNode->form();

        // Use dynamic email
        $dynamicMail = 'test_' .rand(0, 9999). '@gmail.com';
        /**
         * User data
         */
        $user = [
            "lastName" => "test",
            "firstName" => "test",
            "email" => $dynamicMail,
            "phone" => "xxx xx xxx xx",
            "job" => "Acteur",
            "fax" => "89000",
            "password" => "dbpf4dx2",
            "canLogin" => "1"
            // "_token" => "O5EaKz4l4PY47UE-vh2IkNOTyHqVJcbf4AZo78hk0TQ"
        ];
        $formValues = $this->formatFormNames('user', $user);

        /**
         * First submit should work and redirect users list
         */
        $this->submitOverride($client, $form, $formValues);
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'Redirection to users list failed');
        $this->assertStringNotContainsString("form-error-message", $client->getResponse()->getContent());

        /**
         * Empty the password and use the same email to have form invalid
         */
        $user['password'] = 'qsdfdsfqs';
        $formValues = $this->formatFormNames('user', $user);
        /**
         * Second submit should not work because password and email validation are not good
         */
        $this->submitOverride($client, $form, $formValues);
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Return to the user creation page failed');
        $this->assertStringContainsString("form-error-message", $client->getResponse()->getContent());
        
        /**
         * Invalid the password to have error
         */
        $user['email'] = 'test_' .rand(10000, 99999). '@gmail.com';
        $user['password'] = '';
        $formValues = $this->formatFormNames('user', $user);
        /**
         * Second submit should not work because password and email validation are not good
         */
        $this->submitOverride($client, $form, $formValues);
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Return to the user creation page failed');
        $this->assertStringContainsString("form-error-message", $client->getResponse()->getContent());
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
        $crawler = $client->request('GET', $this->getUserUrl('/edit', 'user_role_user_edit_1_@app.locale'));
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        $this->login($client, 'user_role_user_edit@app.locale');
        $crawler = $client->request('GET', $this->getUserUrl('/edit', 'user_role_user_edit_1_@app.locale'));
        $this->assertResponseIsSuccessful();

        /**
         * Test for user edit form and its validation
         */
        $buttonCrawlerNode = $crawler->filter('button[type="submit"]');

        $dynamicMail = 'test_' .rand(0, 9999). '@gmail.com';
        $user = [
            "lastName" => "test",
            "firstName" => "test",
            "email" => $dynamicMail,
            "phone" => "xxx xx xxx xx",
            "job" => "Acteur",
            "fax" => "89000",
            "canLogin" => "1",
        ];

        // - Select the form that contains this button
        $form = $buttonCrawlerNode->form();
        $formValues = $this->formatFormNames('user_edit', $user);

        /**
         * First submit ok
         */
        $this->submitOverride($client, $form, $formValues);
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'Redirection to users list failed');
        $this->assertStringNotContainsString("form-error-message", $client->getResponse()->getContent());
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

    public function generateUser()
    {
        /**
         * User data
         */
        $dynamicMail = 'test_' .rand(0, 9999). '@gmail.com';
        $user = [
            "lastName" => "test",
            "firstName" => "test",
            "email" => $dynamicMail,
            "phone" => "xxx xx xxx xx",
            "job" => "Acteur",
            "fax" => "89000",
            "password" => "dbpf4dx2",
            "canLogin" => "1"
            // "_token" => "O5EaKz4l4PY47UE-vh2IkNOTyHqVJcbf4AZo78hk0TQ"
        ];

        return $user;
    }
}