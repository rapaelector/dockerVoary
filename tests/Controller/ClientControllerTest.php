<?php

namespace App\Tests\Controller;

use App\Repository\ClientRepository;
use App\Tests\Controller\WebCaseTestTrait;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ClientControllerTest extends WebTestCase
{
    use WebCaseTestTrait;

    /**
     * - Test prospect list(client list)
     */
    public function testIndex(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/client/');
        
        $loginRedirectMessage = 'Redirecting to /login';
        $crawler = $client->request('GET', '/client/');
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'Not redirected to login page');
        $this->assertStringContainsString($loginRedirectMessage, $client->getResponse()->getContent());

        $this->login($client, 'user_role_client_edit@app.locale');
        $crawler = $client->request('GET', '/client/');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');
        // $this->assertResponseIsSuccessful();

        $this->login($client, 'user_role_client_view@app.locale');
        $crawler = $client->request('GET', '/client/');
        $this->assertResponseIsSuccessful('unable to access page with required permission');
    }

    /**
     * - Test when create a new prospect(client)
     */
    public function testNew(): void
    {
        $client = static::createClient();

        $this->login($client, 'user_role_client_edit@app.locale');
        $crawler = $client->request('GET', '/client/new');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        $this->login($client, 'user_role_client_add@app.locale');
        $crawler = $client->request('GET', '/client/new');
        $this->assertResponseIsSuccessful();
    }

    public function testShow()
    {
        $client = static::createClient();

        $this->login($client, 'user_role_client_edit@app.locale');
        $showClientUrl = $this->getClientUrl();
        $crawler = $client->request('GET', $showClientUrl);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        $this->login($client, 'user_role_client_view@app.locale');
        $crawler = $client->request('GET', $showClientUrl);
        $this->assertResponseIsSuccessful();
    }

    public function testEdit(): void
    {
        $client = static::createClient();

        $this->login($client, 'user_role_client_delete@app.locale');
        $editClientUrl = $this->getClientUrl('/edit');
        $crawler = $client->request('GET', $editClientUrl);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        $this->login($client, 'user_role_client_edit@app.locale');
        $crawler = $client->request('GET', $editClientUrl);
        $this->assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        $client = static::createClient();

        $this->login($client, 'user_role_client_edit@app.locale');
        $deleteClientUrl = $this->getClientUrl('/delete');
        $crawler = $client->request('DELETE', $deleteClientUrl);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        $this->login($client, 'user_role_client_delete@app.locale');
        $crawler = $client->request('DELETE', $deleteClientUrl);
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'Delete success');
    }

    public function getClientUrl($urlSufix = '', $email = '')
    {
        $clientId = $this->getClientId($email);
        
        return '/client/' .$clientId. '' .$urlSufix;
    }

    public function getClientId($email = '')
    {
        /** store the user id */
        $clientId = null;
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use self::$container to access the service container
        $container = self::$container;

        /** Get clientRepository */
        $clientRepository = $container->get(ClientRepository::class);
        
        $clients = $clientRepository->findAll();
        $clientId = $clients[0]->getId();

        return $clientId;
    }
}