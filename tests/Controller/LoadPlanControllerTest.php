<?php

namespace App\Tests\Controller;

use App\Entity\LoadPlan;
use App\Repository\ProjectRepository;
use App\Repository\LoadPlanRepository;
use App\Tests\Controller\WebCaseTestTrait;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoadPlanControllerTest extends WebTestCase
{
    use WebCaseTestTrait;

    public function testIndex()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');
        $crawler = $client->request('GET', '/load/plan/');
        $pageTitle = 'Plan de charge économiste';
        // First check if the load plan list page have no bugs
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant reach load plan list');
        $this->assertStringContainsString($pageTitle, $client->getResponse()->getContent());

        /** 
         * Create role checker
         * Have a good role
         */
        $this->login($client, 'user_role_load_plan_view@app.locale');
        $crawler = $client->request('GET', '/load/plan/');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant reach load plan list');
        $this->assertStringContainsString($pageTitle, $client->getResponse()->getContent());

        /**
         * Bad role
         * Should be forbidden
         */
        $this->login($client, 'user_role_load_plan_add@app.locale');
        $crawler = $client->request('GET', '/load/plan/');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Cant reach load plan list');
        // $this->assertStringContainsString($pageTitle, $client->getResponse()->getContent());
    }

    public function testNew()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');

        /**
         * New load plan are managed by angularJS then send data manualy
         */
        $crawler = $client->request('GET', '/load/plan/new');
        $client->request('POST', '/load/plan/new', [], [], [], json_encode($this->generateLoadPlanData()));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Saving load plan failed');
        $this->assertStringContainsString('data', $client->getResponse()->getContent());

        /**
         * New load plan send with fake data should get bad request error code 400 
         */
        $client->request('POST', '/load/plan/new', [], [], [], json_encode($this->generateLoadPlanData(true)));
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode(), 'Saving load plan fake data failed');
        $this->assertStringContainsString('errors', $client->getResponse()->getContent());
        
        /**
         * Check with bad role
         * Should be forbidden
         */
        $this->login($client, 'user_role_load_plan_delete@app.locale');
        $client->request('POST', '/load/plan/new', [], [], [], json_encode($this->generateLoadPlanData(true)));
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Saving load plan fake data failed');
    }

    public function testEdit()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');

        $client->request('POST', $this->generateUrl('/load/plan/', '/edit'), [], [], [], json_encode($this->generateLoadPlanData()));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Edit load plan should work here(good request)');
        $this->assertStringContainsString('message', $client->getResponse()->getContent());

        $client->request('POST', $this->generateUrl('/load/plan/', '/edit'), [], [], [], json_encode($this->generateLoadPlanData(true)));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Edit load plan with bad request');
        $this->assertStringContainsString('message', $client->getResponse()->getContent());
        
        $this->login($client, 'user_role_load_plan_delete@app.locale');
        $client->request('POST', $this->generateUrl('/load/plan/', '/edit'), [], [], [], json_encode($this->generateLoadPlanData(true)));
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Edit load plan with bad request');
    }

    /**
     * Test delete for load plan
     * TODO:
     *      - Every delete test should be have csrf_token
     *      - Every delete test should be HTTP_OK code 200 but due to the csrf_token missing use HTTP_FOUND 302 use redirect path to any page you want (fix this)
     */
    public function testDelete()
    {
        
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');
        $loadPlanId = $this->getLoadPlan()->getId();
        
        self::bootKernel();
        $container = self::$container;

        // Create token for delete loadPlan
        $token = $container->get('security.csrf.token_manager')->getToken('delete'.$loadPlanId)->getValue();

        $client->request('DELETE', $this->generateUrl('/load/plan/', '/delete'), [
            '_token' => $token,
        ]);

        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'Delete load plan failed');
        $this->assertStringContainsString('Redirecting to', $client->getResponse()->getContent());
    }
    
    public function testProjects()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');

        $crawler = $client->request('GET', '/load/plan/projects');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Get projects from load plan failed');
        
        $this->login($client, 'user_role_load_plan_view@app.locale');
        $crawler = $client->request('GET', '/load/plan/projects');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Get projects from load plan failed');
        
        $this->login($client, 'user_role_load_plan_edit@app.locale');
        $crawler = $client->request('GET', '/load/plan/projects');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Get projects from load plan failed');
    }
    
    public function testConfig()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');

        $crawler = $client->request('GET', '/load/plan/config');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Get load plan config failed');
        $this->assertStringContainsString('taskTypes', $client->getResponse()->getContent());
        $this->assertStringContainsString('studyTime', $client->getResponse()->getContent());

        $this->login($client, 'user_role_load_plan_view@app.locale');
        $crawler = $client->request('GET', '/load/plan/config');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Get load plan config failed');
        
        $this->login($client, 'user_role_load_plan_delete@app.locale');
        $crawler = $client->request('GET', '/load/plan/config');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Get load plan config failed');
    }

    public function testLoadPlan()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');

        $crawler = $client->request('GET', $this->generateUrl('/load/plan/'));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Get load plan failed');
        
        $this->login($client, 'user_role_load_plan_view@app.locale');
        $crawler = $client->request('GET', $this->generateUrl('/load/plan/'));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Get load plan failed');
        
        $this->login($client, 'user_role_load_plan_delete@app.locale');
        $crawler = $client->request('GET', $this->generateUrl('/load/plan/'));
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Get load plan failed');
    }
    /**
     * Create load plan data
     * 
     * @param bool $invalidData boolean if the function return should be invalid
     * @return array 
     */
    private function generateLoadPlanData(bool $invalidData = false)
    {
        $taskTypes = [
            LoadPlan::METER_CONSULTATION,
            LoadPlan::PRE_STUDY,
            LoadPlan::SKETCH,
            LoadPlan::ENCRYPTION,
        ];

        $data = [
            'project' => $invalidData ? null : $this->getProject()->getId(),
            'effectiveStudyTime' => LoadPlan::STUDY_TIME[rand(0, 7)],
            'estimatedStudyTime' => LoadPlan::STUDY_TIME[rand(0, 7)],
            'natureOfTheCosting' => $taskTypes[rand(0, 3)],
            'start' => "2021-09-20",
            'end' => "2021-09-26",
            'deadline' => (new \DateTime())->modify(rand(1, 100). ' day')->format('Y-m-d'),
            'realizationQuotationDate' => (new \DateTime())->modify(rand(1, 10). ' week')->format('Y-m-d'),
        ];

        return $data;
    }

    private function generateUrl(string $url = null, string $suffix = null)
    {
        return $url .$this->getLoadPlan()->getId(). $suffix;
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
}