<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Unit\Presentation\Twig\DataTable\Util;

use PHPUnit\Framework\TestCase;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Util\ColorBrightnessIdentifier;

class ColorBrightnessIdentifierTest extends TestCase
{
    public function testIsColorDarkWithDarkColors(): void
    {
        // Black and very dark colors
        $this->assertTrue(ColorBrightnessIdentifier::isColorDark('#000000'));
        $this->assertTrue(ColorBrightnessIdentifier::isColorDark('#111111'));
        $this->assertTrue(ColorBrightnessIdentifier::isColorDark('#333333'));

        // Dark blue
        $this->assertTrue(ColorBrightnessIdentifier::isColorDark('#000080'));

        // Dark red
        $this->assertTrue(ColorBrightnessIdentifier::isColorDark('#800000'));

        // Dark green
        $this->assertTrue(ColorBrightnessIdentifier::isColorDark('#006400'));
    }

    public function testIsColorDarkWithLightColors(): void
    {
        // White and very light colors
        $this->assertFalse(ColorBrightnessIdentifier::isColorDark('#FFFFFF'));
        $this->assertFalse(ColorBrightnessIdentifier::isColorDark('#EEEEEE'));
        $this->assertFalse(ColorBrightnessIdentifier::isColorDark('#CCCCCC'));

        // Yellow (light)
        $this->assertFalse(ColorBrightnessIdentifier::isColorDark('#FFFF00'));

        // Cyan (light)
        $this->assertFalse(ColorBrightnessIdentifier::isColorDark('#00FFFF'));

        // Light green
        $this->assertFalse(ColorBrightnessIdentifier::isColorDark('#90EE90'));
    }

    public function testIsColorDarkWithoutHashSymbol(): void
    {
        $this->assertTrue(ColorBrightnessIdentifier::isColorDark('000000'));
        $this->assertFalse(ColorBrightnessIdentifier::isColorDark('FFFFFF'));
    }

    public function testIsColorDarkWithShortHexCode(): void
    {
        $this->assertTrue(ColorBrightnessIdentifier::isColorDark('#000'));
        $this->assertFalse(ColorBrightnessIdentifier::isColorDark('#FFF'));
        $this->assertFalse(ColorBrightnessIdentifier::isColorDark('#CCC'));
    }

    public function testIsColorDarkWithNullOrEmpty(): void
    {
        // Should default to dark (true) when no color provided
        $this->assertTrue(ColorBrightnessIdentifier::isColorDark(null));
        $this->assertTrue(ColorBrightnessIdentifier::isColorDark(''));
    }

    public function testIsColorDarkWithInvalidHex(): void
    {
        // Should default to dark (true) when invalid hex provided
        $this->assertTrue(ColorBrightnessIdentifier::isColorDark('#GGGGGG'));
        $this->assertTrue(ColorBrightnessIdentifier::isColorDark('invalid'));
        $this->assertTrue(ColorBrightnessIdentifier::isColorDark('#12345'));
    }
}
