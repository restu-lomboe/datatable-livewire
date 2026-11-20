<?php

namespace Developerawam\LivewireDatatable\Exports;

use Illuminate\Contracts\Support\Htmlable;

class HtmlToText
{
    /**
     * Convert HTML-rich values to readable plain text for exports.
     */
    public static function convert(mixed $value): mixed
    {
        if ($value instanceof Htmlable) {
            $value = $value->toHtml();
        }

        if (! is_string($value) || $value === '') {
            return $value;
        }

        $text = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\u{00A0}", ' ', $text);

        // Plain strings (including decoded entities) do not need tag processing.
        if (strip_tags($text) === $text) {
            return $text;
        }

        // Remove non-content blocks entirely before stripping the remaining tags.
        $text = preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', ' ', $text) ?? $text;

        // Preserve word separation where browsers would render line breaks or cell boundaries.
        $text = preg_replace('/<(br|hr)\s*\/?>/i', ' ', $text) ?? $text;
        $text = preg_replace(
            '/<\/(p|div|li|tr|td|th|dt|dd|h[1-6]|blockquote|table|ul|ol|section|article|header|footer|main|aside|nav|pre|figure|figcaption)>/i',
            ' ',
            $text
        ) ?? $text;

        $text = strip_tags($text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}
