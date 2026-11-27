<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Unit\Presentation\Twig\Components;

use PHPUnit\Framework\TestCase;
use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterFieldRegistry;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterOperator;
use RamElectronic\DataTableBundle\Application\ReadModel\SortDirection;
use RamElectronic\DataTableBundle\Presentation\Dto\FilterConditionFormData;
use RamElectronic\DataTableBundle\Presentation\Dto\FilterFormData;
use RamElectronic\DataTableBundle\Presentation\Twig\Components\FilterForm;
use Symfony\Component\Translation\TranslatableMessage;

class FilterFormTest extends TestCase
{
    public function testHydrateFieldRegistryRoundTripsFieldsThroughDehydration(): void
    {
        $registry = new readonly class([
            new Column('domain', DataType::STRING, new TranslatableMessage('label.domain')),
            new Column('active', DataType::BOOLEAN, new TranslatableMessage('label.active')),
        ]) extends FilterFieldRegistry {};

        $component = new FilterForm();

        $dehydrated = $component->dehydrateFieldRegistry($registry);
        $this->assertIsString($dehydrated);

        $hydrated = $component->hydrateFieldRegistry($dehydrated);

        $this->assertInstanceOf(FilterFieldRegistry::class, $hydrated);
        $this->assertSame(['domain', 'active'], $hydrated->getKeys());
        $this->assertNotNull($hydrated->getField('domain'));
        $this->assertSame(DataType::BOOLEAN, $hydrated->getField('active')->type);
    }

    public function testFormDataRoundTripsThroughDehydration(): void
    {
        $component = new FilterForm();
        $data = new FilterFormData(
            sort: 'domain',
            direction: SortDirection::DESC,
            page: 3,
            filters: [
                new FilterConditionFormData('domain', FilterOperator::CONTAINS, 'example'),
                new FilterConditionFormData('active', FilterOperator::IS_TRUE),
                new FilterConditionFormData(),
            ],
            search: 'foo',
        );

        $dehydrated = $component->dehydrateFormData($data);
        $this->assertSame(['field' => 'domain', 'operator' => 'contains', 'value' => 'example'], $dehydrated['filters'][0]);

        $this->assertEquals($data, $component->hydrateFormData($dehydrated));
    }

    public function testHydrateFormDataIgnoresGarbage(): void
    {
        $hydrated = (new FilterForm())->hydrateFormData([
            'direction' => 'sideways',
            'page' => 'two',
            'filters' => ['not an array', ['operator' => 'nope', 'field' => 7]],
        ]);

        $this->assertEquals(new FilterFormData(filters: [new FilterConditionFormData()]), $hydrated);
    }

    public function testHydrateFieldRegistryThrowsWhenPayloadIsNotARegistry(): void
    {
        $component = new FilterForm();

        $this->expectException(\UnexpectedValueException::class);

        $component->hydrateFieldRegistry(serialize(new \stdClass()));
    }
}
