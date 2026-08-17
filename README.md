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

The bundle registers its Twig components under the `RamElectronic\DataTableBundle\Presentation\Twig\Components`
namespace, with one exception: `Table`, `Pagination`, and the `Table\Body`,
`Table\Cell`, `Table\Head`, `Table\Header`, and `Table\Row` components are
**not** auto-registered (see `config/services.php`). Those names collide with
anonymous Twig components most consuming apps already define for their own
markup, so you're expected to supply your own `Table`/`Row`/`Cell`/etc. and
compose them with the parts the bundle *does* register:

- `FilterForm` / `FilterCondition` — Live Components backing the filter UI
- `PaginationControl` — pagination controls
- `Table:ActionsCell`, `Table:DataCell`, `Table:HeadRow`, `Table:SortableHeader`

See `templates/components/` for the reference markup these components render.

## Usage

1. Define your filterable columns with an `Application\ReadModel\FilterFieldRegistry`
   subclass built from `Column` definitions.
2. Extend `Infrastructure\Repository\DoctrineTableReadModelRepository` for your
   read-model entity's repository — it provides filtering, sorting, and
   pagination against a Doctrine `QueryBuilder` given a field-to-column mapping.
3. In your controller, build a `TableCriteria` from request/form data via
   `Presentation\Service\TableCriteriaBuilder` (which composes
   `FilterCollectionBuilder`), and pass it to your repository.
4. Render results with `PaginationControl` and the bundle's `Table:*`
   components inside your own table markup.

## Testing

```bash
composer install
vendor/bin/phpunit
```

## License

MIT, see [LICENSE](LICENSE).
