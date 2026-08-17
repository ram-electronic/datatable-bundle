<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\DataTable;

use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Renderer\CellRendererInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class TableCellRenderer
{
    /**
     * @param iterable<CellRendererInterface> $renderer
     */
    public function __construct(
        #[AutowireIterator(CellRendererInterface::class)]
        private iterable $renderer,
    ) {
    }

    public function render(Column $column, mixed $row): string
    {
        foreach ($this->renderer as $renderer) {
            if ($renderer->supports($column)) {
                return $renderer->render($column, $row);
            }
        }

        throw new \LogicException('No cell renderer found.');
    }
}
