<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components\Table;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Table:Cell')]
final class Cell
{
    public string $align = 'left';
    public bool $bold = false;
}
