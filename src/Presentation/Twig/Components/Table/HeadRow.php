<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components\Table;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('DataTable:Table:HeadRow', template: '@DataTable/components/Table/HeadRow.html.twig')]
final class HeadRow
{
}
