<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // make sure login page works
        $this->assertResponseIsSuccessful();

        // this message is part of the content of the response on login failure
        $loginRedirectMessage = 'Redirecting to /login';

        // find the login form submit button
        $buttonCrawlerNode = $crawler->filter('button[type="submit"]');

        // select the form that contains this button
        $form = $buttonCrawlerNode->form();

        // submit the Form object with bad credentials
        $client->submit($form, [
            'email'    => 'test@gmail.com',
            'password' => 'Test12',
        ]);

        $this->assertStringContainsString($loginRedirectMessage, $client->getResponse()->getContent());
        
        // submit the Form object with good credentials
        $client->submit($form, [
            'email'    => 'test@gmail.com',
            'password' => 'Test123',
        ]);

        // After a successful login, we should not be redirected to /login again
        $this->assertStringNotContainsString($loginRedirectMessage, $client->getResponse()->getContent());
    }

    public function testLogout(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/logout');

        // logout page should redirect the user
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
