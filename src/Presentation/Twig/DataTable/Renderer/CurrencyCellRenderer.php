<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Renderer;

use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Util\ValueReaderInterface;

readonly class CurrencyCellRenderer implements CellRendererInterface
{
    public function __construct(
        private ValueReaderInterface $valueReader,
    ) {
    }

    #[\Override]
    public function render(Column $column, mixed $row): string
    {
        $value = $this->valueReader->read($row, $column->key);

        if (null === $value) {
            return '-';
        }

        if (! is_numeric($value)) {
            return '-';
        }

        return number_format((float) $value, 2, ',', '.').' €';
    }

    #[\Override]
    public function supports(Column $column): bool
    {
        return DataType::CURRENCY === $column->type;
    }
}
