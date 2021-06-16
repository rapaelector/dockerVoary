<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Symfony\Component\Intl\Countries;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('country_name', [$this, 'countryCodeToName'])
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('resolve', [$this, 'resolve']),
        ];
    }

    public function resolve(array $paths = [], $default = "")
    {
        return \App\Utils\Resolver::resolve($paths, $default);
    }

    /**
     * Get country code and return country name
     * 
     * @var string $countryCode
     */
    public function countryCodeToName(?string $countryCode): string
    {
        return $countryCode ? (Countries::getName($countryCode) ? Countries::getName($countryCode) : $countryCode) : '';
    }
}
