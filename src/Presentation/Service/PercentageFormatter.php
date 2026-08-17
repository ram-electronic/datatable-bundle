<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Service;

/**
 * Service for formatting percentage values.
 * Provides consistent percentage formatting across the application.
 */
class PercentageFormatter
{
    /**
     * Format a decimal value as a percentage with German number formatting.
     *
     * Example: 0.19 -> "19,00%"
     *
     * @param float|string $value    The decimal value to format
     * @param int          $decimals Number of decimal places (default: 2)
     *
     * @return string The formatted percentage string
     */
    public function format(float|string $value, int $decimals = 2): string
    {
        if (! is_numeric($value)) {
            return '-';
        }

        // Convert decimal to percentage (0.19 -> 19.00)
        $percentage = (float) $value * 100;

        // Format with German number formatting (decimal comma, thousand separator dot)
        return number_format($percentage, $decimals, ',', '.').'%';
    }
}
