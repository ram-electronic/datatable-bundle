<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Application\ReadModel;

/**
 * Registry for managing filterable field definitions.
 * Provides a single source of truth for field metadata.
 */
abstract readonly class FilterFieldRegistry
{
    /** @var array<string> */
    private array $keys;

    /**
     * @param array<Column> $fields
     */
    public function __construct(
        private array $fields,
    ) {
        // Cache keys array to avoid repeated iterations
        $this->keys = array_map(fn (Column $field): string => $field->key, $this->fields);
    }

    /**
     * Get all field definitions.
     *
     * @return array<Column>
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Get field definition by key.
     */
    public function getField(string $key): ?Column
    {
        foreach ($this->fields as $field) {
            if ($field->key === $key) {
                return $field;
            }
        }

        return null;
    }

    /**
     * Get field choices.
     *
     * @return array<string, string> Label => field key
     */
    public function getFieldChoices(): array
    {
        $choices = [];
        foreach ($this->fields as $field) {
            $choices[$field->label->getMessage()] = $field->key;
        }

        return $choices;
    }

    /**
     * Get all keys.
     *
     * @return array<string>
     */
    public function getKeys(): array
    {
        return $this->keys;
    }
}
