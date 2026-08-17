<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Unit\Presentation\Form;

use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterFieldRegistry;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterOperator;
use RamElectronic\DataTableBundle\Presentation\Dto\FilterConditionFormData;
use RamElectronic\DataTableBundle\Presentation\Dto\FilterFormData;
use RamElectronic\DataTableBundle\Presentation\Form\FilterConditionType;
use RamElectronic\DataTableBundle\Presentation\Form\FilterType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

/**
 * Exercises FilterType/FilterConditionType's actual Symfony Form binding - the piece
 * DashboardController::mtaSts() relies on to turn a submitted GET query string into a
 * FilterFormData with real FilterOperator enum instances - without a full kernel/Twig, so it
 * runs independent of any consuming app's container or network access.
 */
class FilterTypeTest extends TypeTestCase
{
    #[\Override]
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([
                new FilterType(),
                new FilterConditionType(),
                new LiveCollectionType(),
            ], []),
        ];
    }

    public function testSubmitBindsFilterConditionsWithRealOperatorEnum(): void
    {
        $fieldRegistry = new readonly class([new Column('domain', DataType::STRING, new TranslatableMessage('label.domain'))]) extends FilterFieldRegistry {};

        $form = $this->factory->create(FilterType::class, null, ['field_registry' => $fieldRegistry]);

        $form->submit([
            'search' => 'example',
            'filters' => [
                ['field' => 'domain', 'operator' => FilterOperator::CONTAINS->value, 'value' => 'example.com'],
            ],
        ]);

        $this->assertTrue($form->isSynchronized());

        $data = $form->getData();
        $this->assertInstanceOf(FilterFormData::class, $data);
        $this->assertSame('example', $data->search);
        $this->assertCount(1, $data->filters);

        $condition = $data->filters[0];
        $this->assertInstanceOf(FilterConditionFormData::class, $condition);
        $this->assertSame('domain', $condition->field);
        $this->assertSame(FilterOperator::CONTAINS, $condition->operator);
        $this->assertSame('example.com', $condition->value);

        $domainCondition = $condition->toDomain();
        $this->assertSame('domain', $domainCondition->field);
        $this->assertSame(FilterOperator::CONTAINS, $domainCondition->operator);
        $this->assertSame('example.com', $domainCondition->value);
    }

    public function testSubmitWithIncompleteFilterRowLeavesOperatorNull(): void
    {
        $fieldRegistry = new readonly class([new Column('domain', DataType::STRING, new TranslatableMessage('label.domain'))]) extends FilterFieldRegistry {};

        $form = $this->factory->create(FilterType::class, null, ['field_registry' => $fieldRegistry]);

        // A user who picked a field but hasn't chosen an operator yet - this is exactly what
        // DashboardController::mtaSts() must handle without a fatal error.
        $form->submit([
            'filters' => [
                ['field' => 'domain', 'operator' => '', 'value' => ''],
            ],
        ]);

        $data = $form->getData();
        $this->assertInstanceOf(FilterFormData::class, $data);
        $this->assertCount(1, $data->filters);
        $this->assertNull($data->filters[0]->operator);
    }

    public function testSubmitWithNoFilterRowsYieldsEmptyFiltersArray(): void
    {
        $fieldRegistry = new readonly class([new Column('domain', DataType::STRING, new TranslatableMessage('label.domain'))]) extends FilterFieldRegistry {};

        $form = $this->factory->create(FilterType::class, null, ['field_registry' => $fieldRegistry]);

        $form->submit([]);

        $data = $form->getData();
        $this->assertInstanceOf(FilterFormData::class, $data);
        $this->assertSame([], $data->filters);
    }
}
