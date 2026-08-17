<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Infrastructure\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterCollection;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterFieldRegistry;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterOperator;
use RamElectronic\DataTableBundle\Application\ReadModel\TableCriteria;

/**
 * Base repository for table read models with common filtering and pagination functionality.
 * Provides reusable methods for applying operator-based filters, sorting, and pagination.
 *
 * @template T of object
 *
 * @extends ServiceEntityRepository<T>
 */
abstract class DoctrineTableReadModelRepository extends ServiceEntityRepository
{
    /** @var array<string> */
    private readonly array $validFieldKeys;

    /**
     * @param array<string, string> $fieldMapping Maps field keys to database columns
     */
    public function __construct(
        private readonly FilterFieldRegistry $fieldRegistry,
        private readonly array $fieldMapping,
        ManagerRegistry $managerRegistry,
        string $entityClass,
    ) {
        parent::__construct($managerRegistry, $entityClass);
        // Cache valid field keys to avoid repeated method calls in filtering loop
        $this->validFieldKeys = $this->fieldRegistry->getKeys();

        $missingMappings = array_diff($this->validFieldKeys, array_keys($fieldMapping));
        if ([] !== $missingMappings) {
            throw new \LogicException(\sprintf(
                'Field(s) "%s" are registered in the field registry but have no entry in $fieldMapping.',
                implode('", "', $missingMappings)
            ));
        }
    }

    /**
     * Get total count for pagination (before applying limit/offset).
     */
    protected function getTotalCount(QueryBuilder $qb): int
    {
        $countQb = clone $qb;
        $countQb->select('COUNT(DISTINCT '.$this->getEntityAlias($qb).'.id)');

        return (int) $countQb->getQuery()->getSingleScalarResult();
    }

    /**
     * Apply pagination to query builder.
     */
    protected function applyPagination(QueryBuilder $qb, TableCriteria $criteria): void
    {
        $qb->setFirstResult($criteria->getOffset())
            ->setMaxResults($criteria->getLimit());
    }

    /**
     * Apply operator-based filters to query builder.
     */
    final protected function applyFilters(
        QueryBuilder $qb,
        FilterCollection $filters,
    ): void {
        $paramIndex = 0;

        foreach ($filters->getConditions() as $condition) {
            // Validate field is whitelisted
            if (! \in_array($condition->field, $this->validFieldKeys, true)) {
                continue; // Skip invalid fields
            }

            // Validate operator is applicable to this field's data type (a request built
            // outside the JS-driven operator dropdown could pair an incompatible operator
            // with the field, e.g. CONTAINS on a BOOLEAN column)
            $fieldDefinition = $this->fieldRegistry->getField($condition->field);
            if (! $fieldDefinition instanceof Column || ! \in_array($condition->operator, $fieldDefinition->getAvailableOperators(), true)) {
                continue; // Skip invalid operator/field combinations
            }

            $column = $this->fieldMapping[$condition->field];
            $paramName = 'filter_'.$paramIndex;
            $value = $condition->value;

            // Skip value validation for unary operators
            // Ensure value is string, numeric or a date for comparison operations (skip
            // non-stringifiable, non-date values) - DataType::DATE/DATETIME columns only ever
            // offer numeric-comparison operators (see DataType::getAvailableOperators()), so a
            // DateTimeInterface value here only ever reaches the raw-$value branches below, never
            // the string-coercion ones.
            if (! $condition->operator->isUnaryOperator() && (! \is_string($value) && ! is_numeric($value) && ! $value instanceof \DateTimeInterface)) {
                ++$paramIndex;

                continue;
            }

            // Compute string value only for binary operators
            $stringValue = '';
            if (! $condition->operator->isUnaryOperator() && (\is_string($value) || is_numeric($value))) {
                $stringValue = (string) $value;
            }

            match ($condition->operator) {
                FilterOperator::EQUALS => $qb->andWhere("{$column} = :{$paramName}")
                    ->setParameter($paramName, $value),
                FilterOperator::NOT_EQUALS => $qb->andWhere("{$column} != :{$paramName}")
                    ->setParameter($paramName, $value),
                FilterOperator::CONTAINS => $qb->andWhere("LOWER({$column}) LIKE LOWER(:{$paramName})")
                    ->setParameter($paramName, '%'.$stringValue.'%'),
                FilterOperator::NOT_CONTAINS => $qb->andWhere("LOWER({$column}) NOT LIKE LOWER(:{$paramName})")
                    ->setParameter($paramName, '%'.$stringValue.'%'),
                FilterOperator::STARTS_WITH => $qb->andWhere("LOWER({$column}) LIKE LOWER(:{$paramName})")
                    ->setParameter($paramName, $stringValue.'%'),
                FilterOperator::ENDS_WITH => $qb->andWhere("LOWER({$column}) LIKE LOWER(:{$paramName})")
                    ->setParameter($paramName, '%'.$stringValue),
                FilterOperator::LESS_THAN => $qb->andWhere("{$column} < :{$paramName}")
                    ->setParameter($paramName, $value),
                FilterOperator::LESS_THAN_OR_EQUAL => $qb->andWhere("{$column} <= :{$paramName}")
                    ->setParameter($paramName, $value),
                FilterOperator::GREATER_THAN => $qb->andWhere("{$column} > :{$paramName}")
                    ->setParameter($paramName, $value),
                FilterOperator::GREATER_THAN_OR_EQUAL => $qb->andWhere("{$column} >= :{$paramName}")
                    ->setParameter($paramName, $value),
                FilterOperator::IS_TRUE => $qb->andWhere("{$column} = true"),
                FilterOperator::IS_FALSE => $qb->andWhere("{$column} = false"),
            };

            ++$paramIndex;
        }
    }

