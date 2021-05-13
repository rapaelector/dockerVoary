<?php

namespace App\DataTables\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoiceFilter extends AbstractFilter
{

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'type' => 'choice',
        ]);
        $resolver->setRequired('choices');

        return $this;
    }

    public function getChoices()
    {
        return $this->options['choices'];
    }
}
