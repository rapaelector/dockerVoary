<?php

namespace App\DataTables\Filter;

use App\Entity\Client;
use App\Entity\Address;
use App\Entity\Project;
use App\Entity\LoadPlan;
use App\Entity\Constants\Project as ProjectConstants;
use App\DataTables\Filter\FilterConstants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Contracts\Translation\TranslatorInterface;

class FilterOptionsProvider
{

    public function __construct(TranslatorInterface $translator, EntityManagerInterface $em)
    {
        $this->translator = $translator;
        $this->em = $em;
    }

    const OPTIONS = [
        'email' => [
            'type' => 'text',
            'width' => '60px'
        ],
        'lastname' => [
            'type' => 'text',
            'width' => '60px'
        ],
        'firstname' => [
            'type' => 'text',
            'width' => '60px'
        ],
        'fax' => [
            'type' => 'text',
            'width' => '60px'
        ],
        'client_name' => [
            'type' => 'text',
        ],
        'client_number' => [
            'type' => 'text',
        ],
        'client_type' => [
            'type' => 'text',
        ],
        'client_code_postal' => [
            'type' => 'text',
        ],
        'client_country' => [
            'type' => 'choice',
            'choices' => [],
        ],
        'client_activity' => [
            'type' => 'choice',
            'choices' => [],
        ],
        'client_created_at' => [
            'type' => 'daterange',
        ],
        'folder_name_on_the_server' => [
            'type' => 'text',
        ],
        'project_description_area' => [
            'type' => 'text',
        ],
        'postal_code' => [
            'type' => 'text',
        ],
        'contact_name' => [
            'type' => 'text',
        ],
        'project_site_address' => [
            'type' => 'text',
        ],
        'business_project_charge' => [
            'type' => 'choice',
            'choices' => [],
        ],
        'project_market_type' => [
            'type' => 'choice',
            'choices' => [],
        ],
        'pc_deposit' => [
            'type' => 'choice',
            'choices' => [],
        ],
        'architect' => [
            'type' => 'choice',
            'choices' => [],
        ],
        'site_code' => [
            'type' => 'choice',
            'choices' => [],
        ],
        'user_email' => [
            'type' => 'choice',
            'choices' => [],
        ],
        'roadmap' => [
            'type' => 'choice',
            'choices' => [],
        ],
        'load_plan_activity' => [
            'type' => 'choice',
            'choices' => [],
        ],
        'project_folder_name_on_the_server' => [
            'type' => 'choice',
            'choices' => [],
        ],
        'business_charge' => [
            'type' => 'choice',
            'choices' => [],
        ],
        'nature_of_the_costing' => [
            'type' => 'choice',
            'choices' => [],
        ],
        'estimated_study_time' => [
            'type' => 'choice',
            'choices' => [],
        ],
        'effective_study_time' => [
            'type' => 'choice',
            'choices' => [],
        ],
        'load_plan_start' => [
            'type' => 'choice',
            'choices' => [],
            'width' => '100px',
            'attr' => [
                'class' => 'form-control',
            ]
        ]
    ];

    public function getOptions($name)
    {
        if (array_key_exists($name, self::OPTIONS)) {
            return self::OPTIONS[$name];
        }

        return [];
    }

