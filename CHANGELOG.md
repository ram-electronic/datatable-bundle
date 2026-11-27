# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

Found while adding the bundle to a fresh Symfony 8.1 / UX 3 app: it needed
four workarounds there, which are no longer necessary.

### Changed

- All texts are in the bundle's `datatable` translation domain, with English
  and German translations: the filter form (`action.apply_filters`,
  `action.clear_filters`, `action.add_filter`, `action.remove_filter`,
  `label.search`, `placeholder.search`, `label.filter_condition`,
  `label.select_field`, `label.select_operator`, `label.filter_value`), the
  operators (`filter.operator.*`) and the pagination. Before, all but the
  previous/next labels were looked up in the app's `messages` domain, so every
  app had to define them and they could clash with the app's own keys. Apps
  that defined these keys in `messages` can delete them; to change a text,
  override it in `translations/datatable.<locale>.xlf`. Column labels stay in
  the app's domain (`FilterType` and `FilterConditionType` default to
  `translation_domain: datatable`; the field choices use the columns'
  `TranslatableMessage`s).
- The pagination summary is one message, `pagination.summary` with
  `%from%`, `%to%` and `%total%`, instead of `pagination.showing`,
  `pagination.to`, `pagination.of` and `pagination.results`.
- The pagination chevrons are inline SVGs; `symfony/ux-icons` is no longer a
  dependency. Before, apps had to register `UXIconsBundle` by hand (Flex
  doesn't for a transitive dependency) and the icons were fetched from
  Iconify at runtime, because the bundle's `assets/icons/` weren't on the
  app's icon path.

### Fixed

- `FilterForm`, `FilterCondition` and `PaginationControl` have explicit names
  and `@DataTable` templates. Before, an app needed a
  `twig_component.defaults` entry for the bundle's namespace, or
  `cache:clear` failed with "Could not generate a component name".
- The bundle adds its filter form theme to `twig.form_themes` itself.
- `FilterForm` (de)hydrates `initialFormData` itself. Before, rendering it
  with at least one filter condition failed with "Unable to dehydrate value of
  type array for property filters" unless the app had
  `phpstan/phpdoc-parser` and `phpdocumentor/type-resolver` installed.

## [1.1.1] - 2026-11-26

### Fixed

- The `Table:*` sub-components (`Body`, `Cell`, `Head`, `Header`, `Row`,
  `ActionsCell`, `DataCell`, `HeadRow`, `SortableHeader`) are now
  `DataTable:Table:*` with explicit `@DataTable` templates. 1.0.0 started
  registering them unprefixed, which replaced a consuming app's own
  `Table:Head`, `Table:Row` etc. (e.g. from the Symfony UX Toolkit). Apps that
  referenced the bundle's `Table:*` names directly must add the `DataTable:`
  prefix.

### Changed

- Allow Symfony 8 (`^7.0 || ^8.0`) and Symfony UX 3 (`ux-live-component` and
  `ux-twig-component` `^2.0 || ^3.0`, `ux-icons` `^2.32 || ^3.0`). CI now runs
  PHPUnit on Symfony 7.4 + UX 2 (PHP 8.4) and Symfony 8 + UX 3 (PHP 8.5).
- Drop the hardcoded `version` from `composer.json`. Composer and Packagist
  take the version from the git tag; a mismatching `version` field makes
  Packagist skip the tag.

### Added

- Default `DataTable:Card`, `DataTable:ButtonRow`, `DataTable:Button:Button`,
  `DataTable:Button:Link`, `DataTable:Form:Input`, `DataTable:Pagination:Content`,
  `DataTable:Pagination:Previous`, and `DataTable:Pagination:Next` components,
  so `FilterForm`, `FilterCondition`, and `PaginationControl` render out of
  the box with zero components required from the consuming app. All are
  overridable per-file via Symfony's bundle-template-override mechanism.
- `twig/extra-bundle`, `twig/html-extra`, `tales-from-a-dev/twig-tailwind-extra`,
  and `symfony/ux-icons` as direct dependencies.
- A bundle-owned `datatable` translation domain (English and German) for the
  pagination previous/next labels and aria-labels.
- An `Integration` test suite (`tests/Integration`, backed by a minimal test
  `Kernel`) that renders `FilterForm`, `FilterCondition`, and
  `PaginationControl` end-to-end to catch "Unknown component" regressions in
  CI without needing a full consuming app.
- `Table`/`Pagination` (top-level components) renamed to `DataTable:Table`/
  `DataTable:Pagination`, and `FilterForm`/`FilterCondition`'s `<twig:Button>`/
  `<twig:Input>` references fixed to the app's actual `Button:Button`/
  `Form:Input` naming convention — both fixed as `DataTable:`-prefixed defaults
  in this release; see "Added" above.
- `symfony/framework-bundle` and `twig/twig` restored as direct dependencies
  (`FilterForm` extends `Symfony\Bundle\FrameworkBundle\Controller\AbstractController`
  and `Table/ActionsCell`/`DataCell` use `Twig\Markup` directly; both were
  needed but had been dropped from `composer.json`, breaking a clean
  `composer install`).

### Changed

- `FilterForm`, `FilterCondition`, and `PaginationControl` now reference
  `DataTable:`-prefixed component names (`DataTable:Card`, `DataTable:Form:Input`,
  `DataTable:Pagination:Content`, etc.) instead of bare ones.

## [1.0.0]

Initial release.

### Added

- `TableCriteria` / `Column` / `Sort` / `Pagination` read models for building
  filterable, sortable, paginated table views.
- `FilterFieldRegistry`, `FilterCollectionBuilder`, and `TableCriteriaBuilder`
  services for turning form input into query criteria.
- `DoctrineTableReadModelRepository` base repository with reusable filtering,
  sorting, and pagination against Doctrine ORM.
- `FilterForm` and `FilterCondition` Symfony UX Live Components, backed by
  `FilterType` / `FilterConditionType` forms, for dynamic filter UIs.
- `PaginationControl` and non-colliding `Table:*` Twig components
  (`ActionsCell`, `DataCell`, `HeadRow`, `SortableHeader`) for rendering.
- Cell renderers for currency, date, date-time, and percentage values.
