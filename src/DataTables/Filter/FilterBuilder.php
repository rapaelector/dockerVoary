<?php

namespace App\DataTables\Filter;

use App\Utils\Resolver;

use Omines\DataTablesBundle\DataTable;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\Query\Expr\Comparison;

class FilterBuilder implements FilterBuilderInterface
{
    private $filters = [];

    public function buildFilter($type, array $options = []): \Omines\DataTablesBundle\Filter\AbstractFilter
    {
        $filter = new $type();
        $filter->setOptions($options);

        return $filter;
    }

    public function configureFilterOPtions(array $options)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'operator' => Comparison::EQ
        ]);

        return $resolver->resolve($options);
    }

    private function addFilter($column, array $options)
    {
        $type = Resolver::resolve([$options, 'type']);
        $this->filters[] = new TextFilter($column, $options);
    }
}