    public function getClientCountries()
    {
        $countryNames = [];
        $countries = $this->em->getRepository(Address::class)
            ->createQueryBuilder('address')
            ->select('DISTINCT(address.country) country')
            ->where('address.country IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;

        foreach ($countries as $countryCode) {
            $countryNames[$countryCode['country']] = Countries::getName($countryCode['country']);
        }

        return $countryNames;
    }

    public function getActivities()
    {
        $filterActivities = [];
        $activities = $this->em->getRepository(Client::class)
            ->createQueryBuilder('client')
            ->select('DISTINCT(client.activity) activity')
            ->where('client.activity IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;

        foreach ($activities as $activity) {
            $filterActivities[$activity['activity']] = $this->translator->trans($activity['activity'], [], 'client');
        }

        return $filterActivities;
    }

    public function getProjectBusinnessCharge()
    {
        $businessChargeProject = $this->em->getRepository(Project::class)
            ->createQueryBuilder('project')
            ->select('DISTINCT(businessCharge.lastName) businessChargeName')
            ->leftJoin('project.businessCharge', 'businessCharge')
            ->where('businessCharge.lastName IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;
        $businessChargeProject = array_column($businessChargeProject, 'businessChargeName');

        return array_combine($businessChargeProject, $businessChargeProject);
    }

    public function getProjectMarketType()
    {
        $marketTypeChoices = [];
        $marketTypes = ProjectConstants::getTypeValues(ProjectConstants::TYPE_DE_MARCHE, true);

        foreach ($marketTypes as $key => $marketType) {
            $marketTypeChoices[$key] = $this->translator->trans($marketType, [], 'project');
        }

        return $marketTypeChoices;
    }

    public function getProjectSiteCode()
    {
        $siteCodes = $this->em->getRepository(Project::class)
            ->createQueryBuilder('project')
            ->select('DISTINCT(project.siteCode) siteCode')
            ->getQuery()
            ->getResult()
        ;
        $siteCodes = array_column($siteCodes, 'siteCode');

        return array_combine($siteCodes, $siteCodes);
    }

    public function getProjectProspect()
    {
        $projectProspect = $this->em->getRepository(Project::class)
            ->createQueryBuilder('project')
            ->select('DISTINCT(prospect.name) prospectName')
            ->leftJoin('project.prospect', 'prospect')
            ->getQuery()
            ->getResult()
        ;
        $projectProspect = array_column($projectProspect, 'prospectName');

        return array_combine($projectProspect, $projectProspect);
    }

    public function getProjectInterlocutor()
    {
        $projectInterlocutors = $this->em->getRepository(Project::class)
            ->createQueryBuilder('project')
            ->select('DISTINCT(contact.email) contactEmail')
            ->leftJoin('project.contact', 'contact')
            ->getQuery()
            ->getResult()
        ;
        $projectInterlocutors = array_column($projectInterlocutors, 'contactEmail');

        return array_combine($projectInterlocutors, $projectInterlocutors);
    }

    public function getPlanActivities()
    {
        $loadPlanActivities = [];
        foreach (ProjectConstants::FIRST_MARKET_TYPES as $activity) {
            $loadPlanActivities[$activity] = $this->translator->trans($activity, [], 'project');
        }

        return $loadPlanActivities;
    }

    public function getProjectFolderNameOnTheServer()
    {
        $folders = array_column($this->em->getRepository(LoadPlan::class)
            ->createQueryBuilder('l')
            ->select('DISTINCT(project.folderNameOnTheServer) folderNameOnTheServer')
            ->leftJoin('l.project', 'project')
            ->getQuery()
            ->getResult()
        , 'folderNameOnTheServer');

        return array_combine($folders, $folders);
    }

    // COMMERCIAL
    public function getLoadPlanBusinessCharge()
    {
        $res = [];
        $businessCharges = $this->em->getRepository(LoadPlan::class)
            ->createQueryBuilder('l')
            ->select('DISTINCT(businessCharge.firstName) firstName, businessCharge.lastName lastName')
            ->leftJoin('l.project', 'project')
            ->leftJoin('project.businessCharge', 'businessCharge')
            ->where('businessCharge.firstName IS NOT NULL OR businessCharge.lastName IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;

        $firstNames = array_column($businessCharges, 'firstName');
        $lastNames = array_column($businessCharges, 'lastName');
        $businessChargesList = array_combine($firstNames, $lastNames);
        foreach ($businessChargesList as $key => $businessCharge) {
            $res[$key] = $businessCharge . ' ' .$key;
        }

        return $res;
    }

    public function getNatureOfTheCosting()
    {
        $res = [];
        foreach (LoadPlan::TASK_TYPES as $studyTime) {
            $res[$studyTime] = $this->translator->trans('load_plan.task_type.' .$studyTime, [], 'projects');
        }

        return $res;
    }

    public function getEstimatedStudyTime()
    {
        $res = [];
        foreach (LoadPlan::STUDY_TIME as $studyTime) {
            $res[$studyTime] = $this->translator->trans('load_plan.study_time.' .$studyTime, [], 'projects');
        }

        return $res;
    }

    public function getEconomist()
    {
        $economists = $this->em->getRepository(LoadPlan::class)
            ->createQueryBuilder('l')
            ->select('economist.firstName firstName, economist.lastName lastName')
            ->leftJoin('l.project', 'project')
            ->leftJoin('project.economist', 'economist')
            ->getQuery()
            ->getScalarResult()
        ;
        
        $res1 = array_column($economists, 'firstName');
        $res2 = array_column($economists, 'lastName');
        $res = [];

        foreach ($res1 as $key => $economist) {
            $res[$res1[$key]] = $res1[$key] . ' ' .$res2[$key];
        }

        return $res;
    }

    public function getLoadPlanStart()
    {
        $currentYear = (new \DateTime())->format('Y');
        $startOfYear = \DateTime::createFromFormat('Y-m-d', $currentYear . '-01-01');
        $endOfYear = \DateTime::createFromFormat('Y-m-d', $currentYear . '-12-31');
        $weeksInYearCount = floor($startOfYear->diff($endOfYear)->days / 7);
        $res = [];
        $weekStart = (clone $startOfYear)->modify('Next monday');
        $weekEnd = (clone $weekStart)->modify('+6 day');
        $res[implode(';', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])] = 1;
        
        for ($i=2; $i < $weeksInYearCount; $i++) { 
            $res[implode(';', [$weekEnd->modify('+1 day')->format('Y-m-d'), $weekEnd->modify('+6 day')->format('Y-m-d')])] = $i;
        }

        return $res;
    }
}