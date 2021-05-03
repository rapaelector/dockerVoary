<?php

namespace App\DataTables\Column;

use App\Utils\Resolver;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PenaliteColumn extends \Omines\DataTablesBundle\Column\AbstractColumn
{
    use ColumnTrait;

    /**
     * {@inheritdoc}
     */
    public function normalize($value): string
    {
        if (!$value instanceof \Doctrine\ORM\PersistentCollection) {
            return $this->options['defaultValue'];
        }
        $realValue = $value->get($this->options['index']);

        return Resolver::resolve(array_merge([$realValue],$this->options['resolverPath']), $this->options['defaultValue']);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(['resolverPath']);

        $resolver->setDefaults([
            'meta' => [],
            'index' => 0,
            'defaultValue' => '',
            'filterOptions' => null,
        ]);

        $resolver
            ->setAllowedTypes('meta', ['null', 'array'])
            ->setAllowedTypes('filterOptions', ['null', 'array'])
        ;

        return $this;
    }
}
