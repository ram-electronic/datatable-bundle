<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Renderer;

use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Util\ValueReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Renders any value as escaped text: scalars and Stringables as they are,
 * TranslatableInterface values (e.g. an enum that implements it) translated,
 * other enums by their value or name.
 *
 * Renderers return HTML (DataCell prints it raw), so everything taken from
 * the row is escaped here.
 */
#[AutoconfigureTag(CellRendererInterface::class, attributes: ['priority' => -100])]
readonly class DefaultRenderer implements CellRendererInterface
{
    public function __construct(
        private ValueReaderInterface $valueReader,
        private TranslatorInterface $translator,
    ) {
    }

    #[\Override]
    public function render(Column $column, mixed $row): string
    {
        $text = $this->text($this->valueReader->read($row, $column->key));

        return htmlspecialchars($text, \ENT_QUOTES, 'UTF-8');
    }

    #[\Override]
    public function supports(Column $column): bool
    {
        return true;
    }

    private function text(mixed $value): string
    {
        return match (true) {
            null === $value => '',
            $value instanceof TranslatableInterface => $value->trans($this->translator),
            $value instanceof \BackedEnum => (string) $value->value,
            $value instanceof \UnitEnum => $value->name,
            \is_scalar($value), $value instanceof \Stringable => (string) $value,
            default => '',
        };
    }
}
