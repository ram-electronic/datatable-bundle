# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
