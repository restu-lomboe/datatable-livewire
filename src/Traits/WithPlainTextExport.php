<?php

namespace Developerawam\LivewireDatatable\Traits;

use Developerawam\LivewireDatatable\Exports\HtmlToText;

trait WithPlainTextExport
{
    /**
     * Convert HTML-rich values to readable plain text for exports.
     */
    protected function toPlainText(mixed $value): mixed
    {
        return HtmlToText::convert($value);
    }
}
