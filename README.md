# DataTable Bundle

Reusable DataTable and Pagination bundle for Symfony applications: filterable,
sortable, paginated table views built on Doctrine ORM, Symfony Forms, and
Symfony UX Live Components.

## Requirements

- PHP 8.4+
- Symfony 7.0+ (`framework-bundle`, `form`, `routing`, `security-csrf`,
  `translation`, `validator`, `twig-bundle`)
- Symfony UX `live-component`, `twig-component` 2.0+, and `ux-icons` (for the
  default pagination chevrons)
- Doctrine `orm` 3.0+ and `doctrine-bundle` 2.11+
- Twig 3.0+, `twig/extra-bundle` and `twig/html-extra` (for the default
  `Button:*` components' variant styling), and `tales-from-a-dev/twig-tailwind-extra`
  (for the `tailwind_merge` filter used throughout the bundle's default markup)

## Installation

```bash
composer require ram-electronic/datatable-bundle
```

If your app doesn't use Symfony Flex, register the bundle — and the Twig
extension bundles its default components depend on — manually in
`config/bundles.php`:

```php
return [
    // ...
    Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['all' => true],
    TalesFromADev\Twig\Extra\Tailwind\Bridge\Symfony\Bundle\TalesFromADevTwigExtraTailwindBundle::class => ['all' => true],
    Symfony\UX\Icons\UXIconsBundle::class => ['all' => true],
    RamElectronic\DataTableBundle\DataTableBundle::class => ['all' => true],
];
```

## Twig components

The bundle auto-registers every component under the
`RamElectronic\DataTableBundle\Presentation\Twig\Components` namespace — there
is nothing else to install or implement, `FilterForm`, `FilterCondition`, and
`PaginationControl` render out of the box:

- `FilterForm` / `FilterCondition` — Live Components backing the filter UI
- `PaginationControl` — pagination controls
- `DataTable:Table` / `DataTable:Pagination` — generic table/nav wrappers.
- `DataTable:Card`, `DataTable:ButtonRow`, `DataTable:Button:Button`,
  `DataTable:Button:Link`, `DataTable:Form:Input`, `DataTable:Pagination:Content`,
  `DataTable:Pagination:Previous`, `DataTable:Pagination:Next` — the shadcn/ui-style
  primitives `FilterForm`/`FilterCondition`/`PaginationControl` are built from.

  All of the above are named with an explicit `DataTable:` prefix (rather than
  the bare `Table`/`Card`/`Button:Button`/etc. a plain `#[AsTwigComponent]`
  would produce) so they can never collide with a same-named top-level
  component a consuming app defines for its own, unrelated markup.
- `Table:Body`, `Table:Cell`, `Table:Head`, `Table:Header`, `Table:Row`,
  `Table:ActionsCell`, `Table:DataCell`, `Table:HeadRow`, `Table:SortableHeader`
  — already namespaced under `Table:`, so no separate prefixing is needed.

See `templates/components/` for the reference markup these components render.

### Customizing the default components

Every default component's look can be overridden per-file, without touching
registration, using Symfony's standard bundle-template-override mechanism:
drop a file at `templates/bundles/DataTableBundle/<relative-path>` in your app
— e.g. `templates/bundles/DataTableBundle/components/Card.html.twig` — and
Symfony will render your version instead of the bundle's default for that one
component, leaving the rest of the bundle's defaults untouched.

Pagination labels (`Previous`/`Next` and their `aria-label`s) live in the
bundle's own `datatable` translation domain (`translations/datatable.*.xlf`,
shipped in English and German) rather than your app's `messages` domain, so
they work without any translation keys of your own.

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
