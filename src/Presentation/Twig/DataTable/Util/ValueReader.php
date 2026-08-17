<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Util;

class ValueReader implements ValueReaderInterface
{
    #[\Override]
    public function read(mixed $row, string $key): mixed
    {
        // Array / ArrayAccess
        if (\is_array($row) || $row instanceof \ArrayAccess) {
            return $row[$key] ?? null;
        }

        if (! \is_object($row)) {
            return null;
        }

        // Getter (getX / isX / hasX)
        return $row->$key ?? $this->readFromGetter($row, $key);
    }

    private function readFromGetter(object $row, string $key): mixed
    {
        $uc = ucfirst($key);

        foreach (["get$uc", "is$uc", "has$uc"] as $method) {
            if (method_exists($row, $method)) {
                return $row->$method();
            }
        }

        return null;
    }
}
