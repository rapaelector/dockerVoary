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

    protected function numberFormatFactory($decimals = 0, $decimalpoint = ',', $separator = ' ', $currency = '')
    {
        return function ($value, $context) use($decimals, $decimalpoint, $separator, $currency) {
            return is_numeric($value) ? number_format($value, $decimals, $decimalpoint, $separator) . ' ' . $currency : '';
        };
    }

    protected function columnMeta(array $options = [], $uppercase = false)
    {
        $labelAttr = [];
        if ($uppercase) {
            $labelAttr['style'] = 'text-transform: uppercase;';
        }
        return array_merge([
            'label_attr' => $labelAttr,
        ], $options);
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'App\\DataTables\\DataTableFactory' => \App\DataTables\DataTableFactory::class,
        ]);
    }
}
