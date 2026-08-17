<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components;

use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterFieldRegistry;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterOperator;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/**
 * Live Component for dynamic filter field handling.
 * Updates available operators based on selected field type.
 * Field definitions are passed via props for reusability.
 */
#[AsLiveComponent]
final class FilterCondition
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $selectedField = '';

    #[LiveProp(writable: true)]
    public string $selectedOperator = '';

    #[LiveProp(writable: true)]
    public string $value = '';

    #[LiveProp]
    public string $fieldName = 'filter';

    #[LiveProp(hydrateWith: 'hydrateFieldRegistry', dehydrateWith: 'dehydrateFieldRegistry')]
    public FilterFieldRegistry $fieldRegistry;

    /**
     * Custom hydration for field registry to handle serialization.
     *
     * Uses PHP's native serialization so the registry's full state (its
     * $fields array) round-trips regardless of the concrete subclass's
     * constructor signature - unserialize() reconstructs the object without
     * invoking the constructor.
     */
    public function hydrateFieldRegistry(string $data): FilterFieldRegistry
    {
        $registry = unserialize($data, ['allowed_classes' => true]);

        if (! $registry instanceof FilterFieldRegistry) {
            throw new \UnexpectedValueException('Failed to unserialize a FilterFieldRegistry instance.');
        }

        return $registry;
    }

    /**
     * Custom dehydration for field registry to handle serialization.
     */
    public function dehydrateFieldRegistry(FilterFieldRegistry $registry): string
    {
        return serialize($registry);
    }

    /**
     * Get available operators for the currently selected field.
     *
     * @return array<FilterOperator>
     */
    public function getAvailableOperators(): array
    {
        if ('' === $this->selectedField) {
            return [];
        }

        $fieldDef = $this->fieldRegistry->getField($this->selectedField);
        if (! $fieldDef instanceof Column) {
            return [];
        }

        return $fieldDef->getAvailableOperators();
    }

    /**
     * Get all field definitions for the dropdown.
     *
     * @return array<Column>
     */
    public function getFieldChoices(): array
    {
        return $this->fieldRegistry->getFields();
    }

    /**
     * Get the full field name with index for form submission.
     */
    public function getFieldInputName(string $subfield): string
    {
        return \sprintf('%s[%s]', $this->fieldName, $subfield);
    }

    /**
     * Check if the currently selected operator is a unary operator.
     */
    public function isUnaryOperator(): bool
    {
        if ('' === $this->selectedOperator) {
            return false;
        }

        $operator = FilterOperator::tryFrom($this->selectedOperator);
        if (! $operator instanceof FilterOperator) {
            return false;
        }

        return $operator->isUnaryOperator();
    }
}