    /**
     * Apply quick search across all filterable fields with OR logic.
     * Searches using CONTAINS for string fields and EQUALS for numeric fields.
     */
    final protected function applySearch(
        QueryBuilder $qb,
        ?string $searchTerm,
    ): void {
        if (null === $searchTerm || '' === trim($searchTerm)) {
            return;
        }

        $searchTerm = trim($searchTerm);
        $orConditions = [];
        $paramIndex = 0;

        foreach ($this->fieldMapping as $fieldName => $column) {
            $fieldType = $this->fieldRegistry->getField($fieldName)->type ?? DataType::STRING;
            $paramName = 'search_'.$paramIndex;

            $condition = $this->buildSearchCondition($qb, $fieldType, $column, $paramName, $searchTerm);
            if ($condition instanceof Comparison) {
                $orConditions[] = $condition;
                $qb->setParameter($paramName, $this->getSearchValue($fieldType, $searchTerm));
            }

            ++$paramIndex;
        }

        // Add OR conditions to the query
        if ([] !== $orConditions) {
            $qb->andWhere($qb->expr()->orX(...$orConditions));
        }
    }

    /**
     * Build search condition for a specific field type.
     * Entity fields are excluded from search to avoid matching by ID.
     */
    private function buildSearchCondition(
        QueryBuilder $qb,
        DataType $fieldType,
        string $column,
        string $paramName,
        string $searchTerm,
    ): ?Comparison {
        return match ($fieldType) {
            DataType::STRING => $qb->expr()->like("LOWER({$column})", "LOWER(:{$paramName})"),
            DataType::NUMERIC => is_numeric($searchTerm) ? $qb->expr()->eq($column, ":{$paramName}") : null,
            DataType::BOOLEAN => $this->buildBooleanSearchCondition($qb, $column, $searchTerm),
            DataType::ENTITY => null, // Exclude entity fields from search to avoid ID matching
            DataType::DATE => null, // Date fields typically not searchable via free text
            DataType::DATETIME => null, // DateTime fields typically not searchable via free text
            DataType::CURRENCY => is_numeric($searchTerm) ? $qb->expr()->eq($column, ":{$paramName}") : null,
            DataType::PERCENTAGE => is_numeric($searchTerm) ? $qb->expr()->eq($column, ":{$paramName}") : null,
            DataType::LOGO => null, // Logo fields not searchable
        };
    }

    /**
     * Build search condition for boolean fields based on search term.
     */
    private function buildBooleanSearchCondition(QueryBuilder $qb, string $column, string $searchTerm): ?Comparison
    {
        $lowerSearchTerm = strtolower($searchTerm);

        if (\in_array($lowerSearchTerm, ['true', '1', 'yes', 'active', 'aktiv'], true)) {
            return $qb->expr()->eq($column, 'true');
        }

        if (\in_array($lowerSearchTerm, ['false', '0', 'no', 'inactive', 'inaktiv'], true)) {
            return $qb->expr()->eq($column, 'false');
        }

        return null;
    }

    /**
     * Get the value to use for search parameter based on field type.
     */
    private function getSearchValue(DataType $fieldType, string $searchTerm): string
    {
        return match ($fieldType) {
            DataType::STRING => '%'.$searchTerm.'%',
            default => $searchTerm,
        };
    }

    /**
     * Apply sorting to query builder using whitelisted columns.
     *
     * @param array<string, string> $sortKeyMapping Whitelist mapping sort keys to DB columns
     * @param string                $defaultSortKey Default sort column if key not found
     */
    protected function applySorting(
        QueryBuilder $qb,
        TableCriteria $criteria,
        array $sortKeyMapping,
        string $defaultSortKey,
    ): void {
        $sortColumn = $sortKeyMapping[$criteria->sort->key] ?? $sortKeyMapping[$defaultSortKey] ?? null;

        if (null === $sortColumn) {
            throw new \InvalidArgumentException(\sprintf(
                'Default sort key "%s" is not present in the sort key mapping.',
                $defaultSortKey
            ));
        }

        $qb->orderBy($sortColumn, $criteria->sort->direction->value);
    }

    /**
     * Add secondary sort for stable pagination.
     */
    protected function addSecondarySortById(QueryBuilder $qb, TableCriteria $criteria): void
    {
        $alias = $this->getEntityAlias($qb);
        $qb->addOrderBy($alias.'.id', $criteria->sort->direction->value);
    }

    /**
     * Extract entity alias from query builder.
     */
    private function getEntityAlias(QueryBuilder $qb): string
    {
        $aliases = $qb->getRootAliases();

        return $aliases[0] ?? 'e';
    }
}
