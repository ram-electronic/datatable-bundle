<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Renderer;

use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Presentation\Service\PercentageFormatter;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Util\ValueReaderInterface;

/**
 * Cell renderer for percentage values.
 * Formats decimal values (0.19 -> 19,00%) with German number formatting.
 */
readonly class PercentageCellRenderer implements CellRendererInterface
{
    public function __construct(
        private ValueReaderInterface $valueReader,
        private PercentageFormatter $percentageFormatter,
    ) {
    }

    #[\Override]
    public function render(Column $column, mixed $row): string
    {
        $value = $this->valueReader->read($row, $column->key);

        // Ensure value is float or string for the formatter
        if (! \is_float($value) && ! \is_string($value)) {
            return $this->percentageFormatter->format(0);
        }

        return $this->percentageFormatter->format($value);
    }

    #[\Override]
    public function supports(Column $column): bool
    {
        return DataType::PERCENTAGE === $column->type;
    }
}
