<?php

namespace App\Tests\Controller\Project;

use App\Form\Project\NgProjectType;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
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

        /**
         * Sending form data
         */
        $buttonCrawlerNode = $crawler->filter('button[type="submit"]');
        $form = $buttonCrawlerNode->form();
        $formValues = $this->formatFormNames('project', $this->generateProject());
        $this->submitOverride($client, NgProjectType::class, $formValues);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Faild to insert project data');
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

    public function generateProject()
    {
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
            'caseType' => [
                "caseType.earthWorks",
                "caseType.bigWork",
                "caseType.plumbing",
                "caseType.electricity",
                "caseType.adminFile",
                "caseType.frame",
                "caseType.blanket",
                "caseType.locksmith",
            ],
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
            'name' => null,
            'scope' => "typeMarket.public_market",
        ];
    }
}