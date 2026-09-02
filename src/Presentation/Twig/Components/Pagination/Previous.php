<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components\Pagination;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('DataTable:Pagination:Previous', template: '@DataTable/components/Pagination/Previous.html.twig')]
final class Previous
{
}
