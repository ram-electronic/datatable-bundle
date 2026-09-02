<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components\Pagination;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('DataTable:Pagination:Next', template: '@DataTable/components/Pagination/Next.html.twig')]
final class Next
{
}
