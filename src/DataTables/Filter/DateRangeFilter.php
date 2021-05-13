<?php

namespace App\DataTables\Filter;

use Doctrine\ORM\Query\Expr\Comparison;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateRangeFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'type' => 'daterange',
            'delimiter' => ' - ',
            'startFormat' => 'd/m/Y',
            'endFormat' => 'd/m/Y',
            'operator' => 'BETWEEN',
            'operator2' => 'AND',
            'width' => '160px',
            'start' => null,
            'end' => null
        ]);
        $resolver->setDefined('start');
        $resolver->setDefined('end');
        
        return $this;
    }

    public function getStart()
    {
        return $this->options['start'];
    }
    
    public function getEnd()
    {
        return $this->options['end'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildSearchQuery($column, $search)
    {
        $values = explode($this->getOption('delimiter'), $search);

        if (count($values) !== 2) {
            return null;
        }

        $startKey = $column->getField() . '_start_' . microtime();
        $endKey = $column->getField() . '_end_' . microtime();
        $startKey = str_replace('.', '', str_replace(' ', '', $startKey));
        $endKey = str_replace('.', '', str_replace(' ', '', $endKey));
        $startDate = \DateTime::createFromFormat($this->getOption('startFormat'), $values[0]);
        $endDate = \DateTime::createFromFormat($this->getOption('endFormat'), $values[1]);    
        $exp = sprintf('%s %s :%s %s :%s', $column->getField(), $this->operator, $startKey, $this->operator2, $endKey);
    
        return [
            'expr' => $exp,
            'parameters' => [
                $startKey => $startDate->format('Y-m-d'),
                $endKey => $endDate->format('Y-m-d'),
            ],
        ];
    }
}