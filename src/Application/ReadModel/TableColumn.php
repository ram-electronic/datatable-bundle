<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Application\ReadModel;

use Symfony\Component\Translation\TranslatableMessage;

/**
 * A column the DataTable component shows: its value comes from the row's
 * property or getter named $key, rendered by the cell renderer for $type
 * unless the page passes a `cell_<key>` block.
 */
readonly class TableColumn extends Column
{
    public function __construct(
        string $key,
        DataType $type,
        TranslatableMessage $label,
        /** The header links to sorting by $key; the repository must map it. */
        public bool $sortable = false,
        /** The filter form offers it; the repository must map it. */
        public bool $filterable = false,
    ) {
        parent::__construct($key, $type, $label);
    }
}
