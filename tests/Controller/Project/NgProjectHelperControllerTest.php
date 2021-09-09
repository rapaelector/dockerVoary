<?php

namespace App\Tests\Controller\Project;

use App\Form\Project\NgProjectType;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use App\Entity\Constants\Project;
use App\Tests\Controller\WebCaseTestTrait;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Knp\Snappy\Pdf;

class NgProjectHelperControllerTest extends WebTestCase
{
    use WebCaseTestTrait;

    public function testProjectSchedulerProject()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');
        $crawler = $client->request('GET', $this->generateUrl('/scheduler/project'));
        // First check if the page have no bugs
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant get project scheduler');
    }

    public function testConfig()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');
        $crawler = $client->request('GET', 'project/ng/config');
        // First check if the page have no bugs
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant get project config');
    }

    public function testUpdateProject()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');

        /**
         * Test sending data
         */
        $crawler = $client->request('GET',$this->generateUrl('/update/project'));
        $client->request('POST', $this->generateUrl('/update/project'), [], [], [], json_encode($this->generateProjectOrderBook(false)));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Saving project order book failed');
        $successMessage = 'Modification du carnet';
        $this->assertStringContainsString($successMessage, $client->getResponse()->getContent());

        /**
         * New load plan send with fake data should get bad request error code 400 
         */
        $client->request('POST', $this->generateUrl('/update/project'), [], [], [], json_encode($this->generateProjectOrderBook(true)));
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode(), 'Saving project order book fake data failed');
        $errorMessage = 'La modification du carnet';
        $this->assertStringContainsString($errorMessage, $client->getResponse()->getContent());
    }

    public function testRemoveToPlanning()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');

        $crawler = $client->request('GET',$this->generateUrl('/remove/to/planning'));
        $client->request('POST', $this->generateUrl('/update/project'));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Saving project order book failed');
    }

    private function generateUrl(string $prefix = '')
    {
        return '/project/' .$this->getProject()->getId() . $prefix;
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

    private function generateProjectOrderBook(bool $isValid = true)
    {
        $types = ['type_delivery_date', 'type_work_duration'];
        $type = $types[rand(0, 1)];
        $date = (new \DateTime())->modify(rand(1, 10). ' weeks');
        $workDuration = rand(1, 24);
        $deliveryDate = (clone $date)->format('Y-m-d');

        if ($type == 'type_work_duration') {
            $deliveryDate = null;
        } else if ($type == 'type_delivery_date') {
            $workDuration = null;
        }

        return [
            "marketType" => "typeMarket.agricultural_bat",
            "paymentPercentage" => rand(1, 100),
            "depositeDateEdit" => $deliveryDate,
            "name" => "Pied sur la lune",
            "provisionalAmount" => !$isValid ? rand(1000, 100000) : 'lorem',
            "deliveryDate" => (clone $date)->modify(rand(1, 3). ' weeks')->format('Y-m-d'),
            "startingDate" => (clone $date)->modify(rand(1, 5). ' weeks')->format('Y-m-d'),
            "workDuration" => $workDuration,
            "type" => $type,
        ];
    }
}