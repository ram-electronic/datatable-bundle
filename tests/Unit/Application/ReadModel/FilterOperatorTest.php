<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Unit\Application\ReadModel;

use PHPUnit\Framework\TestCase;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterOperator;

class FilterOperatorTest extends TestCase
{
    public function testForStringFieldsReturnsCorrectOperators(): void
    {
        $operators = FilterOperator::forStringFields();

        $this->assertCount(6, $operators);
        $this->assertContains(FilterOperator::EQUALS, $operators);
        $this->assertContains(FilterOperator::NOT_EQUALS, $operators);
        $this->assertContains(FilterOperator::CONTAINS, $operators);
        $this->assertContains(FilterOperator::NOT_CONTAINS, $operators);
        $this->assertContains(FilterOperator::STARTS_WITH, $operators);
        $this->assertContains(FilterOperator::ENDS_WITH, $operators);
    }

    public function testForNumericFieldsReturnsCorrectOperators(): void
    {
        $operators = FilterOperator::forNumericFields();

        $this->assertCount(6, $operators);
        $this->assertContains(FilterOperator::EQUALS, $operators);
        $this->assertContains(FilterOperator::NOT_EQUALS, $operators);
        $this->assertContains(FilterOperator::LESS_THAN, $operators);
        $this->assertContains(FilterOperator::LESS_THAN_OR_EQUAL, $operators);
        $this->assertContains(FilterOperator::GREATER_THAN, $operators);
        $this->assertContains(FilterOperator::GREATER_THAN_OR_EQUAL, $operators);
    }

    public function testForBooleanFieldsReturnsCorrectOperators(): void
    {
        $operators = FilterOperator::forBooleanFields();

        $this->assertCount(2, $operators, 'Boolean fields should have IS_TRUE and IS_FALSE');
        $this->assertContains(FilterOperator::IS_TRUE, $operators);
        $this->assertContains(FilterOperator::IS_FALSE, $operators);
    }

    public function testIsUnaryOperatorReturnsTrueForBooleanOperators(): void
    {
        $this->assertTrue(FilterOperator::IS_TRUE->isUnaryOperator());
        $this->assertTrue(FilterOperator::IS_FALSE->isUnaryOperator());
    }

    public function testIsUnaryOperatorReturnsFalseForBinaryOperators(): void
    {
        $this->assertFalse(FilterOperator::EQUALS->isUnaryOperator());
        $this->assertFalse(FilterOperator::NOT_EQUALS->isUnaryOperator());
        $this->assertFalse(FilterOperator::CONTAINS->isUnaryOperator());
        $this->assertFalse(FilterOperator::LESS_THAN->isUnaryOperator());
        $this->assertFalse(FilterOperator::GREATER_THAN->isUnaryOperator());
    }

    public function testGetTranslationKeyReturnsCorrectFormat(): void
    {
        $this->assertSame('filter.operator.equals', FilterOperator::EQUALS->getLabel());
        $this->assertSame('filter.operator.not_equals', FilterOperator::NOT_EQUALS->getLabel());
        $this->assertSame('filter.operator.contains', FilterOperator::CONTAINS->getLabel());
        $this->assertSame('filter.operator.less_than', FilterOperator::LESS_THAN->getLabel());
    }
}
