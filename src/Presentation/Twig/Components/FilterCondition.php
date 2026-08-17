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
     * PHP disallows serialize() on anonymous classes outright, so the whole
     * registry can't be serialized directly. Instead, persist the concrete
     * class name alongside its $fields array and reconstruct via the
     * constructor, which preserves state without depending on serialize()
     * support for the subclass.
     */
    public function hydrateFieldRegistry(string $data): FilterFieldRegistry
    {
        $payload = unserialize($data, ['allowed_classes' => true]);

        if (
            ! \is_array($payload)
            || ! isset($payload['class'], $payload['fields'])
            || ! \is_string($payload['class'])
            || ! \is_array($payload['fields'])
            || ! is_a($payload['class'], FilterFieldRegistry::class, true)
        ) {
            throw new \UnexpectedValueException('Failed to unserialize a FilterFieldRegistry instance.');
        }

        return new $payload['class']($payload['fields']);
    }

    /**
     * Custom dehydration for field registry to handle serialization.
     */
    public function dehydrateFieldRegistry(FilterFieldRegistry $registry): string
    {
        return serialize([
            'class' => $registry::class,
            'fields' => $registry->getFields(),
        ]);
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
