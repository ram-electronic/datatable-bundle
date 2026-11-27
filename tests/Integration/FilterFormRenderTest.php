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

    public function testSearchLabelIsOnlyForScreenReaders(): void
    {
        $html = $this->renderFilterForm();

        self::assertMatchesRegularExpression('/<label[^>]*class="sr-only"[^>]*>Search<\/label>/', $html);
        self::assertStringContainsString('placeholder="Search"', $html);
    }

    public function testCardIsTheOnlyPaddingAroundTheForm(): void
    {
        $html = $this->renderFilterForm();

        // The card brings p-4; a second padded wrapper inside it doubled the space.
        self::assertSame(1, preg_match_all('/class="[^"]*\\bp-4\\b/', $html));
    }

    public function testAddFilterButtonIsTheSmallBundleButton(): void
    {
        $html = $this->renderFilterForm();

        self::assertMatchesRegularExpression('/<button[^>]*class="[^"]*\bh-8\b[^"]*"[^>]*>\s*Add filter\s*<\/button>/', $html);
        // LiveCollectionType's action attributes survive the custom block.
        self::assertStringContainsString('data-live-action-param="addCollectionItem"', $html);
        self::assertStringContainsString('type="button"', $html);
    }

    private function renderFilterForm(): string
    {
        $registry = new readonly class([
            new Column('name', DataType::STRING, new TranslatableMessage('label.name')),
        ]) extends FilterFieldRegistry {};

        return $this->renderTwigComponent('FilterForm', [
            'initialFormData' => new FilterFormData(),
            'actionUrl' => '/list',
            'fieldRegistry' => $registry,
        ])->toString();
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
