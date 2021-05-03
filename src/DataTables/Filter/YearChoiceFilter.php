<?php 

namespace App\DataTables\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

class YearChoiceFilter extends ChoiceFilter
{
    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'operator' => 'BETWEEN',
            'operator2' => 'AND',
        ]);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function buildSearchQuery($column, $year)
    {
        $startKey = $column->getField() . '_start_' . microtime();
        $endKey = $column->getField() . '_end_' . microtime();
        $startKey = str_replace('.', '', str_replace(' ', '', $startKey));
        $endKey = str_replace('.', '', str_replace(' ', '', $endKey));

        $startDate = \DateTime::createFromFormat('d-m-Y', '01-01-'. $year);
        $endDate = \DateTime::createFromFormat('d-m-Y', '31-12-'. $year);

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
