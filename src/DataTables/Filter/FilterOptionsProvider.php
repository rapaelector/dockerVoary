<?php

namespace App\DataTables\Filter;

use App\Entity\Client;
use App\Entity\Address;
use App\Entity\Project;
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
}