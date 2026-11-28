# DataTable Bundle

<a href="https://0rwx.com" title="AI:W AI collaboration, AI wrote, a human read and fixed all of it">
  <picture>
    <source srcset="https://raw.githubusercontent.com/NamtaoProductions/0rwx.com/main/static/AI-W-dark.svg" media="(prefers-color-scheme: dark)">
    <img src="https://raw.githubusercontent.com/NamtaoProductions/0rwx.com/main/static/AI-W-light.svg" alt="AI:W" height="32">
  </picture>
</a>

Reusable DataTable and Pagination bundle for Symfony applications: filterable,
sortable, paginated table views built on Doctrine ORM, Symfony Forms, and
Symfony UX Live Components.

## Requirements

- PHP 8.4+
- Symfony 7.0+ (`framework-bundle`, `form`, `routing`, `security-csrf`,
  `translation`, `validator`, `twig-bundle`)
- Symfony UX `live-component` and `twig-component` 2.0+
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
    RamElectronic\DataTableBundle\DataTableBundle::class => ['all' => true],
];
```

## Twig components

The bundle registers every component with an explicit name and template, so
your app needs no `twig_component.defaults` entry for the bundle's namespace,
and it adds its filter form theme (`@DataTable/form/filter_theme.html.twig`)
to `twig.form_themes` itself. `FilterForm`, `FilterCondition`, and
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
- `DataTable:Table:Body`, `DataTable:Table:Cell`, `DataTable:Table:Head`,
  `DataTable:Table:Header`, `DataTable:Table:Row`, `DataTable:Table:ActionsCell`,
  `DataTable:Table:DataCell`, `DataTable:Table:HeadRow`,
  `DataTable:Table:SortableHeader`. A bare `Table:` namespace is not enough:
  apps commonly have their own `Table:Head`, `Table:Row` etc. (for example
  from the Symfony UX Toolkit), and a bundle component with the same name
  replaces the app's.

See `templates/components/` for the reference markup these components render.

### Customizing the default components

Every default component's look can be overridden per-file, without touching
registration, using Symfony's standard bundle-template-override mechanism:
drop a file at `templates/bundles/DataTableBundle/<relative-path>` in your app
— e.g. `templates/bundles/DataTableBundle/components/Card.html.twig` — and
Symfony will render your version instead of the bundle's default for that one
component, leaving the rest of the bundle's defaults untouched.

### Translations

All texts of the bundle (filter form, operators, pagination) live in its own
`datatable` translation domain (`translations/datatable.*.xlf`, shipped in
English and German), not in your app's `messages` domain, so they work without
any translation keys of your own and can't clash with them. To change a text,
override the key in your app's `translations/datatable.<locale>.xlf`.

Column labels are yours: the `TranslatableMessage` of each `Column` and the
`label` of `DataTable:Table:SortableHeader` are translated in your app's
domain.

### Tailwind CSS

The default components use Tailwind classes. With Tailwind 4, let it scan the
bundle's templates, e.g. in `assets/styles/app.css`:

```css
@source "../../vendor/ram-electronic/datatable-bundle/templates";
```

## Usage

1. Define your filterable columns with an `Application\ReadModel\FilterFieldRegistry`
   subclass built from `Column` definitions. `FilterForm` rebuilds the
   registry as `new YourRegistry($fields)`, so a subclass that defines its
   columns itself takes them as an optional argument:
   `public function __construct(?array $fields = null) { parent::__construct($fields ?? [new Column(...), ...]); }`
2. Extend `Infrastructure\Repository\DoctrineTableReadModelRepository` for your
   read-model entity's repository — it provides filtering, sorting, and
   pagination against a Doctrine `QueryBuilder` given a field-to-column mapping.
   Give it a method that takes a `TableCriteria` and returns a
   `PaginatedResult` (apply filters, search, count, sorting, pagination).
3. In your controller, let `Presentation\Service\TableHandler` handle the
   request. It submits the filter form from the query string (an invalid one
   falls back to the defaults), builds the `TableCriteria`, calls your finder
   and returns one `Presentation\View\Table`:

   ```php
   public function index(Request $request, TableHandler $tableHandler, CustomerFilterFields $filterFields, CustomerRepository $repository): Response
   {
       return $this->render('customer/index.html.twig', [
           'table' => $tableHandler->handle($request, $filterFields, new Sort('name', SortDirection::ASC), $repository->findForTable(...)),
       ]);
   }
   ```

4. Pass the table to the components; your own table markup renders the rows:

   ```twig
   <twig:FilterForm :table="table" />

   <table>
       <tr>
           <th><twig:DataTable:Table:SortableHeader :table="table" sortKey="name" label="label.name" /></th>
       </tr>
       {% for customer in table.items %}
           <tr><td>{{ customer.name }}</td></tr>
       {% endfor %}
   </table>

   <twig:PaginationControl :table="table" />
   ```

   `table` fills the components' other props (`initialFormData`,
   `fieldRegistry`, `actionUrl`, `currentSort`, `route`, `routeParams`,
   `form`, `pagination`, `filterFormName`); props you pass explicitly win.
   Without `TableHandler`, build a `TableCriteria` with
   `Presentation\Service\TableCriteriaBuilder` and pass those props
   yourself, as before.

## Rendering a whole list

Instead of writing the table markup per page, describe the list once and let
`DataTable:List` render it:

```php
final class CustomerTable extends TableDefinition
{
    public function columns(): array
    {
        return [
            new TableColumn('name', DataType::STRING, new TranslatableMessage('label.name'), sortable: true, filterable: true),
            new TableColumn('status', DataType::STRING, new TranslatableMessage('label.status')),
            new TableColumn('createdAt', DataType::DATE, new TranslatableMessage('label.created_at'), sortable: true),
        ];
    }

