<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Dto;

use RamElectronic\DataTableBundle\Application\ReadModel\FilterCondition;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterOperator;
use Symfony\Component\Validator\Constraints as Assert;

class FilterConditionFormData
{
    public function __construct(
        #[Assert\Length(max: 100)]
        public ?string $field = null,
        public ?FilterOperator $operator = null,
        #[Assert\Length(max: 255)]
        public ?string $value = null,
    ) {
    }

    /**
     * Convert form data to domain value object.
     */
    public function toDomain(): FilterCondition
    {
        if (null === $this->field || ! $this->operator instanceof FilterOperator) {
            throw new \InvalidArgumentException('Field must not be null and operator must be a valid FilterOperator instance');
        }

        // For unary operators, pass null as value (it won't be used)
        $value = $this->operator->isUnaryOperator() ? null : ($this->value ?? '');

        return new FilterCondition(
            field: $this->field,
            operator: $this->operator,
            value: $value,
        );
    }
}
