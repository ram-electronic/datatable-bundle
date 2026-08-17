<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components\Table;

use RamElectronic\DataTableBundle\Application\ReadModel\Sort;
use RamElectronic\DataTableBundle\Application\ReadModel\SortDirection;
use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Sortable table header component.
 * Creates a clickable header that preserves filter state while toggling sort direction.
 * Supports Symfony form field naming conventions.
 */
#[AsTwigComponent('Table:SortableHeader')]
final class SortableHeader
{
    public string $sortKey;
    public string $label;
    public Sort $currentSort;
    public string $route;
    public FormView $form;

    /** @var array<string, mixed> */
    public array $routeParams = [];

    /**
     * Get the next sort direction (toggle between ASC and DESC).
     */
    public function getNextDirection(): string
    {
        if ($this->sortKey === $this->currentSort->key) {
            return $this->currentSort->direction->toggle()->value;
        }

        return SortDirection::ASC->value;
    }

    /**
     * Check if this header is currently being sorted.
     */
    public function isActive(): bool
    {
        return $this->sortKey === $this->currentSort->key;
    }

    /**
     * Get the sort indicator (arrow).
     */
    public function getSortIndicator(): string
    {
        if (! $this->isActive()) {
            return '';
        }

        return SortDirection::ASC === $this->currentSort->direction ? '↑' : '↓';
    }

    /**
     * Get the form name (prefix for field names).
     */
    public function getFormName(): string
    {
        $name = $this->form->vars['name'];
        if (! \is_string($name)) {
            throw new \RuntimeException('Form name must be a string');
        }

        return $name;
    }

    /**
     * Build query parameters with proper form field naming.
     *
     * @return array<string, mixed>
     */
    public function getQueryParams(): array
    {
        $formName = $this->getFormName();
        $params = [];

        // Copy all current form data
        foreach ($this->form->children as $fieldName => $field) {
            $data = $field->vars['data'] ?? null;
            if (null !== $data && '' !== $data) {
                $params[\sprintf('%s[%s]', $formName, $fieldName)] = $data;
            }
        }

        // Override sort parameters
        $params[\sprintf('%s[sort]', $formName)] = $this->sortKey;
        $params[\sprintf('%s[direction]', $formName)] = $this->getNextDirection();
        $params[\sprintf('%s[page]', $formName)] = 1; // Reset to first page when sorting

        return $params;
    }
}
