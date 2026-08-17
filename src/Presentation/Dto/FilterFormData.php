<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Dto;

use RamElectronic\DataTableBundle\Application\ReadModel\SortDirection;
use Symfony\Component\Validator\Constraints as Assert;

class FilterFormData
{
    public function __construct(
        #[Assert\Length(max: 100)]
        public ?string $sort = null,
        public ?SortDirection $direction = null,
        #[Assert\Positive]
        public ?int $page = null,
        /** @var array<FilterConditionFormData> */
        #[Assert\Valid]
        public array $filters = [],
        #[Assert\Length(max: 255)]
        public ?string $search = null,
    ) {
    }
}
