# DataTable Bundle

Reusable DataTable and Pagination bundle for Symfony applications: filterable,
sortable, paginated table views built on Doctrine ORM, Symfony Forms, and
Symfony UX Live Components.

## Requirements

- PHP 8.4+
- Symfony 7.0+ (`framework-bundle`, `form`, `routing`, `security-csrf`,
  `translation`, `validator`)
- Symfony UX `live-component` and `twig-component` 2.0+
- Doctrine `orm` 3.0+ and `doctrine-bundle` 2.11+
- Twig 3.0+

## Installation

```bash
composer require ram-electronic/datatable-bundle
```

If your app doesn't use Symfony Flex, register the bundle manually in
`config/bundles.php`:

```php
return [
    // ...
    RamElectronic\DataTableBundle\DataTableBundle::class => ['all' => true],
];
```

## Twig components

The bundle auto-registers every component under the
`RamElectronic\DataTableBundle\Presentation\Twig\Components` namespace:

- `FilterForm` / `FilterCondition` — Live Components backing the filter UI
- `PaginationControl` — pagination controls
- `DataTable:Table` / `DataTable:Pagination` — generic table/nav wrappers.
  Named with an explicit `DataTable:` prefix (rather than the bare `Table`/
  `Pagination` a plain `#[AsTwigComponent]` would produce) so they can never
  collide with a top-level `<twig:Table>` or `<twig:Pagination>` component a
  consuming app defines for its own, unrelated markup.
- `Table:Body`, `Table:Cell`, `Table:Head`, `Table:Header`, `Table:Row`,
  `Table:ActionsCell`, `Table:DataCell`, `Table:HeadRow`, `Table:SortableHeader`
  — already namespaced under `Table:`, so no separate prefixing is needed.

See `templates/components/` for the reference markup these components render.

### Components you must provide

The bundle's own templates assume the consuming app supplies a small
shadcn/ui-style component set, under these exact names, in its own Twig
component namespace:

- `Card`
- `ButtonRow`, `Button:Button`, `Button:Link`
- `Form:Input`
- `Pagination:Content`, `Pagination:Previous`, `Pagination:Next`

Without these, `FilterForm`, `FilterCondition`, and `PaginationControl` will
fail to render with an "Unknown component" error.

## Usage

1. Define your filterable columns with an `Application\ReadModel\FilterFieldRegistry`
   subclass built from `Column` definitions.
2. Extend `Infrastructure\Repository\DoctrineTableReadModelRepository` for your
   read-model entity's repository — it provides filtering, sorting, and
   pagination against a Doctrine `QueryBuilder` given a field-to-column mapping.
3. In your controller, build a `TableCriteria` from request/form data via
   `Presentation\Service\TableCriteriaBuilder` (which composes
   `FilterCollectionBuilder`), and pass it to your repository.
4. Render results with `PaginationControl` and the bundle's `DataTable:Table`/
   `Table:*` components inside your own table markup.

## Testing

```bash
composer install
vendor/bin/phpunit
```

## License

MIT, see [LICENSE](LICENSE).
