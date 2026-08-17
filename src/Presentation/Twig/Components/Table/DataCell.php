<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components\Table;

use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\TableCellRenderer;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Twig\Markup;

#[AsTwigComponent('Table:DataCell')]
final class DataCell
{
    public mixed $row;
    public Column $column;

    public function __construct(private readonly TableCellRenderer $renderer)
    {
    }

    public function htmlValue(): Markup
    {
        $data = $this->renderer->render($this->column, $this->row);

        return new Markup($data, 'UTF-8');
    }
}
