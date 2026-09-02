<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components\Button;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('DataTable:Button:Button', template: '@DataTable/components/Button/Button.html.twig')]
final class Button
{
    public string $variant = 'default';
    public string $size = 'default';
    public ?string $type = null;
}
