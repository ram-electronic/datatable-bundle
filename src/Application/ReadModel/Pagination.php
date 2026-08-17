<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Application\ReadModel;

/**
 * Value object representing pagination settings.
 * Encapsulates all pagination-related logic.
 */
readonly class Pagination
{
    public const int DEFAULT_PAGE = 1;
    public const int DEFAULT_PAGE_SIZE = 30;
    public const int MAX_PAGE_SIZE = 100;

    public function __construct(
        public int $page = self::DEFAULT_PAGE,
        public int $pageSize = self::DEFAULT_PAGE_SIZE,
    ) {
        if ($this->page < 1) {
            throw new \InvalidArgumentException('Page must be at least 1.');
        }

        if ($this->pageSize < 1 || $this->pageSize > self::MAX_PAGE_SIZE) {
            throw new \InvalidArgumentException(\sprintf('Page size must be between 1 and %d.', self::MAX_PAGE_SIZE));
        }
    }

    /**
     * Calculate the offset for database queries.
     */
    public function getOffset(): int
    {
        return ($this->page - 1) * $this->pageSize;
    }

    /**
     * Get the limit for database queries.
     */
    public function getLimit(): int
    {
        return $this->pageSize;
    }
}
