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

}