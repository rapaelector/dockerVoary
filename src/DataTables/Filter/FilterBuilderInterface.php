<?php

namespace App\DataTables\Filter;

use Omines\DataTablesBundle\DataTable;

interface FilterBuilderInterface
{
    public function buildFilter($type, array $options = []): \Omines\DataTablesBundle\Filter\AbstractFilter;
}
