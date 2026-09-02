<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('DataTable:Card', template: '@DataTable/components/Card.html.twig')]
final class Card
{
}
