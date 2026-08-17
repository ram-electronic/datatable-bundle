<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Service;

use RamElectronic\DataTableBundle\Application\ReadModel\Action;
use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\Sort;
use RamElectronic\DataTableBundle\Application\ReadModel\SortDirection;
use RamElectronic\DataTableBundle\Application\ReadModel\TableCriteria;
use RamElectronic\DataTableBundle\Presentation\Dto\FilterFormData;

readonly class TableCriteriaBuilder
{
    public function __construct(
        private FilterCollectionBuilder $filterCollectionBuilder,
    ) {
    }

    /**
     * Build TableCriteria from request data.
     *
     * @param array<Column> $columns       All available columns with their metadata
     * @param array<string> $hiddenColumns List of column keys that should be hidden
     * @param array<Action> $actions       Available actions for each row
     */
    public function build(
        FilterFormData $data,
        ?Sort $defaultSort = null,
        array $columns = [],
        array $hiddenColumns = [],
        array $actions = [],
    ): TableCriteria {
        if (null !== $data->sort && $data->direction instanceof SortDirection) {
            $sortKey = $data->sort;
            $direction = $data->direction;
        } elseif ($defaultSort instanceof Sort) {
            $sortKey = $defaultSort->key;
            $direction = $defaultSort->direction;
        } else {
            $sortKey = 'id';
            $direction = SortDirection::ASC;
        }

        $page = $data->page ?? 1;

        // Build filter collection from form data using shared service
        $filters = $this->filterCollectionBuilder->build($data);

        return new TableCriteria(
            sort: new Sort($sortKey, $direction),
            page: max(1, $page),
            filters: $filters,
            search: $data->search,
            columns: $columns,
            hiddenColumns: $hiddenColumns,
            actions: $actions,
        );
    }
}
