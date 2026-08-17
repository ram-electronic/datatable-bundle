<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Renderer;

use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Util\ValueReaderInterface;

readonly class DateTimeCellRenderer implements CellRendererInterface
{
    public function __construct(
        private ValueReaderInterface $valueReader,
    ) {
    }

    #[\Override]
    public function render(Column $column, mixed $row): string
    {
        $value = $this->valueReader->read($row, $column->key);

        $date = $this->toDateTime($value);
        if (! $date instanceof \DateTimeInterface) {
            return '-';
        }

        return $date->format('d.m.Y H:i');
    }

    #[\Override]
    public function supports(Column $column): bool
    {
        return DataType::DATETIME === $column->type;
    }

    /**
     * Accept string-backed datetime values too, since ValueReader supports
     * array/DTO-style rows (a first-class case) where a datetime field may
     * be a plain string rather than a \DateTimeInterface instance.
     */
    private function toDateTime(mixed $value): ?\DateTimeInterface
    {
        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        if (\is_string($value) && '' !== trim($value)) {
            try {
                return new \DateTimeImmutable($value);
            } catch (\Exception) {
                return null;
            }
        }

        return null;
    }
}
