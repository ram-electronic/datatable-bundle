<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Unit\Presentation\Twig\DataTable\Renderer;

use PHPUnit\Framework\TestCase;
use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Renderer\BooleanCellRenderer;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Util\ValueReaderInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

class BooleanCellRendererTest extends TestCase
{
    private function createRenderer(mixed $value): BooleanCellRenderer
    {
        $valueReader = $this->createMock(ValueReaderInterface::class);
        $valueReader->method('read')->willReturn($value);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return new BooleanCellRenderer($valueReader, $translator);
    }

    private function activeColumn(): Column
    {
        return new Column('active', DataType::BOOLEAN, new TranslatableMessage('label.active'));
    }

    public function testSupportsOnlyBooleanColumns(): void
    {
        $renderer = $this->createRenderer(true);

        $this->assertTrue($renderer->supports($this->activeColumn()));
        $this->assertFalse($renderer->supports(new Column('name', DataType::STRING, new TranslatableMessage('label.name'))));
    }

    public function testRendersDistinctLabelForFalseValue(): void
    {
        $rendered = $this->createRenderer(false)->render($this->activeColumn(), row: []);

        $this->assertSame('boolean.false', $rendered);
        $this->assertNotSame('', $rendered);
    }

    public function testRendersDistinctLabelForTrueValue(): void
    {
        $rendered = $this->createRenderer(true)->render($this->activeColumn(), row: []);

        $this->assertSame('boolean.true', $rendered);
    }

    public function testRendersEmptyStringForNullValue(): void
    {
        $rendered = $this->createRenderer(null)->render($this->activeColumn(), row: []);

        $this->assertSame('', $rendered);
    }
}
