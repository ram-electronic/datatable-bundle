<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Unit\Application\ReadModel;

use PHPUnit\Framework\TestCase;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterCondition;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterOperator;

class FilterConditionTest extends TestCase
{
    public function testConstructorThrowsExceptionForEmptyField(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Filter field cannot be empty');

        new FilterCondition('', FilterOperator::EQUALS, 'value');
    }

    public function testConstructorThrowsExceptionForEmptyValueOnBinaryOperator(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Filter value cannot be empty');

        new FilterCondition('name', FilterOperator::EQUALS, '');
    }

    public function testConstructorAllowsNullValueForUnaryOperators(): void
    {
        $condition = new FilterCondition('active', FilterOperator::IS_TRUE, null);

        $this->assertNull($condition->value);
    }

    public function testConstructorAcceptsScalarValues(): void
    {
        $condition = new FilterCondition('name', FilterOperator::EQUALS, 'example');

        $this->assertSame('example', $condition->value);
    }

    public function testConstructorAcceptsDateTimeValue(): void
    {
        $date = new \DateTimeImmutable('2024-01-01');
        $condition = new FilterCondition('createdAt', FilterOperator::GREATER_THAN, $date);

        $this->assertSame($date, $condition->value);
    }

    public function testConstructorThrowsExceptionForArrayValueOnBinaryOperator(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Filter value must be scalar or a DateTimeInterface instance');

        new FilterCondition('name', FilterOperator::EQUALS, ['unexpected', 'array']);
    }

    public function testConstructorThrowsExceptionForObjectValueOnBinaryOperator(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Filter value must be scalar or a DateTimeInterface instance');

        new FilterCondition('name', FilterOperator::EQUALS, new \stdClass());
    }
}
