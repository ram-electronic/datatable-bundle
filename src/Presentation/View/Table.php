<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\View;

use RamElectronic\DataTableBundle\Application\ReadModel\FilterFieldRegistry;
use RamElectronic\DataTableBundle\Application\ReadModel\PaginatedResult;
use RamElectronic\DataTableBundle\Application\ReadModel\Sort;
use RamElectronic\DataTableBundle\Application\ReadModel\TableCriteria;
use RamElectronic\DataTableBundle\Presentation\Dto\FilterFormData;
use Symfony\Component\Form\FormView;

/**
 * Everything a page needs to render one filterable, sortable, paginated
 * table: pass it as `table` to FilterForm, DataTable:Table:SortableHeader
 * and PaginationControl. Built by TableHandler::handle().
 *
 * @template T
 */
final readonly class Table
{
    /**
     * @param PaginatedResult<T>   $result
     * @param array<string, mixed> $routeParams
     */
    public function __construct(
        public PaginatedResult $result,
        public TableCriteria $criteria,
        public FormView $form,
        public FilterFormData $formData,
        public FilterFieldRegistry $fieldRegistry,
        public string $route,
        public array $routeParams = [],
    ) {
    }

    /**
     * The rows of the current page.
     *
     * @return array<T>
     */
    public function getItems(): array
    {
        return $this->result->items;
    }

    public function isEmpty(): bool
    {
        return $this->result->isEmpty();
    }

    public function getSort(): Sort
    {
        return $this->criteria->sort;
    }

    /**
     * The name of the filter form, the prefix of its query parameters.
     */
    public function getFormName(): string
    {
        $name = $this->form->vars['name'] ?? null;
        if (! \is_string($name)) {
            throw new \LogicException('The filter form has no name.');
        }

        return $name;
    }
}
