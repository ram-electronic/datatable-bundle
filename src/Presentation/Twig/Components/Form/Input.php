<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components\Form;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('DataTable:Form:Input', template: '@DataTable/components/Form/Input.html.twig')]
final class Input
{
    public ?string $type = null;
    public string $name = '';
    public string $value = '';
    public ?string $placeholder = null;
    public ?string $data_model = null;
    public bool $required = false;
    public bool $disabled = false;
}