    public function defaultSort(): Sort
    {
        return new Sort('name', SortDirection::ASC);
    }

    public function actions(): array
    {
        return [Action::edit('customer_edit')];
    }
}
```

The repository takes its filter fields from the definition
(`parent::__construct($customerTable->filterFields(), ...)`) and maps the
filterable and sortable keys. The controller:

```php
return $this->render('customer/index.html.twig', [
    'table' => $tableHandler->handleDefinition($request, $customerTable, $repository->findForTable(...)),
]);
```

The page:

```twig
<twig:DataTable:List :table="table" :rowAttributes="{'data-test': 'customer-row'}">
    {# optional, `row` is the current row #}
    <twig:block name="cell_status"><span class="badge">{{ row.status|trans }}</span></twig:block>
    <twig:block name="actions"><a href="{{ path('customer_edit', {id: row.id}) }}">Edit</a></twig:block>
    <twig:block name="empty">No customers yet.</twig:block>
</twig:DataTable:List>
```

A column without a `cell_<key>` block shows the row's property or getter
named like the key through the cell renderer for its `DataType`
(translated if the value implements `TranslatableInterface`, e.g. an enum).
Without an `actions` block the definition's `actions()` are rendered. The
filter form appears when the definition has filterable columns
(`filter="false"` hides it).

The list is built from `DataTable:Table`, `Table:Head`, `Table:HeadRow`,
`Table:Header`, `Table:Body`, `Table:Row` and `Table:Cell`. To give every
list your app's look, override those templates
(`templates/bundles/DataTableBundle/components/Table/Row.html.twig`, ...),
e.g. to render your own table components.

## Development

The dev setup runs PHP, Composer and Xdebug in a container (`compose.yaml`),
so you need only Docker and make:

```bash
make install   # build the image, resolve and install the dependencies
make test      # run PHPUnit; make test ARGS="--filter FilterForm"
make help      # all targets
```

`composer.lock` is not committed, so `make install` resolves the dependencies
like CI does, for PHP 8.5, Symfony 8 and UX 3 by default. Pick other
versions with `make install test PHP_VERSION=8.4 SYMFONY=7.4.* UX=^2.0`,
or run both CI matrix entries with `make test-ci`.

Xdebug is off by default. `XDEBUG_MODE=debug make test` connects to your
IDE on port 9003 (path mapping: the checkout is `/app`, server name
`datatable-bundle`); `XDEBUG_MODE=coverage` enables coverage reports.

Without Docker, with PHP 8.4+ and Composer on the host:

```bash
composer install
vendor/bin/phpunit
```

## License

MIT, see [LICENSE](LICENSE).
