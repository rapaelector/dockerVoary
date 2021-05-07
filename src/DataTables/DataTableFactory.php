<?php

namespace App\DataTables;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Omines\DataTablesBundle\DependencyInjection\Instantiator;

class DataTableFactory extends \Omines\DataTablesBundle\DataTableFactory
{
    /** @var Instantiator */
    protected $instantiator;

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [], array $createOptions = [], $prefix = 'app')
    {
        $config = $this->config;

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver, $prefix, $config);

        $createOptions = $resolver->resolve($createOptions);

        return (new DataTable($this->eventDispatcher, array_merge($config['options'] ?? [], $options), $this->instantiator))
            ->setRenderer($this->renderer)
            ->setMethod($config['method'] ?? Request::METHOD_POST)
            ->setPersistState($config['persist_state'] ?? 'fragment')
            ->setTranslationDomain($createOptions['translation_domain'])
            ->setLanguageFromCDN($config['language_from_cdn'] ?? true)
            ->setTemplate($config['template'] ?? DataTable::DEFAULT_TEMPLATE, $config['template_parameters'] ?? [])
            ->setTableId($createOptions['tableId'])
            ->setTableFiltersId($createOptions['tableFiltersId'])
            ->setName($createOptions['name'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver, $prefix, array $config)
    {
        $resolver->setDefaults([
            'tableId'           => $prefix . '_table',
            'tableFiltersId'    => $prefix . '_table-filters',
            'template'          => 'shared/datatables/datatable.html.twig',
            'name'              => 'dt',
            'translation_domain' => $config['translation_domain'] ?? 'messages',
        ]);
    }
}
