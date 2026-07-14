<?php

namespace App\Support;

class ChatMessageFormatter
{
    /**
     * Escape message text and turn http(s) URLs into safe clickable links.
     */
    public static function toHtml(?string $text): string
    {
        $text = (string) $text;
        if ($text === '') {
            return '';
        }

        $pattern = '/https?:\/\/[^\s<>"\']+/iu';
        $result = '';
        $offset = 0;

        if (preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as [$url, $pos]) {
                $result .= e(substr($text, $offset, $pos - $offset));

                $clean = rtrim($url, '.,);]\'"');
                $result .= '<a href="'.e($clean).'" target="_blank" rel="noopener noreferrer" class="underline break-all hover:opacity-80">'
                    .e($clean)
                    .'</a>';

                if ($clean !== $url) {
                    $result .= e(substr($url, strlen($clean)));
                }

                $offset = $pos + strlen($url);
            }
        }

        $result .= e(substr($text, $offset));

        return $result;
    }
}
