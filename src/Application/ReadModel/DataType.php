<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Application\ReadModel;

/**
 * Enum representing the data type of a field.
 * Determines which operators are available for filtering.
 */
enum DataType: string
{
    case STRING = 'string';
    case NUMERIC = 'numeric';
    case BOOLEAN = 'boolean';
    case ENTITY = 'entity';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case CURRENCY = 'currency';
    case PERCENTAGE = 'percentage';
    case LOGO = 'avatar';

    /**
     * Get available operators for this field type.
     *
     * @return array<FilterOperator>
     */
    public function getAvailableOperators(): array
    {
        return match ($this) {
            self::STRING => FilterOperator::forStringFields(),
            self::NUMERIC => FilterOperator::forNumericFields(),
            self::BOOLEAN => FilterOperator::forBooleanFields(),
            self::ENTITY => FilterOperator::forEntities(),
            self::DATE => FilterOperator::forNumericFields(),
            self::DATETIME => FilterOperator::forNumericFields(),
            self::CURRENCY => FilterOperator::forNumericFields(),
            self::PERCENTAGE => FilterOperator::forNumericFields(),
            self::LOGO => [], // Not filterable
        };
    }
}
