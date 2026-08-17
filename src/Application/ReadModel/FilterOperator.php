<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Application\ReadModel;

/**
 * Enum representing filter comparison operators.
 * Provides type-safe operator definitions for filtering operations.
 */
enum FilterOperator: string
{
    case EQUALS = 'equals';
    case NOT_EQUALS = 'not_equals';
    case CONTAINS = 'contains';
    case NOT_CONTAINS = 'not_contains';
    case LESS_THAN = 'less_than';
    case LESS_THAN_OR_EQUAL = 'less_than_or_equal';
    case GREATER_THAN = 'greater_than';
    case GREATER_THAN_OR_EQUAL = 'greater_than_or_equal';
    case STARTS_WITH = 'starts_with';
    case ENDS_WITH = 'ends_with';
    case IS_TRUE = 'is_true';
    case IS_FALSE = 'is_false';

    /**
     * Get operators applicable to string fields.
     *
     * @return array<FilterOperator>
     */
    public static function forStringFields(): array
    {
        return [
            self::EQUALS,
            self::NOT_EQUALS,
            self::CONTAINS,
            self::NOT_CONTAINS,
            self::STARTS_WITH,
            self::ENDS_WITH,
        ];
    }

    /**
     * Get operators applicable to numeric/date fields.
     *
     * @return array<FilterOperator>
     */
    public static function forNumericFields(): array
    {
        return [
            self::EQUALS,
            self::NOT_EQUALS,
            self::LESS_THAN,
            self::LESS_THAN_OR_EQUAL,
            self::GREATER_THAN,
            self::GREATER_THAN_OR_EQUAL,
        ];
    }

    /**
     * Get operators applicable to entities.
     *
     * @return array<FilterOperator>
     */
    public static function forEntities(): array
    {
        return [
            self::EQUALS,
            self::NOT_EQUALS,
        ];
    }

    /**
     * Get operators applicable to boolean fields.
     *
     * @return array<FilterOperator>
     */
    public static function forBooleanFields(): array
    {
        return [
            self::IS_TRUE,
            self::IS_FALSE,
        ];
    }

    /**
     * Check if this operator is a unary operator (doesn't require a value).
     */
    public function isUnaryOperator(): bool
    {
        return match ($this) {
            self::IS_TRUE, self::IS_FALSE => true,
            default => false,
        };
    }

    /**
     * Get human-readable label for translation.
     */
    public function getLabel(): string
    {
        return 'filter.operator.'.$this->value;
    }
}
