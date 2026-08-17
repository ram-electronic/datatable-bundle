<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Renderer;

use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Util\ValueReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(CellRendererInterface::class, attributes: ['priority' => -100])]
readonly class DefaultRenderer implements CellRendererInterface
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
            return '';
        }

        if (\is_scalar($value)) {
            return (string) $value;
        }

        return '';
    }

    #[\Override]
    public function supports(Column $column): bool
    {
        return true;
    }
}
