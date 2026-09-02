<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components\Button;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('DataTable:Button:Link', template: '@DataTable/components/Button/Link.html.twig')]
final class Link
{
    public string $variant = 'default';
    public string $size = 'default';
}
