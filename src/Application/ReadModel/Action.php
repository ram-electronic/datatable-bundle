<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Application\ReadModel;

/**
 * Value object representing a table action (edit, delete, view, etc.).
 * Contains all metadata needed for action button rendering.
 */
readonly class Action
{
    public function __construct(
        public string $type,
        public string $route,
        public ?string $label = null,
        public ?string $confirmMessage = null,
        public bool $requiresCsrf = false,
    ) {
    }

    /**
     * Create an edit action.
     */
    public static function edit(string $route, ?string $label = null): self
    {
        return new self(
            type: 'edit',
            route: $route,
            label: $label ?? 'action.edit',
        );
    }

    /**
     * Create a delete action.
     */
    public static function delete(string $route, ?string $confirmMessage = null, ?string $label = null): self
    {
        return new self(
            type: 'delete',
            route: $route,
            label: $label ?? 'action.delete',
            confirmMessage: $confirmMessage,
            requiresCsrf: true,
        );
    }

    /**
     * Create a view/show action.
     */
    public static function view(string $route, ?string $label = null): self
    {
        return new self(
            type: 'view',
            route: $route,
            label: $label ?? 'action.view',
        );
    }

    /**
     * Create a PDF download action.
     */
    public static function pdf(string $route, ?string $label = null): self
    {
        return new self(
            type: 'pdf',
            route: $route,
            label: $label,
        );
    }

    /**
     * Create a custom action.
     */
    public static function custom(string $type, string $route, ?string $label = null, bool $requiresCsrf = false): self
    {
        return new self(
            type: $type,
            route: $route,
            label: $label,
            requiresCsrf: $requiresCsrf,
        );
    }
}
