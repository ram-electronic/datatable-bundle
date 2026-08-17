<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Application\ReadModel;

/**
 * Value object representing a single filter condition.
 * Immutable and validates its own state.
 */
final readonly class FilterCondition
{
    /**
     * @param string         $field    The field to filter on (must be whitelisted by repository)
     * @param FilterOperator $operator The comparison operator
     * @param mixed          $value    The value to compare against (can be null for unary operators)
     */
    public function __construct(
        public string $field,
        public FilterOperator $operator,
        public mixed $value,
    ) {
        if ('' === trim($this->field)) {
            throw new \InvalidArgumentException('Filter field cannot be empty');
        }

        // Unary operators don't require a value
        if ($this->operator->isUnaryOperator()) {
            return;
        }

        if (null === $this->value || '' === $this->value) {
            throw new \InvalidArgumentException('Filter value cannot be empty');
        }

        if (! \is_scalar($this->value) && ! $this->value instanceof \DateTimeInterface) {
            throw new \InvalidArgumentException('Filter value must be scalar or a DateTimeInterface instance');
        }
    }
}
