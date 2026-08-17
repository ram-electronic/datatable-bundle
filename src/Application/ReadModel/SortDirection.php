<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Application\ReadModel;

/**
 * Enum for sort direction values.
 */
enum SortDirection: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';

    /**
     * Get the opposite direction.
     */
    public function toggle(): self
    {
        return match ($this) {
            self::ASC => self::DESC,
            self::DESC => self::ASC,
        };
    }
}
