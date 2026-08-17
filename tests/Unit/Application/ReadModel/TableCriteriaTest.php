<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Unit\Application\ReadModel;

use PHPUnit\Framework\TestCase;
use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterCollection;
use RamElectronic\DataTableBundle\Application\ReadModel\Sort;
use RamElectronic\DataTableBundle\Application\ReadModel\SortDirection;
use RamElectronic\DataTableBundle\Application\ReadModel\TableCriteria;
use Symfony\Component\Translation\TranslatableMessage;

class TableCriteriaTest extends TestCase
{
    public function testIsColumnVisibleReturnsTrueWhenNoColumnsSpecified(): void
    {
        $criteria = new TableCriteria(
            sort: new Sort('id', SortDirection::ASC),
        );

        $this->assertTrue($criteria->isColumnVisible('customer'));
        $this->assertTrue($criteria->isColumnVisible('contractRef'));
        $this->assertTrue($criteria->isColumnVisible('anyColumn'));
    }

    public function testIsColumnVisibleReturnsTrueForVisibleColumns(): void
    {
        $criteria = new TableCriteria(
            sort: new Sort('id', SortDirection::ASC),
            columns: [
                new Column('customer', DataType::STRING, new TranslatableMessage('label.customer')),
                new Column('contractRef', DataType::STRING, new TranslatableMessage('label.contract_ref')),
            ],
            hiddenColumns: [],
        );

        $this->assertTrue($criteria->isColumnVisible('customer'));
        $this->assertTrue($criteria->isColumnVisible('contractRef'));
    }

    public function testIsColumnVisibleReturnsFalseForHiddenColumns(): void
    {
        $criteria = new TableCriteria(
            sort: new Sort('id', SortDirection::ASC),
            columns: [
                new Column('customer', DataType::STRING, new TranslatableMessage('label.customer')),
                new Column('contractRef', DataType::STRING, new TranslatableMessage('label.contract_ref')),
                new Column('active', DataType::BOOLEAN, new TranslatableMessage('label.active')),
                new Column('createdAt', DataType::NUMERIC, new TranslatableMessage('label.created_at')),
            ],
            hiddenColumns: ['customer', 'createdAt'],
        );

        $this->assertFalse($criteria->isColumnVisible('customer'));
        $this->assertTrue($criteria->isColumnVisible('contractRef'));
        $this->assertTrue($criteria->isColumnVisible('active'));
        $this->assertFalse($criteria->isColumnVisible('createdAt'));
    }

    public function testGetVisibleColumnsReturnsOnlyVisibleColumns(): void
    {
        $criteria = new TableCriteria(
            sort: new Sort('id', SortDirection::ASC),
            columns: [
                new Column('customer', DataType::STRING, new TranslatableMessage('label.customer')),
                new Column('contractRef', DataType::STRING, new TranslatableMessage('label.contract_ref')),
                new Column('active', DataType::BOOLEAN, new TranslatableMessage('label.active')),
            ],
            hiddenColumns: ['customer'],
        );

        $visibleColumns = $criteria->getVisibleColumns();

        $this->assertCount(2, $visibleColumns);
        $columnKeys = array_map(fn (Column $col): string => $col->key, $visibleColumns);
        $this->assertContains('contractRef', $columnKeys);
        $this->assertContains('active', $columnKeys);
        $this->assertNotContains('customer', $columnKeys);
    }

    public function testConstructorThrowsExceptionForInvalidPage(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Page must be at least 1.');

        new TableCriteria(
            sort: new Sort('id', SortDirection::ASC),
            page: 0,
        );
    }

    public function testConstructorThrowsExceptionForInvalidPageSize(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Page size must be between 1 and 100.');

        new TableCriteria(
            sort: new Sort('id', SortDirection::ASC),
            pageSize: 0,
        );
    }

    public function testGetOffsetCalculatesCorrectly(): void
    {
        $criteria = new TableCriteria(
            sort: new Sort('id', SortDirection::ASC),
            page: 3,
            pageSize: 20,
        );

        $this->assertSame(40, $criteria->getOffset());
    }

    public function testGetLimitReturnsPageSize(): void
    {
        $criteria = new TableCriteria(
            sort: new Sort('id', SortDirection::ASC),
            pageSize: 25,
        );

        $this->assertSame(25, $criteria->getLimit());
    }

    public function testGetPageReturnsCurrentPage(): void
    {
        $criteria = new TableCriteria(
            sort: new Sort('id', SortDirection::ASC),
            page: 5,
        );

        $this->assertSame(5, $criteria->getPage());
    }

    public function testGetPageSizeReturnsSize(): void
    {
        $criteria = new TableCriteria(
            sort: new Sort('id', SortDirection::ASC),
            pageSize: 50,
        );

        $this->assertSame(50, $criteria->getPageSize());
    }

    public function testHasFiltersReturnsFalseWhenFiltersEmpty(): void
    {
        $criteria = new TableCriteria(
            sort: new Sort('id', SortDirection::ASC),
            filters: new FilterCollection([]),
        );

        $this->assertFalse($criteria->hasFilters());
    }

    public function testPaginationIsAccessible(): void
    {
        $criteria = new TableCriteria(
            sort: new Sort('id', SortDirection::ASC),
            page: 3,
            pageSize: 15,
        );

        $this->assertSame(3, $criteria->getPage());
        $this->assertSame(15, $criteria->getPageSize());
    }
}
