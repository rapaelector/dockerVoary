<?php

namespace App\DataTables\Adapter;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\DataTableState;
use Omines\DataTablesBundle\Adapter\AdapterQuery;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\QueryBuilderProcessorInterface;
// use App\DataTables\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\AutomaticQueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use App\DataTables\Adapter\Doctrine\ORM\SearchCriteriaProviderDecorator;
use Omines\DataTablesBundle\Exception\InvalidConfigurationException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ORMAdapter extends \Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter
{
    /** @var callable */
    protected $postPrepareQuery;

    /** @var RegistryInterface */
    private $registry;

    /** @var EntityManager */
    protected $manager;

    /** @var \Doctrine\ORM\Mapping\ClassMetadata */
    protected $metadata;

    /** @var int */
    private $hydrationMode;

    private $postResult;

    public function  __construct(ManagerRegistry $registry = null)
    {
        parent::__construct($registry);
        $this->registry = $registry;
    }

    /**
     * @param mixed $processor
     */
    public function addCriteriaProcessor($processor)
    {
        $this->criteriaProcessors[] = $this->normalizeProcessor($processor);
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $providerNormalizer = function (Options $options, $value) {
            return array_map([$this, 'normalizeProcessor'], (array) $value);
        };

        $resolver
            ->setDefaults([
                'hydrate' => Query::HYDRATE_OBJECT,
                'query' => [],
                'criteria' => function (Options $options) {
                    return [new SearchCriteriaProvider()];
                },
            ])
            ->setRequired('entity')
            ->setAllowedTypes('entity', ['string'])
            ->setAllowedTypes('hydrate', 'int')
            ->setAllowedTypes('query', [QueryBuilderProcessorInterface::class, 'array', 'callable'])
            ->setAllowedTypes('criteria', [QueryBuilderProcessorInterface::class, 'array', 'callable', 'null'])
            ->setNormalizer('query', $providerNormalizer)
            ->setNormalizer('criteria', $providerNormalizer)
        ;
    }

    /**
     * @param callable|QueryBuilderProcessorInterface $provider
     * @return QueryBuilderProcessorInterface
     */
    private function normalizeProcessor($provider)
    {
        if ($provider instanceof QueryBuilderProcessorInterface) {
            return new SearchCriteriaProviderDecorator($provider);
        } elseif (is_callable($provider)) {
            return new class($provider) implements QueryBuilderProcessorInterface {
                private $callable;

                public function __construct(callable $value)
                {
                    $this->callable = $value;
                }

                public function process(QueryBuilder $queryBuilder, DataTableState $state)
                {
                    return call_user_func($this->callable, $queryBuilder, $state);
                }
            };
        }

        throw new InvalidConfigurationException('Provider must be a callable or implement QueryBuilderProcessorInterface');
    }

    public function setPostPrepareQuery(callable $callback)
    {
        $this->postPrepareQuery = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $options)
    {
        parent::configure($options);
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $options = $resolver->resolve($options);

        // Enable automated mode or just get the general default entity manager
        if (null === ($this->manager = $this->registry->getManagerForClass($options['entity']))) {
            throw new InvalidConfigurationException(sprintf('Doctrine has no manager for entity "%s", is it correctly imported and referenced?', $options['entity']));
        }
        $this->metadata = $this->manager->getClassMetadata($options['entity']);
        if (empty($options['query'])) {
            $options['query'] = [new AutomaticQueryBuilder($this->manager, $this->metadata)];
        }

        // Set options
        $this->hydrationMode = $options['hydrate'];
        $this->queryBuilderProcessors = $options['query'];
        $this->criteriaProcessors = $options['criteria'];
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareQuery(AdapterQuery $query)
    {
        $state = $query->getState();
        $query->set('qb', $builder = $this->createQueryBuilder($state));
        $query->set('rootAlias', $rootAlias = $builder->getDQLPart('from')[0]->getAlias());

        // Provide default field mappings if needed
        foreach ($state->getDataTable()->getColumns() as $column) {
            if (null === $column->getField() && isset($this->metadata->fieldMappings[$name = $column->getName()])) {
                $column->setOption('field', "{$rootAlias}.{$name}");
            }
        }

        /** @var Query\Expr\From $fromClause */
        $fromClause = $builder->getDQLPart('from')[0];
        $identifier = "{$fromClause->getAlias()}.{$this->metadata->getSingleIdentifierFieldName()}";
        $query->setTotalRows($this->getCount($builder, $identifier));

        // Get record count after filtering
        $this->buildCriteria($builder, $state);
        $query->setFilteredRows($this->getCount($builder, $identifier));

        // Perform mapping of all referred fields and implied fields
        $aliases = $this->getAliases($query);
        $query->set('aliases', $aliases);
        $query->setIdentifierPropertyPath($this->mapFieldToPropertyPath($identifier, $aliases));

        if ($this->postPrepareQuery) {
            $this->postResult = call_user_func($this->postPrepareQuery, $builder, $state);
        }
    }

    public function getPostResult()
    {
        return $this->postResult;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapFieldToPropertyPath($field, array $aliases = [])
    {
        $parts = explode('.', $field);
        if (count($parts) < 2) {
            throw new InvalidConfigurationException(sprintf("Field name '%s' must consist at least of an alias and a field separated with a period", $field));
        }
        list($origin, $target) = $parts;

        $path = [$target];
        $current = $aliases[$origin][0];

        while (null !== $current) {
            list($origin, $target) = explode('.', $current);
            $path[] = $target;
            $current = $aliases[$origin][0];
        }

        if (Query::HYDRATE_ARRAY === $this->hydrationMode) {
            return '[' . implode('][', array_reverse($path)) . ']';
        } else {
            return implode('.', array_reverse($path));
        }
    }
}
