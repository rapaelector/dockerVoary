<?php

namespace App\Tests\Controller;

use App\Repository\ProjectRepository;
use App\Tests\Controller\WebCaseTestTrait;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Knp\Snappy\Pdf;

class ProjectControllerTest extends WebTestCase
{
    public function testPdf()
    {
        $client = static::createClient();
        $crawler = $client->request('GET',$this->generateProjectUrl('/pdf', 'preview=true&nofooter=true'));
        // $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Pdf page can not reached');
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'Pdf page can not reached');
    }

    public function getProjectId()
    {
        /** store the user id */
        $projectId = null;
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use self::$container to access the service container
        $container = self::$container;

        /** Get projectRepository */
        $projectRepository = $container->get(ProjectRepository::class);
        
        $projects = $projectRepository->findAll();
        $projectId = $projects[0]->getId();

        return $projectId;
    }

    public function generateProjectUrl($prefix = '', $parameters = '')
    {
        return '/project/' .$this->getProjectId(). $prefix. '?' .$parameters;
    }
}