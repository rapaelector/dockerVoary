<?php

namespace App\DataTables\Filter;

use App\Utils\Resolver;
use Doctrine\ORM\Query\Expr\Comparison;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractFilter extends \Omines\DataTablesBundle\Filter\AbstractFilter implements FilterInterface
{
    const TYPE_TEXT = 'text';
    const TYPE_CHOICE = 'choice';
    const TYPE_DATE = 'date';
    const TYPE_DATERANGE = 'daterange';
    const TYPE_RANGE = 'range';

    protected $column;
    protected $options;

    /**
     * Build column search query
     * @param $column
     * @param $search
     * @return array    Associative array, keys are :
     * - expr : contains query expression
     * - parameters : Associative array containing key/value parameters
     */
    public function buildSearchQuery($column, $search)
    {
        $fields = [$column->getField()];
        if ($this->additionalFields && is_array($this->additionalFields)) {
            $fields = array_merge($fields, $this->additionalFields);
        }
        $searchKey = $column->getField() . microtime();
        $searchKey = str_replace('.', '', str_replace(' ', '', $searchKey));         

        $exprs = [];
        foreach ($fields as $field) {
            $exprs[] = sprintf('(%s %s :%s)', $field, $this->operator, $searchKey);
        }

        return [
            'expr' => implode(' OR ', $exprs),
            'parameters' => [$searchKey => $this->applyFormatter($search)],
        ];
    }

    private function applyFormatter($search)
    {
        try {
            return call_user_func($this->searchFormatter, $search);
        } catch (\Exception $e) {
            // TODO : log exception
        }

        return $search;
    }

    // public function __construct($column, $options)
    // {
    //     $this->column = $column;
    //     $this->options = $options;
    // }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'operator' => Comparison::EQ,
            'type' => 'text',
            'width' => '100px',
            /**
             * This option allow to add additional field to the filter search
             *      e.g:
             *          - filter for some field and add additional field to the search
             *              - field to search "prospect.name" and add "prospect.postalCode" field in the search of "prospect.name"
             *              - SQL : SELECT prospect.name FORM prospect WHERE name LIKE '%value%' OR postalCode LIKE '%value%'
             */
            'additionalFields' => [],
            'searchFormatter' => function ($search) {
                return $search;
            },
            /**
             * Attribute for the input filter
             * Any html attribute can be send here
             *      e.g: ['class' => 'some class', 'data-something' => 'data-value', etc.]
             */
            'attr' => [],
        ]);

        return $this;
    }

    public function setOptions(array $options)
    {
        parent::set($options);
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    public function setColumn($column)
    {
        $this->column = $column;
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function getName()
    {
        return $this->column->getName();
    }

    public function getLabel()
    {
        return $this->column->getLabel();
    }

    public function getType(): ?string
    {
        return Resolver::resolve([$this->options, 'type'], self::TYPE_TEXT);
    }

    public function isTypeText(): bool
    {
        return $this->getType() === self::TYPE_TEXT;
    }

    public function isTypeChoice(): bool
    {
        return $this->getType() === self::TYPE_CHOICE;
    }

    public function isTypeDate(): bool
    {
        return $this->getType() === self::TYPE_DATE;
    }

    public function isTypeDateRange(): bool
    {
        return $this->getType() == self::TYPE_DATERANGE;
    }

    public function isTypeRange(): bool
    {
        return $this->getType() == self::TYPE_RANGE;
    }

    public function getOption($name, $default = null)
    {
        return Resolver::resolve([$this->options, $name], $default);
    }

    public function isValidValue($value): bool
    {
        return true;
    }

    public function getWidth()
    {
        return $this->getOption('width');
    }
}
