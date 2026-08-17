<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Unit\Presentation\Twig\DataTable\Renderer;

use PHPUnit\Framework\TestCase;
use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Renderer\DateTimeCellRenderer;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Util\ValueReaderInterface;
use Symfony\Component\Translation\TranslatableMessage;

class DateTimeCellRendererTest extends TestCase
{
    private function createRenderer(mixed $value): DateTimeCellRenderer
    {
        $valueReader = $this->createMock(ValueReaderInterface::class);
        $valueReader->method('read')->willReturn($value);

        return new DateTimeCellRenderer($valueReader);
    }

    private function column(): Column
    {
        return new Column('updatedAt', DataType::DATETIME, new TranslatableMessage('label.updated_at'));
    }

    public function testRendersDateTimeInterfaceValue(): void
    {
        $rendered = $this->createRenderer(new \DateTimeImmutable('2024-03-05 14:30:00'))->render($this->column(), row: []);

        $this->assertSame('05.03.2024 14:30', $rendered);
    }

    public function testRendersStringBackedDateTimeValue(): void
    {
        $rendered = $this->createRenderer('2024-03-05 14:30:00')->render($this->column(), row: []);

        $this->assertSame('05.03.2024 14:30', $rendered);
    }

    public function testRendersDashForUnparsableStringValue(): void
    {
        $rendered = $this->createRenderer('not a datetime')->render($this->column(), row: []);

        $this->assertSame('-', $rendered);
    }

    public function testRendersDashForNullValue(): void
    {
        $rendered = $this->createRenderer(null)->render($this->column(), row: []);

        $this->assertSame('-', $rendered);
    }
}
