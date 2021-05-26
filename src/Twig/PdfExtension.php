<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PdfExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('space_value', [$this, 'spaceValue']),
        ];
    }

    /**
     * Return spacing size for pdf template
     * 
     * @var number $count
     * @var number $totalCount
     * @var number $colPxSize
     */
    public function spaceValue($count, $totalCount, $colPxSize = 1)
    {
        if ($count && $totalCount && $colPxSize && ($totalCount > 0)) {
            return (($count * 100) / $totalCount) * $colPxSize;
        }

        return 0;
    }
}
