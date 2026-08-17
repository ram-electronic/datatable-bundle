<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Application\ReadModel;

use Symfony\Component\Translation\TranslatableMessage;

/**
 * Value object representing a table column definition.
 * Contains all metadata needed for field display and filtering.
 */
readonly class Column
{
    public function __construct(
        public string $key,
        public DataType $type,
        public TranslatableMessage $label,
    ) {
    }

    /**
     * Get available operators for this field.
     *
     * @return array<FilterOperator>
     */
    public function getAvailableOperators(): array
    {
        return $this->type->getAvailableOperators();
    }
}
