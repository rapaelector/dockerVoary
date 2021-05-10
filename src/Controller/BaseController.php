<?php

namespace App\Controller;

use App\Utils\Resolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\DataTables\Controller\DataTablesTrait;
use App\DataTables\DataTableFactory;
use Symfony\Component\Routing\Annotation\Route;
use App\DataTables\Filter\FilterOptionsProvider;
use App\DataTables\Filter\FilterBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class BaseController extends AbstractController
{
    use DataTablesTrait;

    protected $filterBuilder;
    protected $filterOptionsProvider;
    protected $dataTableFactory;

    public function __construct( 
        FilterOptionsProvider $filterOptionsProvider, 
        FilterBuilderInterface $filterBuilderInterface,
        RequestStack $requestStack,
        DataTableFactory $dataTableFactory
    )
    {
        $this->filterBuilder = $filterBuilderInterface;
        $this->filterOptionsProvider = $filterOptionsProvider;
        $this->requestStack = $requestStack;
        $this->dataTableFactory = $dataTableFactory;
    }

    protected function actionsRenderer($redirectPath, $templatePath)
    {
        return function($value, $context) use($redirectPath, $templatePath) {
            return $this->renderView($templatePath, [
                'id' => $value,
                'context' => $context,
                'redirect_path' => $redirectPath,
            ]);
        };
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'App\\DataTables\\DataTableFactory' => \App\DataTables\DataTableFactory::class,
        ]);
    }
}
