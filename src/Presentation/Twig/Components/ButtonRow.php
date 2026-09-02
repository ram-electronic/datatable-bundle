<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('DataTable:ButtonRow', template: '@DataTable/components/ButtonRow.html.twig')]
final class ButtonRow
{
}
