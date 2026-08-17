<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Service;

use RamElectronic\DataTableBundle\Application\ReadModel\FilterCollection;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterOperator;
use RamElectronic\DataTableBundle\Presentation\Dto\FilterFormData;

/**
 * Service for building FilterCollection from form data.
 *
 * Handles validation and conversion of filter form data to domain objects.
 * Centralizes filter validation logic for reuse across controllers.
 */
readonly class FilterCollectionBuilder
{
    /**
     * Build filter collection from form data.
     *
     * Validates each filter condition and converts valid ones to domain objects.
     * Skips invalid or incomplete filter conditions.
     */
    public function build(FilterFormData $formData): FilterCollection
    {
        $conditions = [];

        foreach ($formData->filters as $filterData) {
            // Skip if required fields are missing
            if (null === $filterData->field) {
                continue;
            }
            if (! $filterData->operator instanceof FilterOperator) {
                continue;
            }
            // For binary operators, value must be provided
            if (! $filterData->operator->isUnaryOperator() && (null === $filterData->value || '' === $filterData->value)) {
                continue;
            }

            try {
                $conditions[] = $filterData->toDomain();
            } catch (\InvalidArgumentException) {
                // Skip invalid conditions
                continue;
            }
        }

        return new FilterCollection($conditions);
    }
}
