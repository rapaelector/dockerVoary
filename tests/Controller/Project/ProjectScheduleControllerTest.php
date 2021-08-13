<?php

namespace App\Tests\Controller\Project;

use App\Entity\LoadPlan;
use App\Repository\ProjectRepository;
use App\Repository\LoadPlanRepository;
use App\Tests\Controller\WebCaseTestTrait;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProjectScheduleControllerTest extends WebTestCase
{
    use WebCaseTestTrait;

    public function testIndex() 
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');
        $crawler = $client->request('GET', '/project/scheduler/');
        $pageTitle = 'Échéancier du carnet de commande';

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant reach order book schedule');
        $this->assertStringContainsString($pageTitle, $client->getResponse()->getContent());
    }

    public function testResources() 
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');
        $crawler = $client->request('GET', '/project/scheduler/resources');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant get resources order book schedule');
        $this->assertStringContainsString('resources', $client->getResponse()->getContent());
    }

    public function testEvents() 
    {
        /**
         * Test whithout params
         */
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');

        $crawler = $client->request('GET', '/project/scheduler/events');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant get events order book schedule');

        /**
         * Test whit params
         */
        $start = (new \DateTime())->modify(rand(1, 100).' day');
        $end = (clone $start)->modify(rand(1, 5).' month');

        $crawler = $client->request('GET', '/project/scheduler/events?start='.$start->format('Y-m-d').'&end='.$end->format('Y-m-d'));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant get events order book schedule');
    }

}