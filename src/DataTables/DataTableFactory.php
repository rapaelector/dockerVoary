<?php

namespace App\DataTables;

use Symfony\Component\HttpFoundation\Request;
use Omines\DataTablesBundle\DependencyInjection\Instantiator;

class DataTableFactory extends \Omines\DataTablesBundle\DataTableFactory
{
    /** @var Instantiator */
    protected $instantiator;

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $config = $this->config;

        return (new DataTable($this->eventDispatcher, array_merge($config['options'] ?? [], $options), $this->instantiator))
            ->setRenderer($this->renderer)
            ->setMethod($config['method'] ?? Request::METHOD_POST)
            ->setPersistState($config['persist_state'] ?? 'fragment')
            ->setTranslationDomain($config['translation_domain'] ?? 'messages')
            ->setLanguageFromCDN($config['language_from_cdn'] ?? true)
            ->setTemplate($config['template'] ?? DataTable::DEFAULT_TEMPLATE, $config['template_parameters'] ?? [])
        ;
    }
}
