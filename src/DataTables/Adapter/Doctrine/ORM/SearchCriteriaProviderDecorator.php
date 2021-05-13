<?php

namespace App\DataTables\Adapter\Doctrine\ORM;

use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Column\AbstractColumn;
use Omines\DataTablesBundle\DataTableState;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\QueryBuilderProcessorInterface;

class SearchCriteriaProviderDecorator implements QueryBuilderProcessorInterface
{
    public function __construct(SearchCriteriaProvider $searchCriteriaProvider)
    {
        $this->searchCriteriaProvider = $searchCriteriaProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function process(QueryBuilder $queryBuilder, DataTableState $state)
    {
        $this->processSearchColumns($queryBuilder, $state);
        $this->processGlobalSearch($queryBuilder, $state);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param DataTableState $state
     */
    private function processSearchColumns(QueryBuilder $queryBuilder, DataTableState $state)
    {
        foreach ($state->getSearchColumns() as $searchInfo) {
            /** @var AbstractColumn $column */
            $column = $searchInfo['column'];
            $search = $searchInfo['search']; 
            
            if (!empty($search) && null !== ($filter = $column->getFilter())) {
                if ($res = $filter->buildSearchQuery($column, $search)) {
                    $queryBuilder->andWhere($res['expr']);
                    foreach ($res['parameters'] as $key => $parameter) {
                        $queryBuilder->setParameter($key, $parameter);
                    }
                }
                // $comparison = new Comparison($column->getField(), $column->getOperator(), $search);
                // dump($column->getOperator(), get_class($column));
                // $searchKey = $column->getField() . microtime();
                // $searchKey = str_replace('.', '', str_replace(' ', '', $searchKey));
                // $exp = sprintf('%s %s :%s', $column->getField(), $column->getOperator(), $searchKey);
                // $queryBuilder->andWhere($exp);
                // $queryBuilder->setParameter($searchKey, $search);
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param DataTableState $state
     */
    private function processGlobalSearch(QueryBuilder $queryBuilder, DataTableState $state)
    {
        if (!empty($globalSearch = $state->getGlobalSearch())) {
            $expr = $queryBuilder->expr();
            $comparisons = $expr->orX();
            foreach ($state->getDataTable()->getColumns() as $column) {
                if ($column->isGlobalSearchable() && !empty($column->getField()) && $column->isValidForSearch($globalSearch)) {
                    $comparisons->add(new Comparison($column->getLeftExpr(), $column->getOperator(),
                    $expr->literal($column->getRightExpr($globalSearch))));
                }
            }
            $queryBuilder->andWhere($comparisons);
        }
    }
}
