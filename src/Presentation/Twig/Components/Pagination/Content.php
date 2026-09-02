<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components\Pagination;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('DataTable:Pagination:Content', template: '@DataTable/components/Pagination/Content.html.twig')]
final class Content
{
}
