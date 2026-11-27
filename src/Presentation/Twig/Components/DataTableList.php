<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components;

use RamElectronic\DataTableBundle\Presentation\View\Table;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * A whole list from a Table built by TableHandler::handleDefinition():
 * filter form, sortable header, rows, actions, empty text and pagination.
 *
 *     <twig:DataTable:List :table="table">
 *         <twig:block name="cell_status">{{ row.status|trans }}</twig:block>
 *         <twig:block name="actions"><a href="{{ path('edit', {id: row.id}) }}">...</a></twig:block>
 *     </twig:DataTable:List>
 *
 * Blocks (all optional; `row` is the current row in the row blocks):
 * - `cell_<column key>`: the content of that column's cells
 * - `actions`: the content of the last cell, instead of the definition's actions
 * - `empty`: the text when there are no rows
 *
 * The markup comes from the DataTable:Table:* components, so overriding
 * their templates restyles every list.
 */
#[AsTwigComponent('DataTable:List', template: '@DataTable/components/List.html.twig')]
final class DataTableList
{
    public Table $table;

    /** Show the filter form (only if the definition has filterable columns). */
    public bool $filter = true;

    /**
     * Attributes for every row, e.g. {'data-test': 'customer-row'}.
     *
     * @var array<string, string>
     */
    public array $rowAttributes = [];
}
