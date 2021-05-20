<?php

namespace App\DataTables\Filter;

use App\Entity\Client;
use App\Entity\Address;
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
}