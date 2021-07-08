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

class NgProjectControllerTest extends WebTestCase
{
    use WebCaseTestTrait;

    public function testIndex()
    {
        /**
         * Check if we can access the page
         */
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');
        $crawler = $client->request('GET', $this->generateNgProjectRoute('/follow-up'));
        // First check if we reach the project création page
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Cant reach project new page');

        /** Sending valid data */
        $formValues = $this->formatFormNames('project', $this->generateProject());
        $client->request('POST', $this->generateNgProjectRoute('/follow-up'), $formValues);
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Faild to insert project data');
        
        /** Sending invalid data */
        $formValues = $this->formatFormNames('project', $this->generateProject(true));
        $client->request('POST', $this->generateNgProjectRoute('/follow-up'), $formValues);
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Faild to insert invalid project data');
    }

    public function testCreateContact()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');

        /** Sending valid data */
        // $formValues = $this->formatFormNames('project', $this->generateContact());
        $formValues =  $this->generateContact(false);
        $client->request('POST', '/project/create/contact', [], [], [], json_encode($formValues));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Faild to insert contact data');

        /** Sending invalid data */
        // $this->login($client, 'test@gmail.com');
        $formValues = $this->generateContact(true);
        $client->request('POST', '/project/create/contact', [], [], [], json_encode($formValues));
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode(), 'Test for project new contact invalid data failed');
    }

    public function testSaveExchangeHistory()
    {
        $client = static::createClient();
        $this->login($client, 'test@gmail.com');

        /** 
         * Sending valid data
         * First type_relaunch
         */
        $formValues = $this->generatePilotingProject(false, Project::EXCHANGE_HISTORY_RELAUNCH_TYPE);
        $client->request('POST', $this->generateNgProjectRoute('/save/exchange-history'), $formValues);
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Faild to insert exchange history data');

        /** 
         * Sending valid data
         * Second type_next_date
         */
        $formValues =$this->generatePilotingProject(false, Project::EXCHANGE_HISTORY_NEXT_STEP_TYPE);
        $client->request('POST', $this->generateNgProjectRoute('/save/exchange-history'), $formValues);
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Faild to insert exchange history data type_next_date');
    }

    public function generateNgProjectRoute($prefix)
    {
        $projectId = $this->getProjectId();
        return '/project/' .$projectId . $prefix;
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

    public function generateProject(bool $invalidData = false)
    {
        $caseType = [
            "caseType.earthWorks",
            "caseType.bigWork",
            "caseType.plumbing",
            "caseType.electricity",
            "caseType.adminFile",
            "caseType.frame",
            "caseType.blanket",
            "caseType.locksmith",
        ];

        if ($invalidData) {
            $caseType = [
                'error',
                'error',
                'error',
                'error',
            ];
        }

        return [
            'siteCode' => "CDL",
            'roadmap' => false,
            'projectOwner' => "Ouvrage",
            'projectManager' => "Main",
            'billingAddres' => null,
            'siteAddress' => null,
            'descriptionOperation' => "Lorem ipsum dolor sit amet",
            'businessCharge' => null,
            'economist' => null,
            'norm1090' => 2,
            'marketType' => "typeMarket.shadows",
            'bonhomePercentage' => "bonhommePercentage.10percent",
            'disaSheetValidation' => [
                "disaSheetValidation.signed_quote",
                "disaSheetValidation.customer_order_form",
                "disaSheetValidation.subcontract",
            ],
            'paymentChoice' => false,
            'paymentPercentage' => 12.0,
            'depositeDateEdit' => (new \DateTime())->format('d/m/Y'),
            'clientCondition' => "Lorem ipsum dolor sit amet",
            'quoteValidatedMDE' => "Lorem ipsum dolor sit amet",
            'quoteValidatedMDEDate' => (new \DateTime())->format('d/m/Y'),
            'globalAmount' => 2000,
            'amountSubcontractedWork' => 90000,
            'amountBBISpecificWork' => 21000,
            'caseType' => $caseType,
            'planningProject' => "Lorem ipsum dolor sit amet",
            'recordAssistant' => null,
            'contact' => null,
            'ocbsDriver' => null,
            'tceDriver' => null,
            'prospect' => null,
            'encryptiontype' => "Réalisation / consultation",
            'notApplicable' => false,
            'priorizationOfFile' => "NORMAL",
            'answerForThe' => "Lorem ipsum dolor sit amet",
            'folderNameOnTheServer' => "Lorem ipsum dolor sit amet",
            'comment' => "Lorem ipsum dolor sit amet",
            'completion' => "29",
            'lastRelaunch' => null,
            'relaunches' => null,
            'pcDeposit' => true,
            'architect' => false,
            'name' => 'Test lorem ipsum',
            'scope' => "typeMarket.public_market",
        ];
    }

    public function generateContact(bool $invalidData = false)
    {  
        $dynamicEmail = 'projectUser' .(new \DateTime())->getTimestamp(). '@app.locale';
        if ($invalidData) {
            $dynamicEmail = 'test@gmail.com';
        }

        return [
            "lastName" => "Lionel",
            "firstName" => "Messi",
            "email" => $dynamicEmail,
            "phone" => "xxx xxx xx",
            "job" => "xxx xx xx",
            "fax" => "xx xx xx"
        ];
    }

    public function generatePilotingProject(bool $invalidData = false, $type = Project::EXCHANGE_HISTORY_RELAUNCH_TYPE)
    {
        // {"flag":"type_next_step","nextStepDate":"2021-07-23T21:00:00.000Z","description":"12","percentage":"98","archi":"1","archiUser":2,"date":null}
        $pilotingProject = [
            'relaunchDate' => (new \DateTime())->format('d/m/Y'),
            'flag' => 'type_relaunch',
            'description' => '',
            'percentage' => '40',
            'archi' => '1',
            'date' => null,
            'archiUser' => 2
        ];

        if ($invalidData) {
            $pilotingProject['relaunchDate'] = null;
            $pilotingProject['flag'] = '';
        }

        if ($type == Project::EXCHANGE_HISTORY_RELAUNCH_TYPE) {
            $pilotingProject['relaunchDate'] = (new \DateTime())->format('d/m/Y');
            $pilotingProject['flag'] = Project::EXCHANGE_HISTORY_RELAUNCH_TYPE;
        } else if ($type == Project::EXCHANGE_HISTORY_NEXT_STEP_TYPE) {
            $pilotingProject['nextStepDate'] = (new \DateTime())->format('d/m/Y');
            $pilotingProject['flag'] = Project::EXCHANGE_HISTORY_NEXT_STEP_TYPE;
        }

        return $pilotingProject;
    }
}