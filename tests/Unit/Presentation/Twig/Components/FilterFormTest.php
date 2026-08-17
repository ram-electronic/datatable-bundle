<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Unit\Presentation\Twig\Components;

use PHPUnit\Framework\TestCase;
use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterFieldRegistry;
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

    public function testHydrateFieldRegistryThrowsWhenPayloadIsNotARegistry(): void
    {
        $component = new FilterForm();

        $this->expectException(\UnexpectedValueException::class);

        $component->hydrateFieldRegistry(serialize(new \stdClass()));
    }
}
