<?php

namespace App\DataTables\Controller;

use App\DataTables\DataTableFactory;
use App\DataTables\DataTable;
trait DataTablesTrait 
{
    /**
     * Creates and returns a basic DataTable instance.
     *
     * @param array $options Options to be passed
     * @return DataTable
     */
    protected function createDataTable(array $options = [])
    {
        return $this->container->get(DataTableFactory::class)->create($options);
    }
}
