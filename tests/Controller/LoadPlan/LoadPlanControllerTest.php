<?php

namespace App\Tests\Controller\LoadPlan;

use App\Entity\LoadPlan;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\LoadPlanRepository;
use App\Repository\UserRepository;
use App\Tests\Controller\WebCaseTestTrait;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoadPlanControllerTest extends WebTestCase
{
    use WebCaseTestTrait;

    /**
     * Change load plan project economist
     */
    public function testChangeProjectEconomist()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');
        
        /**
         * Test with good user id
         */
        $client->request('POST', $this->generateUrl('/change/project/economist'), [], [], [], json_encode(['id' => $this->getEconomist()->getId()]));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Failed to change load plan project economist');
        $this->assertStringContainsString('Changement economiste', $client->getResponse()->getContent());

        /**
         * Should get errors because user id should not found and throw errors
         * Get random id (int) no matter how many
         */
        $client->request('POST', $this->generateUrl('/change/project/economist'), [], [], [], json_encode(['id' => rand(2000, 2009)]));
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode(), 'Bad data not working ');
        $this->assertStringContainsString('Impossible de', $client->getResponse()->getContent());
    }
    
    /** 
     * Change update realization date
    */
    public function testUpdateRealizationDate()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');

        /**
         * valid date
         */
        $client->request('POST', $this->generateUrl('/update/realization/date'), [], [], [], json_encode([
            'realizationDate' => $this->generateDate(),
        ]));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Realization date not updated');
        $this->assertStringContainsString('Date de devis modifi', $client->getResponse()->getContent());
    }

    public function testUpdateDeadlineDate()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');
        
        /**
         * valid date
         */
        $client->request('POST', $this->generateUrl('/update/deadline'), [], [], [], json_encode([
            'newDeadlineDate' => $this->generateDate(),
        ]));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Deadline date not updated');
        $this->assertStringContainsString('Date butoire modifi', $client->getResponse()->getContent());
    }

    public function testUpdateStartDate()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');

        /**
         * valid date
         */
        $client->request('POST', $this->generateUrl('/update/start/date'), [], [], [], json_encode([
            'startDate' => $this->generateDate(),
        ]));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Start date not updated');
        $this->assertStringContainsString('Date de remise modifi', $client->getResponse()->getContent());
    }

    public function testWeekLoadMetering()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');
        
        /**
         * valid date
         */
        $client->request('GET', '/load/plan/helper/' . $this->generateDate(). '/week/load/metering');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Get week load metering not working');
    }

    private function generateUrl(string $suffix = '')
    {
        return '/load/plan/helper/' . $this->getLoadPlan()->getId() . $suffix;
    }

    private function getProject()
    {
        self::bootKernel();

        // (2) use self::$container to access the service container
        $container = self::$container;

        /** Get userRepository */
        $projects = $container->get(ProjectRepository::class)->findAll();
        // var_dump($projects);

        return $projects[rand(0, count($projects) - 1)];
    }

    private function getLoadPlan()
    {
        // $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('registration');
        self::bootKernel();

        $container = self::$container;

        $loadPlans = $container->get(LoadPlanRepository::class)->findAll();

        return $loadPlans[rand(0, count($loadPlans) -1)];
    }

    private function getEconomist()
    {
        self::bootKernel();

        $container = self::$container;

        $economists = $container->get(UserRepository::class)->findAll();

        return $economists[rand(0, count($economists) -1)];
    }

    private function generateDate()
    {
        return (new \DateTime())->modify('+'.rand(1, 10).' week')->format('Y-m-d');
    }
}