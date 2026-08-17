<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Util;

class ColorBrightnessIdentifier
{
    /**
     * Determines if a hex color is dark or light.
     *
     * Uses the relative luminance formula from WCAG 2.0 to determine
     * if a color should be considered dark (requiring light text) or
     * light (requiring dark text).
     *
     * @param string|null $hexColor The hex color code (with or without #)
     *
     * @return bool True if the color is dark, false if light
     */
    public static function isColorDark(?string $hexColor): bool
    {
        // Default to dark (white text) if no color provided
        if (null === $hexColor || '' === $hexColor) {
            return true;
        }

        // Remove # if present
        $hexColor = ltrim($hexColor, '#');

        // Handle 3-character hex codes
        if (3 === \strlen($hexColor)) {
            $hexColor = $hexColor[0].$hexColor[0].$hexColor[1].$hexColor[1].$hexColor[2].$hexColor[2];
        }

        // Validate hex color
        if (6 !== \strlen($hexColor) || ! ctype_xdigit($hexColor)) {
            return true; // Default to dark if invalid
        }

        // Convert hex to RGB
        $r = (int) hexdec(substr($hexColor, 0, 2));
        $g = (int) hexdec(substr($hexColor, 2, 2));
        $b = (int) hexdec(substr($hexColor, 4, 2));

        // Calculate relative luminance using WCAG formula
        $rLuminance = self::calculateChannelLuminance($r);
        $gLuminance = self::calculateChannelLuminance($g);
        $bLuminance = self::calculateChannelLuminance($b);

        $luminance = 0.2126 * $rLuminance + 0.7152 * $gLuminance + 0.0722 * $bLuminance;

        // Threshold of 0.5 means colors with luminance < 0.5 are dark
        // Adjust threshold if needed (0.4-0.6 range is typical)
        return $luminance < 0.5;
    }

    /**
     * Calculate the luminance contribution of a single RGB channel.
     *
     * @param int $channel The RGB channel value (0-255)
     *
     * @return float The luminance contribution
     */
    private static function calculateChannelLuminance(int $channel): float
    {
        $c = $channel / 255.0;

        if ($c <= 0.03928) {
            return $c / 12.92;
        }

        return (($c + 0.055) / 1.055) ** 2.4;
    }
}
