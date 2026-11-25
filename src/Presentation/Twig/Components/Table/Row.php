<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components\Table;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('DataTable:Table:Row', template: '@DataTable/components/Table/Row.html.twig')]
final class Row
{
    public bool $hover = true;
}
