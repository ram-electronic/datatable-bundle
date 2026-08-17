<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Unit\Presentation\Twig\DataTable\Renderer;

use PHPUnit\Framework\TestCase;
use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Presentation\Service\PercentageFormatter;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Renderer\PercentageCellRenderer;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Util\ValueReaderInterface;
use Symfony\Component\Translation\TranslatableMessage;

class PercentageCellRendererTest extends TestCase
{
    private function createRenderer(mixed $value): PercentageCellRenderer
    {
        $valueReader = $this->createMock(ValueReaderInterface::class);
        $valueReader->method('read')->willReturn($value);

        return new PercentageCellRenderer($valueReader, new PercentageFormatter());
    }

    private function rateColumn(): Column
    {
        return new Column('rate', DataType::PERCENTAGE, new TranslatableMessage('label.rate'));
    }

    public function testSupportsOnlyPercentageColumns(): void
    {
        $renderer = $this->createRenderer(0.5);

        $this->assertTrue($renderer->supports($this->rateColumn()));
        $this->assertFalse($renderer->supports(new Column('name', DataType::STRING, new TranslatableMessage('label.name'))));
    }

    public function testRendersFloatValue(): void
    {
        $rendered = $this->createRenderer(0.19)->render($this->rateColumn(), row: []);

        $this->assertSame('19,00%', $rendered);
    }

    public function testRendersIntegerValueInsteadOfFallingBackToZero(): void
    {
        $rendered = $this->createRenderer(1)->render($this->rateColumn(), row: []);

        $this->assertSame('100,00%', $rendered);
    }

    public function testRendersStringValue(): void
    {
        $rendered = $this->createRenderer('0.5')->render($this->rateColumn(), row: []);

        $this->assertSame('50,00%', $rendered);
    }

    public function testFallsBackToZeroForUnsupportedType(): void
    {
        $rendered = $this->createRenderer(['unexpected'])->render($this->rateColumn(), row: []);

        $this->assertSame('0,00%', $rendered);
    }
}
