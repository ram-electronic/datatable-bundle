<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components;

use RamElectronic\DataTableBundle\Application\ReadModel\PaginatedResult;
use RamElectronic\DataTableBundle\Presentation\View\Table;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('PaginationControl', template: '@DataTable/components/PaginationControl.html.twig')]
final class PaginationControl
{
    /** @var PaginatedResult<object> */
    public PaginatedResult $pagination;
    public string $route;
    /** @var array<string, mixed> */
    public array $routeParams = [];
    public ?string $filterFormName = null;

    /**
     * Fills pagination, route, routeParams and filterFormName from a Table.
     *
     * @param Table<mixed>|null $table
     */
    public function mount(?Table $table = null): void
    {
        if ($table instanceof Table) {
            $this->pagination = $table->result;
            $this->route = $table->route;
            $this->routeParams = $table->routeParams;
            $this->filterFormName = $table->getFormName();
        }
    }
}
