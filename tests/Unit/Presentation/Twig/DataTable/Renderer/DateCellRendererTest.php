<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Unit\Presentation\Twig\DataTable\Renderer;

use PHPUnit\Framework\TestCase;
use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Renderer\DateCellRenderer;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Util\ValueReaderInterface;
use Symfony\Component\Translation\TranslatableMessage;

class DateCellRendererTest extends TestCase
{
    private function createRenderer(mixed $value): DateCellRenderer
    {
        $valueReader = $this->createMock(ValueReaderInterface::class);
        $valueReader->method('read')->willReturn($value);

        return new DateCellRenderer($valueReader);
    }

    private function createdAtColumn(): Column
    {
        return new Column('createdAt', DataType::DATE, new TranslatableMessage('label.created_at'));
    }

    public function testSupportsOnlyDateColumns(): void
    {
        $renderer = $this->createRenderer(new \DateTimeImmutable('2024-01-01'));

        $this->assertTrue($renderer->supports($this->createdAtColumn()));
        $this->assertFalse($renderer->supports(new Column('name', DataType::STRING, new TranslatableMessage('label.name'))));
    }

    public function testRendersDateTimeInterfaceValue(): void
    {
        $rendered = $this->createRenderer(new \DateTimeImmutable('2024-03-15'))->render($this->createdAtColumn(), row: []);

        $this->assertSame('15.03.2024', $rendered);
    }

    public function testRendersStringBackedDateValue(): void
    {
        $rendered = $this->createRenderer('2024-01-01')->render($this->createdAtColumn(), row: []);

        $this->assertSame('01.01.2024', $rendered);
    }

    public function testRendersDashForUnparsableString(): void
    {
        $rendered = $this->createRenderer('not-a-date')->render($this->createdAtColumn(), row: []);

        $this->assertSame('-', $rendered);
    }

    public function testRendersDashForNullValue(): void
    {
        $rendered = $this->createRenderer(null)->render($this->createdAtColumn(), row: []);

        $this->assertSame('-', $rendered);
    }
}
