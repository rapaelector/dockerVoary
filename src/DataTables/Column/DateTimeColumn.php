<?php

namespace App\DataTables\Column;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Omines\DataTablesBundle\Filter\AbstractFilter;

class DateTimeColumn extends \Omines\DataTablesBundle\Column\DateTimeColumn
{
    use ColumnTrait;

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'meta' => [],
            'filterOptions' => null,
        ]);

        $resolver
            ->setAllowedTypes('meta', ['null', 'array'])
            ->setAllowedTypes('filterOptions', ['null', 'array'])
            ->setAllowedTypes('filter', ['null', AbstractFilter::class])
        ;

        return $this;
    }
}
