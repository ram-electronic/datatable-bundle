<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Application\ReadModel;

/**
 * Generic paginated result wrapper.
 * Framework-agnostic - no Doctrine/Knp types.
 *
 * @template T
 */
final readonly class PaginatedResult
{
    /**
     * @param array<T> $items       The items on the current page
     * @param int      $totalCount  Total number of items across all pages
     * @param int      $currentPage Current page number (1-indexed)
     * @param int      $pageSize    Number of items per page
     */
    public function __construct(
        public array $items,
        public int $totalCount,
        public int $currentPage,
        public int $pageSize,
    ) {
        if ($this->currentPage < 1) {
            throw new \InvalidArgumentException('Current page must be at least 1.');
        }

        if ($this->pageSize < 1) {
            throw new \InvalidArgumentException('Page size must be at least 1.');
        }

        if ($this->totalCount < 0) {
            throw new \InvalidArgumentException('Total count cannot be negative.');
        }
    }

    /**
     * Get the total number of pages.
     */
    public function getTotalPages(): int
    {
        if (0 === $this->totalCount) {
            return 1;
        }

        return (int) ceil($this->totalCount / $this->pageSize);
    }

    /**
     * Check if there is a previous page.
     */
    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * Check if there is a next page.
     */
    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getTotalPages();
    }

    /**
     * Get the number of items on the current page.
     */
    public function getItemCount(): int
    {
        return \count($this->items);
    }

    /**
     * Check if the result is empty.
     */
    public function isEmpty(): bool
    {
        return 0 === $this->totalCount;
    }
}
