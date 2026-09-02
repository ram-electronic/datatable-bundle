<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Integration;

use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterFieldRegistry;
use RamElectronic\DataTableBundle\Presentation\Dto\FilterFormData;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class FilterFormRenderTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testRendersWithoutUnknownComponentError(): void
    {
        $registry = new readonly class([
            new Column('name', DataType::STRING, new TranslatableMessage('label.name')),
        ]) extends FilterFieldRegistry {};

        $rendered = $this->renderTwigComponent('FilterForm', [
            'initialFormData' => new FilterFormData(),
            'actionUrl' => '/list',
            'fieldRegistry' => $registry,
        ]);

        $html = $rendered->toString();

        self::assertStringContainsString('<form', $html);
        self::assertStringContainsString('action.apply_filters', $html);
    }
}
