<?php

namespace Developerawam\LivewireDatatable\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

trait WithFormatters
{
    protected function formatSimpleValue($value, string $formatter, array $options = []): string|int|float|bool
    {
        switch ($formatter) {
            case 'date':
                return $this->formatDate($value, $options['format'] ?? 'Y-m-d');
            case 'datetime':
                return $this->formatDate($value, $options['format'] ?? 'Y-m-d H:i:s');
            case 'time':
                return $this->formatDate($value, $options['format'] ?? 'H:i:s');
            case 'number':
                return number_format(
                    $value,
                    $options['decimals'] ?? 0,
                    $options['decimal_point'] ?? '.',
                    $options['thousand_sep'] ?? ','
                );
            case 'currency':
                return ($options['symbol'] ?? 'Rp ').number_format(
                    $value,
                    $options['decimals'] ?? 2,
                    $options['decimal_point'] ?? '.',
                    $options['thousand_sep'] ?? ','
                );
            case 'boolean':
                return $value ? ($options['true'] ?? 'Yes') : ($options['false'] ?? 'No');
            case 'uppercase':
                return Str::upper($value);
            case 'lowercase':
                return Str::lower($value);
            default:
                return $value;
        }
    }

    protected function formatComplexValue($value, array $formatter, $item = null, $key = null): string|int|float|bool
    {
        $type = $formatter['type'] ?? null;
        $options = $formatter['options'] ?? [];

        switch ($type) {
            case 'limit':
                return Str::limit($value, $options['length'] ?? 50, $options['end'] ?? '...');
            case 'words':
                return Str::words($value, $options['words'] ?? 10, $options['end'] ?? '...');
            case 'markdown':
                return Str::markdown($value);
            case 'money':
                return ($options['symbol'] ?? 'Rp ').number_format(
                    $value,
                    $options['decimals'] ?? 2,
                    $options['decimal_point'] ?? '.',
                    $options['thousand_sep'] ?? ','
                );
            case 'date':
                return $this->formatDate($value, $options['format'] ?? 'Y-m-d');
            case 'link':
                return $this->formatLink($value, $options, $item, $key);
            default:
                return $value;
        }
    }

    /**
     * Build an HTML anchor for the "link" formatter.
     *
     * @param  array  $options  Supports:
     *                          - route: named route to link to (e.g. "users.show")
     *                          - params: route parameters as column names, either
     *                          numeric ["id"] or associative ["user" => "id"]
     *                          - url: custom/static URL, optionally with {column}
     *                          placeholders (e.g. "/users/{id}/edit")
     *                          - text: link label (defaults to the cell value)
     *                          - target: "_self" (default) or "_blank"
     *                          - class: CSS classes for the anchor
     *                          - title: title attribute
     */
    protected function formatLink($value, array $options, $item = null, $key = null): string
    {
        $href = $this->resolveLinkHref($value, $options, $item, $key);

        if (! $href) {
            return (string) $value;
        }

        $attributes = 'href="'.e($href).'"';

        if (! empty($options['target']) && $options['target'] !== '_self') {
            $attributes .= ' target="'.e($options['target']).'"';
        }

        if (! empty($options['class'])) {
            $attributes .= ' class="'.e($options['class']).'"';
        }

        if (! empty($options['title'])) {
            $attributes .= ' title="'.e($options['title']).'"';
        }

        $text = $options['text'] ?? $value;

        return '<a '.$attributes.'>'.e($text).'</a>';
    }

    protected function resolveLinkHref($value, array $options, $item = null, $key = null): ?string
    {
        if (! empty($options['route'])) {
            $routeParams = [];

            foreach ($options['params'] ?? [] as $routeParam => $columnName) {
                if (is_int($routeParam)) {
                    $routeParam = $columnName;
                }

                $routeParams[$routeParam] = $this->resolveLinkParam($value, $columnName, $item, $key);
            }

            try {
                return route($options['route'], $routeParams);
            } catch (\Exception $e) {
                return null;
            }
        }

        if (! empty($options['url'])) {
            $url = $options['url'];

            if (preg_match_all('/\{([\w.]+)\}/', $url, $matches)) {
                foreach ($matches[1] as $placeholder) {
                    $replacement = $this->resolveLinkParam($value, $placeholder, $item, $key);
                    $url = str_replace('{'.$placeholder.'}', (string) $replacement, $url);
                }
            }

            return $url;
        }

        return null;
    }

    protected function resolveLinkParam($value, string $columnName, $item = null, $key = null)
    {
        if ($item && ! is_null($resolved = data_get($item, $columnName)) && $resolved !== '') {
            return $resolved;
        }

        if ($columnName === $key) {
            return $value;
        }

        return $columnName;
    }

    protected function formatDate($value, string $format): string
    {
        if (! $value) {
            return '';
        }

        try {
            return Carbon::parse($value)->format($format);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function formatValue($key, $value, $item = null): string|int|float|bool
    {
        // Handle null values by returning a dash
        if ($value === null || $value === '') {
            return '-';
        }

        if (! isset($this->formatters[$key])) {
            return e($value);
        }

        $formatter = $this->formatters[$key];

        // Handle complex formatter array with type and options
        if (is_array($formatter)) {
            $result = $this->formatComplexValue($value, $formatter, $item, $key);
            $type = $formatter['type'] ?? null;
            // Only link & markdown intentionally return HTML; others must be escaped
            if (in_array($type, ['link', 'markdown'], true)) {
                return $result;
            }

            return e($result);
        }

        // Handle simple string formatter
        if (is_string($formatter)) {
            $result = $this->formatSimpleValue($value, $formatter, $this->getFormatterOptions($key));

            return e($result);
        }

        return e($value);
    }

    protected function getFormatterOptions($key): array
    {
        return $this->formatterOptions[$key] ?? [];
    }
}
