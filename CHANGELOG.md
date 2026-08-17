# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
