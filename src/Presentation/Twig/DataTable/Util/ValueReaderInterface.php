<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Util;

interface ValueReaderInterface
{
    public function read(mixed $row, string $key): mixed;
}
