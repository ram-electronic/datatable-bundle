<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Application\ReadModel;

/**
 * Base value object representing table query criteria.
 * Immutable contract between UI and Application layer.
 * Framework-agnostic - no Doctrine types.
 */
readonly class TableCriteria
{
    public const int DEFAULT_PAGE = 1;
    public const int DEFAULT_PAGE_SIZE = 30;
    public const int MAX_PAGE_SIZE = 100;

    private readonly Pagination $pagination;

    /**
     * @param Sort             $sort          Sort criteria (field + direction)
     * @param int              $page          Current page number (1-indexed)
     * @param int              $pageSize      Number of items per page
     * @param FilterCollection $filters       Operator-based filter conditions
     * @param string|null      $search        Quick search query across all filterable fields
     * @param array<Column>    $columns       All available columns with their metadata
     * @param array<string>    $hiddenColumns List of column keys that should be hidden
     * @param array<Action>    $actions       Available actions for each row
     */
    public function __construct(
        public Sort $sort,
        int $page = self::DEFAULT_PAGE,
        int $pageSize = self::DEFAULT_PAGE_SIZE,
        public FilterCollection $filters = new FilterCollection([]),
        public ?string $search = null,
        public array $columns = [],
        public array $hiddenColumns = [],
        public array $actions = [],
    ) {
        $this->pagination = new Pagination($page, $pageSize);
    }

    /**
     * Calculate the offset for database queries.
     * Delegates to the Pagination object.
     */
    public function getOffset(): int
    {
        return $this->pagination->getOffset();
    }

    /**
     * Get the limit for database queries.
     * Delegates to the Pagination object.
     */
    public function getLimit(): int
    {
        return $this->pagination->getLimit();
    }

    /**
     * Get current page number.
     */
    public function getPage(): int
    {
        return $this->pagination->page;
    }

    /**
     * Get page size.
     */
    public function getPageSize(): int
    {
        return $this->pagination->pageSize;
    }

    /**
     * Check if any filters are active.
     */
    public function hasFilters(): bool
    {
        return ! $this->filters->isEmpty();
    }

    /**
     * Check if a specific column should be visible.
     * A column is visible if it's not in the hiddenColumns list.
     */
    public function isColumnVisible(string $columnName): bool
    {
        return ! \in_array($columnName, $this->hiddenColumns, true);
    }

    /**
     * Get visible columns with their metadata.
     * Returns all columns except those in the hiddenColumns list.
     *
     * @return array<Column>
     */
    public function getVisibleColumns(): array
    {
        if ([] === $this->columns) {
            return [];
        }

        return array_filter(
            $this->columns,
            fn (Column $column): bool => $this->isColumnVisible($column->key)
        );
    }

    /**
     * Check if actions should be displayed.
     */
    public function hasActions(): bool
    {
        return [] !== $this->actions;
    }
}
