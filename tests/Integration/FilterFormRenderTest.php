<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Integration;

use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterFieldRegistry;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterOperator;
use RamElectronic\DataTableBundle\Application\ReadModel\SortDirection;
use RamElectronic\DataTableBundle\Presentation\Dto\FilterConditionFormData;
use RamElectronic\DataTableBundle\Presentation\Dto\FilterFormData;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class FilterFormRenderTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testRendersWithoutUnknownComponentError(): void
    {
        $registry = new readonly class([
            new Column('name', DataType::STRING, new TranslatableMessage('label.name')),
        ]) extends FilterFieldRegistry {};

        $rendered = $this->renderTwigComponent('FilterForm', [
            'initialFormData' => new FilterFormData(),
            'actionUrl' => '/list',
            'fieldRegistry' => $registry,
        ]);

        $html = $rendered->toString();

        self::assertStringContainsString('<form', $html);
        self::assertStringContainsString('Apply filters', $html);
        self::assertStringContainsString('Add filter', $html);
    }

    /**
     * Without hydrateWith/dehydrateWith on FilterForm::$initialFormData this
     * failed with "Unable to dehydrate value of type array for property
     * filters" unless the app had a phpdoc type extractor installed (this
     * bundle's dev dependencies have none).
     */
    public function testRendersWithFilterConditionsThroughTheFilterTheme(): void
    {
        $registry = new readonly class([
            new Column('name', DataType::STRING, new TranslatableMessage('label.name')),
            new Column('active', DataType::BOOLEAN, new TranslatableMessage('label.active')),
        ]) extends FilterFieldRegistry {};

        $rendered = $this->renderTwigComponent('FilterForm', [
            'initialFormData' => new FilterFormData(
                sort: 'name',
                direction: SortDirection::DESC,
                filters: [new FilterConditionFormData('name', FilterOperator::CONTAINS, 'ada')],
                search: 'x',
            ),
            'actionUrl' => '/list',
            'fieldRegistry' => $registry,
        ]);

        $html = $rendered->toString();

        // The filter theme (prepended by the bundle) renders the row with the FilterCondition live component.
        self::assertStringContainsString('data-live-name-value="FilterCondition"', $html);
        self::assertStringContainsString('<option value="contains" selected>contains</option>', $html);
        self::assertStringContainsString('value="ada"', $html);
    }
}
