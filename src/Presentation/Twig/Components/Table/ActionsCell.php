<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components\Table;

use RamElectronic\DataTableBundle\Application\ReadModel\Action;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Renderer\ActionsCellRenderer;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Twig\Markup;

#[AsTwigComponent('Table:ActionsCell')]
final class ActionsCell
{
    /** @var array<Action> */
    public array $actions = [];
    public mixed $row;

    public function __construct(private readonly ActionsCellRenderer $renderer)
    {
    }

    public function htmlValue(): Markup
    {
        $data = $this->renderer->render($this->actions, $this->row);

        return new Markup($data, 'UTF-8');
    }
}
