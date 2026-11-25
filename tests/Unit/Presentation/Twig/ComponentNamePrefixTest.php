<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Unit\Presentation\Twig;

use PHPUnit\Framework\TestCase;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * A bundle component without the DataTable: prefix replaces any same-named
 * component of the consuming app (for example the app's own Table:Head from
 * the Symfony UX Toolkit), so every component the bundle registers must be
 * prefixed and point at an explicit @DataTable template.
 */
final class ComponentNamePrefixTest extends TestCase
{
    /**
     * The bundle's documented public entry points keep their bare names; renaming
     * them would break every consumer template.
     */
    private const PUBLIC_UNPREFIXED = [
        'RamElectronic\\DataTableBundle\\Presentation\\Twig\\Components\\PaginationControl',
    ];

    public function testEveryTwigComponentIsPrefixedAndHasABundleTemplate(): void
    {
        $srcDir = \dirname(__DIR__, 4).'/src';
        $componentsDir = $srcDir.'/Presentation/Twig/Components';
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($componentsDir, \FilesystemIterator::SKIP_DOTS));

        $checked = 0;
        foreach ($files as $file) {
            if ('php' !== $file->getExtension()) {
                continue;
            }

            $class = 'RamElectronic\\DataTableBundle\\'.str_replace(['/', '.php'], ['\\', ''], substr($file->getPathname(), \strlen($srcDir) + 1));
            if (!class_exists($class) || \in_array($class, self::PUBLIC_UNPREFIXED, true)) {
                continue;
            }

            foreach ((new \ReflectionClass($class))->getAttributes(AsTwigComponent::class) as $attribute) {
                // Read the raw arguments: AsTwigComponent's accessors differ between UX 2 and UX 3.
                $arguments = $attribute->getArguments();
                $name = $arguments['name'] ?? $arguments[0] ?? null;
                $template = $arguments['template'] ?? $arguments[1] ?? null;
                self::assertIsString($name, $class);
                self::assertStringStartsWith('DataTable:', $name, $class);
                self::assertIsString($template, $class);
                self::assertStringStartsWith('@DataTable/', $template, $class);
                ++$checked;
            }
        }

        self::assertGreaterThan(0, $checked);
    }
}
