<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Renderer;

use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Util\ValueReaderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Cell renderer for boolean values.
 * Without this renderer, BOOLEAN columns fall through to DefaultRenderer,
 * which prints `false` as an empty string - indistinguishable from a
 * missing/null cell.
 */
readonly class BooleanCellRenderer implements CellRendererInterface
{
    public function __construct(
        private ValueReaderInterface $valueReader,
        private TranslatorInterface $translator,
    ) {
    }

    #[\Override]
    public function render(Column $column, mixed $row): string
    {
        $value = $this->valueReader->read($row, $column->key);

        if (null === $value) {
            return '';
        }

        return $this->translator->trans($value ? 'boolean.true' : 'boolean.false');
    }

    #[\Override]
    public function supports(Column $column): bool
    {
        return DataType::BOOLEAN === $column->type;
    }
}
