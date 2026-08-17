<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components;

use RamElectronic\DataTableBundle\Application\ReadModel\PaginatedResult;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class PaginationControl
{
    /** @var PaginatedResult<object> */
    public PaginatedResult $pagination;
    public string $route;
    /** @var array<string, mixed> */
    public array $routeParams = [];
    public ?string $filterFormName = null;
}
