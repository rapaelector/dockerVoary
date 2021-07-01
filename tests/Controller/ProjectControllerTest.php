<?php

namespace App\Tests\Controller;

use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use App\Tests\Controller\WebCaseTestTrait;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Knp\Snappy\Pdf;

class ProjectControllerTest extends WebTestCase
{
    use WebCaseTestTrait;

    public function testPdf()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');
        $crawler = $client->request('GET',$this->generateProjectUrl('/pdf', 'preview=true&nofooter=true'));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant reach project pdf preview page');
    }

    public function testIndex()
    {
        $client = static::createClient();
        $this->login($client, 'user_role_project_view@app.locale');
        $crawler = $client->request('GET', '/project/');
        // First check if the project list page have no bugs
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant reach project list');

        $this->login($client, 'user_role_project_delete@app.locale');
        $crawler = $client->request('GET', '/project/');
        // First check if the project list page have no bugs
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Cant reach project list');
    }

    public function testNew()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');
        $crawler = $client->request('GET', '/project/new');
        // First check if we reach the project création page
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant reach project new page');

        // Create project with valid data then redirect to the project list
        $buttonCrawlerNode = $crawler->filter('button[type="submit"]');
        $form = $buttonCrawlerNode->form();
        $formValues = $this->formatFormNames('project', $this->generateProject());

        $this->submitOverride($client, $form, $formValues);
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'Faild to insert project data');
        $this->assertStringNotContainsString("form-error-message", $client->getResponse()->getContent());

        // Create project with invalid data
        $formValues = $this->formatFormNames('project', $this->generateProject(true));
        $this->submitOverride($client, $form, $formValues);
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Faild to test invalid project data');
        $this->assertStringContainsString("form-error-message", $client->getResponse()->getContent());

        // TODO : create user role checker but not now
        /**
         * First check if user with good access can reach the page
         */
        $this->login($client, 'user_role_project_add@app.locale');
        $crawler = $client->request('GET', '/project/new');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Bad project role checking');

        // Second check if user with bad access cant reach the page
        $this->login($client, 'user_role_project_edit@app.locale');
        $crawler = $client->request('GET', '/project/new');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Project check user role not work');
    }

    public function testShow()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');
        $crawler = $client->request('GET', $this->generateProjectUrl('/show'));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant reach project view');
        // TODO : create more test like adding some new project, check user role, check data exception

        $this->login($client, 'user_role_project_view@app.locale');
        $crawler = $client->request('GET', $this->generateProjectUrl('/show'));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Project check user role not work');

        $this->login($client, 'user_role_project_delete@app.locale');
        $crawler = $client->request('GET', $this->generateProjectUrl('/show'));
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Project check user role not work');
    }

    public function testEdit()
    {
        $projectEditMessage = 'Cant reach project edit';

        $client = static::createClient();
        $this->login($client, 'user_role_project_edit@app.locale');
        $crawler = $client->request('GET', $this->generateProjectUrl('/edit'));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), $projectEditMessage);
        
        $this->login($client, 'user_role_project_view@app.locale');
        $crawler = $client->request('GET', $this->generateProjectUrl('/edit'));
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), $projectEditMessage);

        // TODO uncomment under codes and need to undertand why its not work for now
        // $this->login($client, 'user_role_project_edit@app.locale');
        // $crawler = $client->request('GET', $this->generateProjectUrl('/edit'));
        // $buttonCrawlerNode = $crawler->filter('button[type="submit"]');
        // $form = $buttonCrawlerNode->form();
        // $formValues = $this->formatFormNames('project', $this->generateProject());

        // $this->submitOverride($client, $form, $formValues);
        // $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'Faild to test invalid project data');
        // $this->assertStringNotContainsString("form-error-message", $client->getResponse()->getContent());
        
        // // Send edit form with invalid data
        // $formValues = $this->formatFormNames('project', $this->generateProject(true));
        // $this->submitOverride($client, $form, $formValues);
        // $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Faild to test invalid project data');
        // $this->assertStringContainsString("form-error-message", $client->getResponse()->getContent());
    }

    public function testDelete()
    {
        $client = static::createClient();
        $this->login($client, 'user_role_project_delete@app.locale');
        $crawler = $client->request('DELETE', $this->generateProjectUrl('/delete'));
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'Delete project error');

        $this->login($client, 'user_role_project_add@app.locale');
        $crawler = $client->request('DELETE', $this->generateProjectUrl('/delete'));
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Delete project error');
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

    /**
     * @var boolean $invalidData = false
     */
    public function generateProject(bool $invalidData = false)
    {
        $dynamicMail = '_projectUserMail_' .(new \DateTime())->getTimestamp(). '@locale.app';
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use self::$container to access the service container
        $container = self::$container;

        /** Get userRepository */
        $userRepository = $container->get(UserRepository::class);
        $users = $userRepository->findAll();
        $user = $users[0];

        /**
         * Commented field here are mostly a relation field
         * TODO : Increase data structure to be more real, add relation with other entity by relation fields
         */
        return [
            // "roadmap" => $invalidData ? "1" : rand(0, 1),
            // "prospect" => "9",
            // "siteCode" => "TTPC",
            // "projectOwner" => "Henintsoa",
            // "projectManager" => "Henintsoa",
            // "contactSelect" => "36",
            "businessCharge" => $user->getId(),
            "economist" => $user->getId(),
            "descriptionOperation" => "Lorem ipsum dolor sit amet consectetur",
            "contact" => [
                "lastName" => $invalidData ? "" : "_henintsoa",
                "firstName" => "Idealy",
                "email" => $invalidData ? "misterData@gmail.com" : 'user_' .(new \DateTime())->getTimestamp(). '@app.locale',
                "phone" => "xxx xx xxx xx",
                "job" => "",
                "fax" => "",
            ],
            // "billingAddres" => [
            //     "name" => "Livraison",
            //     "phone" => "034 887 6788",
            //     "fax" => "",
            //     "line1" => "",
            //     "line2" => "",
            //     "line3" => "",
            //     "postalCode" => "",
            //     "city" => "",
            //     "country" => "US",
            // ],
            // "siteAddress" => [
            //     "name" => "Livraison",
            //     "phone" => "xxx xx xxx xx",
            //     "fax" => "xxx xx xxx xx",
            //     "line1" => "lorem ipsum dolor",
            //     "line2" => "restore me",
            //     "line3" => "Lorem ipsum dolor",
            //     "postalCode" => "Test",
            //     "city" => "Majunga",
            //     "country" => "ZA",
            // ],
            // "marketType" => "typeMarket.asbestos_removal",
            // "encryptiontype" => "Réalisation / consultation",
            // "bonhomePercentage" => "bonhommePercentage.20percent",
            // "norm1090" => "3",
            // "notApplicable" => 1,
            // "globalAmount" => "9000",
            // "amountSubcontractedWork" => "2000",
            // "amountBBISpecificWork" => "9800",
            // "disaSheetValidation" => [
            //     "disaSheetValidation.signed_quote",
            //     "disaSheetValidation.customer_order_form",
            //     "disaSheetValidation.authorization_letter",
            // ],
            // "paymentChoice" => 0,
            // "paymentPercentage" => "21",
            // "depositeDateEdit" => "2021-07-08",
            // "clientCondition" => "Lorem",
            // "quoteValidatedMDE" => "DVS 89000",
            // "quoteValidatedMDEDate" => "2021-06-25",
            "caseType" => [
                // "caseType.bigWork",
                // "caseType.plumbing",
                // "caseType.frame",
            ],
            "planningProject" => "Lorem ipsum dolor sit amet",
            "priorizationOfFile" => "SANS SUITE",
            "answerForThe" => "Lorem ipsum dolor sit amet",
            // "businessCharge" => "Lorem ipsum dolor sit amet",
            // "economist" => "Lorem",
            // "folderNameOnTheServer" => "Folder\SDF",
            // "recordAssistant" => $user->getId(),
            // "ocbsDriver" => $user->getId(),
            // "tceDriver" => $user->getId(),
        ];
    }
}