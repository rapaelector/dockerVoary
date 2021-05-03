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

    protected $flotteHelper;
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

    protected function moneyFormatFactory($decimals = 0, $decimalpoint = ',', $separator = '.', $currency = '€')
    {
        return function ($value, $context) use($decimals, $decimalpoint, $separator, $currency) {
            return is_numeric($value) ? number_format($value, $decimals, $decimalpoint, $separator) . ' ' . $currency : '';
        };
    }

    protected function numberFormatFactory($decimals = 0, $decimalpoint = ',', $separator = ' ', $currency = '')
    {
        return function ($value, $context) use($decimals, $decimalpoint, $separator, $currency) {
            return is_numeric($value) ? number_format($value, $decimals, $decimalpoint, $separator) . ' ' . $currency : '';
        };
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
    
    protected function dateRenderer($path, $default = '', $dateFormat = 'd/m/Y')
    {
        return function($value, $context) use($path, $default, $dateFormat) {
            $val = Resolver::resolve(array_merge([$context], $path), null);

            return !is_null($val) ? $val->format($dateFormat) : $default;
        };
    }

    protected function columnMeta(array $options = [], $uppercase = true)
    {
        $labelAttr = [];
        if ($uppercase) {
            $labelAttr['style'] = 'text-transform: uppercase;';
        }
        return array_merge([
            'label_attr' => $labelAttr,
        ], $options);
    }

    protected function hasClient(): bool
    {
        $client = $this->flotteHelper->getClient();
        
        return !is_null($client);
    }

    protected function redirectToClientSetting()
    {
        $request = $this->requestStack->getMasterRequest();
        $routeName = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params');

        return $this->redirectToRoute('flotte.index', [
            'r' => $routeName, 
            'rp' => $routeParams
        ]);
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'App\\DataTables\\DataTableFactory' => \App\DataTables\DataTableFactory::class,
        ]);
    }
}
