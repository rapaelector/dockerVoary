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

        // $loginRedirectMessage = 'Redirecting to /login';
        // // Role access test here everything ok
        // $this->login($client, 'test@gmail.com');
        // $crawler = $client->request('GET', '/user/');
        // $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Not redirected to login page');
        // $this->assertStringContainsString($loginRedirectMessage, $client->getResponse()->getContent());

        // Role access test ok too
        $this->login($client, 'user_role_user_view@app.locale');
        $crawler = $client->request('GET', '/user/');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        // Role access test invalid credential
        $this->login($client, 'user_role_user_edit@app.locale');
        $crawler = $client->request('GET', '/user/');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');
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
        // $this->login($client, 'user_role_user_add@app.locale');
        // $crawler = $client->request('GET', '/user/new');
        $buttonCrawlerNode = $crawler->filter('button[type="submit"]');

        // - Select the form that contains this button
        $form = $buttonCrawlerNode->form();

        // Use dynamic email
        $dynamicMail = 'test_' .(new \DateTime())->getTimestamp(). '@gmail.com';
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
        $user['email'] = 'test_' .(new \DateTime())->getTimestamp(). '@gmail.com';
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

        // Role access test
        // Here not allowed 
        $this->login($client, 'user_role_user_delete@app.locale');
        $crawler = $client->request('GET', $this->getUserUrl('/edit'));
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        // Role access test
        // Here allowed because have good credential
        $this->login($client, 'user_role_user_edit@app.locale');
        $crawler = $client->request('GET', $this->getUserUrl('/edit'));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        /**
         * Test for user edit form and its validation
         */
        $this->login($client, 'user_role_user_edit@app.locale');
        $crawler = $client->request('GET', $this->getUserUrl('/edit'));
        $buttonCrawlerNode = $crawler->filter('button[type="submit"]');

        $dynamicMail = 'test_' .(new \DateTime())->getTimestamp(). '@gmail.com';
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
        $users = $userRepository->findAll();

        if ($email) {
            $user = $userRepository->findOneByEmail($email);
            if (!$user) {
                return $users[2]->getId();
            }

            return $user->getid();
        } else {
            return $users[0]->getId();
        }
    }

    public function generateUser()
    {
        /**
         * User data
         */
        $dynamicMail = 'test_' .(new \DateTime())->getTimestamp(). '@gmail.com';
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