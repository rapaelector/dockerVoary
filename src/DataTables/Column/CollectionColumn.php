<?php

namespace App\DataTables\Column;

use App\Utils\Resolver;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionColumn extends \Omines\DataTablesBundle\Column\AbstractColumn
{
    use ColumnTrait;

    /**
     * {@inheritdoc}
     */
    public function normalize($collection): string
    {
        if (!$collection instanceof \Doctrine\ORM\PersistentCollection) {
            return $this->options['defaultValue'];
        }
        $values = [];

        foreach ($collection as $item) {
            $values[] = Resolver::resolve(array_merge([$item], $this->options['resolverPath']), $this->options['itemDefaultValue']);
        }

        return implode($this->options['separator'], $values);
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
            'itemDefaultValue' => '-',
            'separator' => ',',
            'filterOptions' => null,
        ]);

        $resolver
            ->setAllowedTypes('meta', ['null', 'array'])
            ->setAllowedTypes('filterOptions', ['null', 'array'])
        ;

        return $this;
    }
}
