<?php

namespace App\DataTables\Column;

use App\Utils\Resolver;

trait ColumnTrait
{
    public function getMeta(): ?array
    {
        return Resolver::resolve([$this->options, 'meta'], []);
    }

    public function getFilterOptions(): ?array
    {
        return Resolver::resolve([$this->options, 'filterOptions'], []);
    }
}
