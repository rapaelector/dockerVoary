<?php

namespace App\Tests\Controller\Project;

use App\Repository\ClientRepository;
use App\Tests\Controller\WebCaseTestTrait;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProjectControllerTest extends WebTestCase
{
    use WebCaseTestTrait;

    public function testIndex()
    {
        $client = static::createClient();
        $this->login($client, 'user_role_project_view@app.locale');
        $crawler = $client->request('GET', '/project/case/');
        // First check if the project list page have no bugs
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant reach project list');

        $this->login($client, 'user_role_client_view@app.locale');
        $crawler = $client->request('GET', '/project/case/');
        // First check if the project list page have no bugs
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Failed to redirect to login page');
    }
}