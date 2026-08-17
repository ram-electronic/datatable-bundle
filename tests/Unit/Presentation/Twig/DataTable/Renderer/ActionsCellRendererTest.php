<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Unit\Presentation\Twig\DataTable\Renderer;

use PHPUnit\Framework\TestCase;
use RamElectronic\DataTableBundle\Application\ReadModel\Action;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Renderer\ActionsCellRenderer;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Util\ValueReaderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActionsCellRendererTest extends TestCase
{
    private function createRenderer(mixed $rowId): ActionsCellRenderer
    {
        $valueReader = $this->createMock(ValueReaderInterface::class);
        $valueReader->method('read')->willReturn($rowId);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturn('/delete');

        $csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        // Echo the requested token id back as the token value so tests can
        // assert exactly which id string the CSRF token was bound to.
        $csrfTokenManager->method('getToken')->willReturnCallback(
            static fn (string $tokenId): CsrfToken => new CsrfToken('csrf', $tokenId)
        );

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return new ActionsCellRenderer($valueReader, $urlGenerator, $csrfTokenManager, $translator);
    }

    public function testDeleteButtonUsesDistinctCsrfTokenForIntId(): void
    {
        $html = $this->createRenderer(42)->render([Action::delete('route_delete')], row: []);

        $this->assertStringContainsString('value="delete42"', $html);
    }

    public function testDeleteButtonUsesDistinctCsrfTokenForStringId(): void
    {
        $html = $this->createRenderer('abc-123')->render([Action::delete('route_delete')], row: []);

        $this->assertStringContainsString('value="deleteabc-123"', $html);
    }

    public function testDeleteButtonUsesDistinctCsrfTokenForStringableId(): void
    {
        $uuid = new class implements \Stringable {
            public function __toString(): string
            {
                return 'uuid-9f8e';
            }
        };

        $html = $this->createRenderer($uuid)->render([Action::delete('route_delete')], row: []);

        $this->assertStringContainsString('value="deleteuuid-9f8e"', $html);
    }

    public function testDeleteButtonThrowsForNullId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->createRenderer(null)->render([Action::delete('route_delete')], row: []);
    }

    public function testDeleteButtonThrowsForUnsupportedIdType(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->createRenderer(['not', 'a', 'valid', 'id'])->render([Action::delete('route_delete')], row: []);
    }

    public function testDistinctIdsProduceDistinctCsrfTokensRatherThanColliding(): void
    {
        $htmlForRowOne = $this->createRenderer(1)->render([Action::delete('route_delete')], row: []);
        $htmlForRowTwo = $this->createRenderer(2)->render([Action::delete('route_delete')], row: []);

        $this->assertStringContainsString('value="delete1"', $htmlForRowOne);
        $this->assertStringContainsString('value="delete2"', $htmlForRowTwo);
        $this->assertNotSame($htmlForRowOne, $htmlForRowTwo);
    }
}
