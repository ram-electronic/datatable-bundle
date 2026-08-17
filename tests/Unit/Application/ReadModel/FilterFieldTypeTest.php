<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Unit\Application\ReadModel;

use PHPUnit\Framework\TestCase;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterOperator;

class FilterFieldTypeTest extends TestCase
{
    public function testStringFieldTypeReturnsCorrectOperators(): void
    {
        $operators = DataType::STRING->getAvailableOperators();

        $this->assertContains(FilterOperator::EQUALS, $operators);
        $this->assertContains(FilterOperator::NOT_EQUALS, $operators);
        $this->assertContains(FilterOperator::CONTAINS, $operators);
        $this->assertContains(FilterOperator::NOT_CONTAINS, $operators);
        $this->assertContains(FilterOperator::STARTS_WITH, $operators);
        $this->assertContains(FilterOperator::ENDS_WITH, $operators);

        // String fields should not have numeric operators
        $this->assertNotContains(FilterOperator::LESS_THAN, $operators);
        $this->assertNotContains(FilterOperator::GREATER_THAN, $operators);
    }

    public function testNumericFieldTypeReturnsCorrectOperators(): void
    {
        $operators = DataType::NUMERIC->getAvailableOperators();

        $this->assertContains(FilterOperator::EQUALS, $operators);
        $this->assertContains(FilterOperator::NOT_EQUALS, $operators);
        $this->assertContains(FilterOperator::LESS_THAN, $operators);
        $this->assertContains(FilterOperator::LESS_THAN_OR_EQUAL, $operators);
        $this->assertContains(FilterOperator::GREATER_THAN, $operators);
        $this->assertContains(FilterOperator::GREATER_THAN_OR_EQUAL, $operators);

        // Numeric fields should not have string operators
        $this->assertNotContains(FilterOperator::CONTAINS, $operators);
        $this->assertNotContains(FilterOperator::STARTS_WITH, $operators);
    }

    public function testBooleanFieldTypeReturnsCorrectOperators(): void
    {
        $operators = DataType::BOOLEAN->getAvailableOperators();

        $this->assertCount(2, $operators, 'Boolean fields should have IS_TRUE and IS_FALSE operators');
        $this->assertContains(FilterOperator::IS_TRUE, $operators);
        $this->assertContains(FilterOperator::IS_FALSE, $operators);

        // Boolean fields should not have other operators
        $this->assertNotContains(FilterOperator::EQUALS, $operators);
        $this->assertNotContains(FilterOperator::NOT_EQUALS, $operators);
        $this->assertNotContains(FilterOperator::CONTAINS, $operators);
        $this->assertNotContains(FilterOperator::LESS_THAN, $operators);
        $this->assertNotContains(FilterOperator::GREATER_THAN, $operators);
    }

    public function testDateFieldTypeReturnsCorrectOperators(): void
    {
        $operators = DataType::DATE->getAvailableOperators();

        // Date fields should have same operators as numeric fields
        $this->assertContains(FilterOperator::EQUALS, $operators);
        $this->assertContains(FilterOperator::NOT_EQUALS, $operators);
        $this->assertContains(FilterOperator::LESS_THAN, $operators);
        $this->assertContains(FilterOperator::LESS_THAN_OR_EQUAL, $operators);
        $this->assertContains(FilterOperator::GREATER_THAN, $operators);
        $this->assertContains(FilterOperator::GREATER_THAN_OR_EQUAL, $operators);

        // Date fields should not have string operators
        $this->assertNotContains(FilterOperator::CONTAINS, $operators);
        $this->assertNotContains(FilterOperator::STARTS_WITH, $operators);
    }

    public function testCurrencyFieldTypeReturnsCorrectOperators(): void
    {
        $operators = DataType::CURRENCY->getAvailableOperators();

        // Currency fields should have same operators as numeric fields
        $this->assertContains(FilterOperator::EQUALS, $operators);
        $this->assertContains(FilterOperator::NOT_EQUALS, $operators);
        $this->assertContains(FilterOperator::LESS_THAN, $operators);
        $this->assertContains(FilterOperator::LESS_THAN_OR_EQUAL, $operators);
        $this->assertContains(FilterOperator::GREATER_THAN, $operators);
        $this->assertContains(FilterOperator::GREATER_THAN_OR_EQUAL, $operators);

        // Currency fields should not have string operators
        $this->assertNotContains(FilterOperator::CONTAINS, $operators);
        $this->assertNotContains(FilterOperator::STARTS_WITH, $operators);
    }
}
