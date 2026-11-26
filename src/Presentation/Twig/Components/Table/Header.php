<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components\Table;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('DataTable:Table:Header', template: '@DataTable/components/Table/Header.html.twig')]
final class Header
{
    public string $align = 'left';
}
