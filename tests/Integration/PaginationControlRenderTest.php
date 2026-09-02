<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Integration;

use RamElectronic\DataTableBundle\Application\ReadModel\PaginatedResult;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

final class PaginationControlRenderTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testRendersPreviousAndNextControlsWithoutUnknownComponentError(): void
    {
        self::getContainer()->get('request_stack')->push(Request::create('/list'));

        $rendered = $this->renderTwigComponent('PaginationControl', [
            'pagination' => new PaginatedResult(items: [1, 2, 3], totalCount: 30, currentPage: 2, pageSize: 10),
            'route' => 'test_list',
        ]);

        $html = $rendered->toString();

        self::assertStringContainsString('Previous', $html);
        self::assertStringContainsString('Next', $html);
    }
}
