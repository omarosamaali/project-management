<?php

namespace App\Support;

use ArPHP\I18N\Arabic;
use Throwable;

/**
 * Shape Arabic text for DomPDF (which does not apply Arabic glyph joining).
 */
class CertificateArabic
{
    protected static ?Arabic $engine = null;

    public static function glyphs(?string $text): string
    {
        $text = trim((string) $text);
        if ($text === '') {
            return '';
        }

        // Latin-only / digits: leave as-is (names like "admin").
        if (! preg_match('/\p{Arabic}/u', $text)) {
            return $text;
        }

        try {
            return self::engine()->utf8Glyphs($text);
        } catch (Throwable) {
            return $text;
        }
    }

    protected static function engine(): Arabic
    {
        return self::$engine ??= new Arabic();
    }
}
