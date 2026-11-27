<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Application\ReadModel;

/**
 * Everything about one list in one place: the columns it shows (and which of
 * them can be sorted and filtered), the default sort and the row actions.
 * Pass it to TableHandler::handleDefinition() and render the result with
 * <twig:DataTable:List :table="table" />.
 *
 * Subclasses in the app are ordinary services (autowired), e.g.
 *
 *     final class CustomerTable extends TableDefinition
 *     {
 *         public function columns(): array
 *         {
 *             return [
 *                 new TableColumn('name', DataType::STRING, new TranslatableMessage('label.name'), sortable: true, filterable: true),
 *                 new TableColumn('createdAt', DataType::DATE, new TranslatableMessage('label.created_at'), sortable: true),
 *             ];
 *         }
 *
 *         public function defaultSort(): Sort
 *         {
 *             return new Sort('name', SortDirection::ASC);
 *         }
 *     }
 */
abstract class TableDefinition
{
    /**
     * @return list<TableColumn> in display order
     */
    abstract public function columns(): array;

    abstract public function defaultSort(): Sort;

    /**
     * Rendered by the bundle's ActionsCellRenderer unless the page passes an
     * `actions` block.
     *
     * @return list<Action>
     */
    public function actions(): array
    {
        return [];
    }

    /**
     * The filterable columns, for the filter form and the repository.
     */
    final public function filterFields(): FilterFieldRegistry
    {
        return new FilterFields(array_values(array_filter(
            $this->columns(),
            static fn (TableColumn $column): bool => $column->filterable,
        )));
    }
}
