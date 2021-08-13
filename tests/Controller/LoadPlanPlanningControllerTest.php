<?php

namespace App\Tests\Controller;

use App\Entity\LoadPlan;
use App\Repository\ProjectRepository;
use App\Repository\LoadPlanRepository;
use App\Tests\Controller\WebCaseTestTrait;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoadPlanPlanningControllerTest extends WebTestCase
{
    use WebCaseTestTrait;

    public function testIndex()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');
        $crawler = $client->request('GET', '/load/plan/planning/');
        $pageTitle = 'Planning du plan de charge économiste';

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant reach load plan list');
        $this->assertStringContainsString($pageTitle, $client->getResponse()->getContent());
    }

    public function testResources()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');
        $crawler = $client->request('GET', '/load/plan/planning/resources');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant get load plan resources');
        $this->assertStringContainsString('resources', $client->getResponse()->getContent());
    }

    public function testEvents()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');
        $crawler = $client->request('GET', '/load/plan/planning/events');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant get load plan planning event');
        $this->assertStringContainsString('events', $client->getResponse()->getContent());

        /**
         * Test whit params
         */
        $start = (new \DateTime())->modify(rand(1, 100).' day');
        $end = (clone $start)->modify(rand(1, 5).' month');
        $crawler = $client->request('GET', '/load/plan/planning/events?start='.$start->format('Y-m-d').'&end='.$end->format('Y-m-d'));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant reach load plan list');
    }
}