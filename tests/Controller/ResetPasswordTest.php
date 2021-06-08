<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

class ResetPasswordTest extends WebTestCase
{
    public function testResetPasswordPage(): void
    {
        // This calls KernelTestCase::bootKernel(), and creates a
        // "client" that is acting as the browser
        $client = static::createClient();

        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use self::$container to access the service container
        $container = self::$container;

        // Request a specific page
        $crawler = $client->request('GET', '/reset-password');

        // make sure login page works
        $this->assertResponseIsSuccessful();

        // get the form for resetPassword

        $submitButton = $crawler->filter('button[type="submit"]');
        $form = $submitButton->form();
        // submit the Form object with bad credentials
        $client->submit($form, [
            'reset_password_request_form[email]' => 'test@gmail.com'
        ]);
        // value of redirect check email 

        $checkEmailRedirectString = 'Redirecting to /reset-password/check-email';

        $this->assertStringContainsString($checkEmailRedirectString, $client->getResponse()->getContent());

        // $session = $container->get('session.storage');
        // $token = $session->get('ResetPasswordToken');
    }
}