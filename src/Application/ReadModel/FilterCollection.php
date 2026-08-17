<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Application\ReadModel;

/**
 * Collection of filter conditions with validation.
 * Immutable collection that ensures all conditions are valid.
 */
final readonly class FilterCollection
{
    /** @var array<FilterCondition> */
    private array $conditions;

    /**
     * @param array<FilterCondition> $conditions
     */
    public function __construct(array $conditions = [])
    {
        $this->conditions = array_values($conditions); // Re-index
    }

    /**
     * Create empty collection.
     */
    public static function empty(): self
    {
        return new self([]);
    }

    /**
     * Get all conditions.
     *
     * @return array<FilterCondition>
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * Check if collection is empty.
     */
    public function isEmpty(): bool
    {
        return [] === $this->conditions;
    }

    /**
     * Get number of conditions.
     */
    public function count(): int
    {
        return \count($this->conditions);
    }

    /**
     * Add a condition and return new collection (immutable).
     */
    public function add(FilterCondition $condition): self
    {
        return new self([...$this->conditions, $condition]);
    }

    /**
     * Get conditions for a specific field.
     *
     * @return array<FilterCondition>
     */
    public function getConditionsForField(string $field): array
    {
        return array_filter(
            $this->conditions,
            fn (FilterCondition $condition): bool => $condition->field === $field
        );
    }
}
