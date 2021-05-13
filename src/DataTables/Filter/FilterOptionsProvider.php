<?php

namespace App\DataTables\Filter;

use App\DataTables\Filter\FilterConstants;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class FilterOptionsProvider
{

    public function __construct(
        TranslatorInterface $translator,
        EntityManagerInterface $em
    )
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
    ];

    public function getOptions($name)
    {
        if (array_key_exists($name, self::OPTIONS)) {
            return self::OPTIONS[$name];
        }

        return [];
    }

}