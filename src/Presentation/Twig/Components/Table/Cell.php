<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components\Table;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('DataTable:Table:Cell', template: '@DataTable/components/Table/Cell.html.twig')]
final class Cell
{
    public string $align = 'left';
    public bool $bold = false;
}
