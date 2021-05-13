<?php

namespace App\DataTables\Filter;

use Doctrine\ORM\Query\Expr\Comparison;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'type' => 'text',
            'operator' => 'LIKE',
            'width' => '100px',
            'searchFormatter' => function ($search) {
                return '%'.$search.'%';
            }
        ]);

        return $this;
    }
}
