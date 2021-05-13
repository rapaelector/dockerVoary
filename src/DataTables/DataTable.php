<?php

namespace App\DataTables;

use Omines\DataTablesBundle\Exception\InvalidArgumentException;
use Omines\DataTablesBundle\DependencyInjection\Instantiator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\Response;
use Omines\DataTablesBundle\Exception\InvalidStateException;

class DataTable extends \Omines\DataTablesBundle\DataTable
{
    private $instantiator;

    /**
     * Table container id
     * @var string
     */
    private $tableId;

    /**
     * Table column filters container id
     * @var string
     */
    private $tableFiltersId;

    public function __construct(EventDispatcherInterface $eventDispatcher, array $options = [], Instantiator $instantiator = null)
    {
        $this->eventDispatcher = $eventDispatcher;

        $this->instantiator = $instantiator ?? new Instantiator();

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * {@inheritdoc}
     */
    public function add(string $name, string $type, array $options = [])
    {
        // Ensure name is unique
        if (isset($this->columnsByName[$name])) {
            throw new InvalidArgumentException(sprintf("There already is a column with name '%s'", $name));
        }

        $column = $this->instantiator->getColumn($type);
        $column->initialize($name, count($this->columns), $options, $this);

        $this->columns[] = $column;
        $this->columnsByName[$name] = $column;

        if ($filter = $column->getFilter()) {
            $filter->setColumn($column);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function createAdapter(string $adapter, array $options = []): \Omines\DataTablesBundle\DataTable
    {
        return $this->setAdapter($this->instantiator->getAdapter($adapter), $options);
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        $response = parent::getResponse();

        try {
            if ($postResults = $this->adapter->getPostResult()) {
                $data = json_decode($response->getContent(), true);
                $data['extras'] = $postResults;
                $response->setData($data);
            }
        } catch (\Exception $e) {}

        return $response;
    }

    public function getTemplateParams()
    {
        return $this->templateParams;
    }

    /**
     * Get table container id
     *
     * @return  string
     */ 
    public function getTableId()
    {
        return $this->tableId;
    }

    /**
     * Set table container id
     *
     * @param  string  $tableId  Table container id
     *
     * @return  self
     */ 
    public function setTableId(string $tableId)
    {
        $this->tableId = $tableId;

        return $this;
    }

    /**
     * Get table column filters container id
     *
     * @return  string
     */ 
    public function getTableFiltersId()
    {
        return $this->tableFiltersId;
    }

    /**
     * Set table column filters container id
     *
     * @param  string  $tableFiltersId  Table column filters container id
     *
     * @return  self
     */ 
    public function setTableFiltersId(string $tableFiltersId)
    {
        $this->tableFiltersId = $tableFiltersId;

        return $this;
    }
}
