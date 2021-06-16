<?php

namespace App\DataFixtures;

use App\Entity\Project;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\SerializerInterface;

class ProjectFixture extends Fixture
{
    /** @var ObjectNormalizer $objectNormalizer */
    private $objectNormalizer;

    /** @var SerializerInterface $serializer */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        // $this->objectNormalizer = $objectNormalizer;
        $this->serializer = $serializer;
    }

    public function load(ObjectManager $manager)
    {
        $this->generateFixture($manager);
    }

    public function generateFixture(ObjectManager $manager)
    {
        $project = new Project();
        $mockData = [
            "roadmap" => true,
            "prospect" => "8",
            "siteCode" => "TTP",
            "projectOwner" => "Lorem ipsum ouvrage",
            "projectManager" => "Test",
            "descriptionOperation" => "Lorem ipsum",
            "contact" => [
                "lastName" => "6tso",
                "firstName" => "idealy",
                "email" => "6tso@gmail.com",
                "phone" => "0342440768",
                "job" => "tester",
                "fax" => "lorem",
                "password" => md5('lorem ispum'),
                "rawAddress" => "Lorem",
            ],
            "billingAddres" => [
                "name" => "Madagascar",
                "phone" => "XXXXX XXXX",
                "fax" => "5678",
                "line1" => "Antananarivo",
                "line2" => "Antsimondrano",
                "line3" => "Itaosy",
                "postalCode" => "102",
                "city" => "qsdfqsdf",
                "country" => "MG",
            ],
            "siteAddress" => [
                "name" => "Test adresse chantier",
                "phone" => "899 00 789",
                "fax" => "787 78 608",
                "line1" => "Antananarivo",
                "line2" => "Antsimondrano",
                "line3" => "Itaosy",
                "postalCode" => "fqsdfqds",
                "city" => "Centre afrique",
                "country" => "ZA",
            ],
            "marketType" => "typeMarket.work_on_existing",
            "encryptiontype" => "Réalisation / consultation",
            "bonhomePercentage" => "bonhommePercentage.10percent",
            "norm1090" => 3,
            "notApplicable" => true,
            "globalAmount" => 2000,
            "amountSubcontractedWork" => 90000,
            "amountBBISpecificWork" => 21000,
            "disaSheetValidation" => [
                "disaSheetValidation.customer_order_form",
                "disaSheetValidation.authorization_letter",
            ],
            "paymentChoice" => "1",
            "paymentPercentage" => 12.4,
            "depositeDateEdit" => "2021-06-17",
            "clientCondition" => "Lorem ipsum dolor sit, amet consectetur adipisicing elit. Fugiat minima amet nihil quas saepe rerum assumenda totam modi facere possimus repellendus, quisquam adipisci non eum sit esse consequuntur! Ex, totam!",
            "quoteValidatedMDE" => "12",
            "quoteValidatedMDEDate" => "2021-06-25",
            "caseType" => [
                "caseType.bigWork",
                "caseType.plumbing",
                "caseType.electricity",
                "caseType.adminFile",
            ],
            "planningProject" => "Lorem ipsum dolor sit amet",
            "priorizationOfFile" => "NORMAL",
            "answerForThe" => "Idealy henintsoa qdsfffffffffffffffffffffff Idealy henintsoa qdsfffffffffffffffffffffff",
            "folderNameOnTheServer" => "Xfds DDc dddff",
            // "recordAssistant" => "47",
            // "ocbsDriver" => "59",
            // "tceDriver" => "47",
        ];
        $formattedObject = $this->serializer->denormalize($mockData, Project::class, null, []);

        $manager->persist($formattedObject);
        $manager->flush();
    }
}
