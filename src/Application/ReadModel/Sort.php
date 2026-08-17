<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Application\ReadModel;

/**
 * Value object representing a sort instruction for table queries.
 * Immutable and validated.
 */
final readonly class Sort
{
    /**
     * @param string        $key       The field to sort by (must be validated against whitelist)
     * @param SortDirection $direction Sort direction (ASC or DESC)
     */
    public function __construct(
        public string $key,
        public SortDirection $direction = SortDirection::ASC,
    ) {
    }

    /**
     * Create a Sort instance with ASC direction.
     */
    public static function asc(string $key): self
    {
        return new self($key, SortDirection::ASC);
    }

    /**
     * Create a Sort instance with DESC direction.
     */
    public static function desc(string $key): self
    {
        return new self($key, SortDirection::DESC);
    }

    /**
     * Check if the sort direction is ascending.
     */
    public function isAscending(): bool
    {
        return SortDirection::ASC === $this->direction;
    }

    /**
     * Check if the sort direction is descending.
     */
    public function isDescending(): bool
    {
        return SortDirection::DESC === $this->direction;
    }

    /**
     * Get the opposite direction.
     */
    public function toggleDirection(): self
    {
        return new self(
            $this->key,
            $this->direction->toggle()
        );
    }
}
