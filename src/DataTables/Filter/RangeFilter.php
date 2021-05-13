<?php

namespace App\DataTables\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

class RangeFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'type' => 'text',
            'delimiter' => ';',
            'operator' => 'BETWEEN',
            'operator2' => 'AND',
            'min' => 0,
            'max' => 999999,
            'width' => '110px',
            'decimal' => 0,
            'step' => 1,
            'prefix' => '',
            'suffix' => '',
        ]);
        
        $resolver->setRequired('min');
        $resolver->setRequired('max');

        return $this;
    }

    public function parseSearch($search)
    {
        $values = explode($this->getOption('delimiter'), $search);

        return array_map(function ($value) {
            return floatval(trim($value));
        }, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function buildSearchQuery($column, $search)
    {
        $values = $this->parseSearch($search);
        
        if (empty($values)) {
            return null;
        }

        $minKey = $column->getField() . '_min_' . microtime();
        $maxKey = $column->getField() . '_max_' . microtime();
        $minKey = str_replace('.', '', str_replace(' ', '', $minKey));
        $maxKey = str_replace('.', '', str_replace(' ', '', $maxKey));
        $min = $values[0];
        $max = $values[1];
        $parameters = [
            $minKey => $min,
            $maxKey => $max,
        ];


        if ($min && !$max) {
            $exp = sprintf('%s > :%s', $column->getField(), $minKey);
            unset($parameters[$maxKey]);
        } else if (!$min && $max) {
            $exp = sprintf('%s < :%s', $column->getField(), $maxKey);
            unset($parameters[$minKey]);
        } else {
            $exp = sprintf('%s %s :%s %s :%s', $column->getField(), $this->operator, $minKey, $this->operator2, $maxKey);
        }
        
        return [
            'expr' => $exp,
            'parameters' => $parameters,
        ];
    }
}
