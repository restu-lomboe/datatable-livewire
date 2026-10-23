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
            case 'capitalize':
                return $this->formatCapitalize($value, $options);
            case 'replace':
                return $this->formatReplace($value, $options);
            case 'strip':
            case 'html':
            case 'plain':
                return strip_tags((string) $value, $options['allowed'] ?? '');
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
            case 'capitalize':
                return $this->formatCapitalize($value, $options);
            case 'replace':
                return $this->formatReplace($value, $options);
            case 'link':
                return $this->formatLink($value, $options, $item, $key);
            case 'strip':
            case 'html':
            case 'plain':
                return strip_tags((string) $value, $options['allowed'] ?? '');
            default:
                return $value;
        }
    }

    protected function formatCapitalize($value, array $options = []): string
    {
        $text = (string) $value;
        $mode = $options['mode'] ?? $options[0] ?? (! empty($options['words']) ? 'words' : 'first');
        $mode = is_string($mode) ? strtolower($mode) : 'first';

        if (in_array($mode, ['words', 'title', 'each'], true)) {
            return Str::title($text);
        }

        return Str::ucfirst($text);
    }

    protected function formatReplace($value, array $options = []): string
    {
        $search = $options['search'] ?? $options[0] ?? null;
        $replace = $options['replace'] ?? $options[1] ?? '';
        $text = (string) $value;

        if ($search === null || $search === '' || $search === []) {
            return $text;
        }

        if (! is_array($search)) {
            $search = (string) $search;
        }

        if (! is_array($replace)) {
            $replace = (string) $replace;
        }

        return str_replace($search, $replace, $text);
    }

    protected function formatChainResult($value, array $steps, $item = null, $key = null): string|int|float|bool
    {
        $result = $this->formatFormatterChain($value, $steps, $item, $key);

        if ($this->chainContainsHtml($steps)) {
            return $result;
        }

        return e($result);
    }

    protected function formatFormatterChain($value, array $steps, $item = null, $key = null): string|int|float|bool
    {
        $current = $value;

        foreach ($steps as $step) {
            if (is_array($step)) {
                $type = $step['type'] ?? null;

                if (! is_string($type) || $type === '') {
                    continue;
                }

                $stepOptions = isset($step['options'])
                    ? (is_array($step['options']) ? $step['options'] : [])
                    : array_diff_key($step, ['type' => true]);

                $options = $stepOptions !== [] ? $stepOptions : $this->getFormatterOptions($key);

                $current = $this->formatFormatterStep($current, $type, $options, $item, $key);

                continue;
            }

            $parsed = $this->parseFormatterStep((string) $step);
            $options = $parsed['args'] !== [] ? $parsed['args'] : $this->getFormatterOptions($key);

            $current = $this->formatFormatterStep($current, $parsed['name'], $options, $item, $key);
        }

        return $current;
    }

    protected function formatFormatterStep($value, string $name, array $options, $item = null, $key = null): string|int|float|bool
    {
        $name = strtolower(trim($name));

        if (in_array($name, ['limit', 'words', 'markdown', 'money', 'link'], true)) {
            return $this->formatComplexValue($value, ['type' => $name, 'options' => $options], $item, $key);
        }

        return $this->formatSimpleValue($value, $name, $options);
    }

    /**
     * Split "capitalize and replace(...)" / "uppercase|replace" / "trim->uppercase".
     *
     * @return array<int, string>
     */
    protected function splitFormatterChain(string $formatter): array
    {
        $parts = [];
        $buffer = '';
        $depth = 0;
        $quote = null;
        $length = strlen($formatter);

        for ($i = 0; $i < $length; $i++) {
            $char = $formatter[$i];

            if ($quote !== null) {
                $buffer .= $char;

                if ($char === '\\' && $i + 1 < $length) {
                    $buffer .= $formatter[++$i];
                } elseif ($char === $quote) {
                    $quote = null;
                }

                continue;
            }

            if ($char === '"' || $char === "'") {
                $quote = $char;
                $buffer .= $char;

                continue;
            }

            if ($char === '(') {
                $depth++;
                $buffer .= $char;

                continue;
            }

            if ($char === ')') {
                $depth = max(0, $depth - 1);
                $buffer .= $char;

                continue;
            }

            if ($depth === 0) {
                if ($char === '|') {
                    $parts[] = trim($buffer);
                    $buffer = '';

                    continue;
                }

                if ($char === '-' && ($formatter[$i + 1] ?? '') === '>') {
                    $parts[] = trim($buffer);
                    $buffer = '';
                    $i++;

                    continue;
                }

                if ($char === ' ' && preg_match('/^\s+(?:and|then)\s+/i', substr($formatter, $i), $match)) {
                    $parts[] = trim($buffer);
                    $buffer = '';
                    $i += strlen($match[0]) - 1;

                    continue;
                }
            }

            $buffer .= $char;
        }

        $parts[] = trim($buffer);

        return array_values(array_filter($parts, fn ($part) => $part !== ''));
    }

    /**
     * @return array{name: string, args: array<int, string>}
     */
    protected function parseFormatterStep(string $step): array
    {
        $step = trim($step);

        if (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)\s*(?:\((.*)\))?$/s', $step, $matches)) {
            return [
                'name' => strtolower($matches[1]),
                'args' => $this->parseFormatterArguments($matches[2] ?? ''),
            ];
        }

        return ['name' => strtolower($step), 'args' => []];
    }

    /**
     * @return array<int, string>
     */
    protected function parseFormatterArguments(string $arguments): array
    {
        $arguments = trim($arguments);

        if ($arguments === '') {
            return [];
        }

        $result = [];
        $buffer = '';
        $quote = null;
        $quoted = false;
        $length = strlen($arguments);

        for ($i = 0; $i < $length; $i++) {
            $char = $arguments[$i];

            if ($quote !== null) {
                if ($char === '\\' && $i + 1 < $length) {
                    $buffer .= $arguments[++$i];

                    continue;
                }

                if ($char === $quote) {
                    $quote = null;

                    continue;
                }

                $buffer .= $char;

                continue;
            }

            if ($char === '"' || $char === "'") {
                $quote = $char;
                $quoted = true;

                continue;
            }

            if ($char === ',') {
                $result[] = $quoted ? $buffer : trim($buffer);
                $buffer = '';
                $quoted = false;

                continue;
            }

            if (ctype_space($char)) {
                if ($buffer !== '') {
                    $buffer .= $char;
                }

                continue;
            }

            $buffer .= $char;
        }

        $result[] = $quoted ? $buffer : trim($buffer);

        return $result;
    }

    protected function chainContainsHtml(array $steps): bool
    {
        foreach ($steps as $step) {
            if (is_array($step)) {
                $type = strtolower((string) ($step['type'] ?? ''));

                if (in_array($type, ['link', 'markdown'], true)) {
                    return true;
                }

                continue;
            }

            $parsed = $this->parseFormatterStep((string) $step);

            if (in_array($parsed['name'], ['link', 'markdown'], true)) {
                return true;
            }
        }

        return false;
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
     *                          - text: literal link label (defaults to the cell value)
     *                          - text_formatter: formatter or chain applied to the cell value for the label only
     *                          - text_formats: alias for text_formatter
     *                          - target: "_self" (default) or "_blank"
     *                          - class: CSS classes for the anchor
     *                          - title: title attribute
     */
    protected function formatLink($value, array $options, $item = null, $key = null): string
    {
        $href = $this->resolveLinkHref($value, $options, $item, $key);

        if (! $href) {
            return e($value);
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

        $text = $options['text'] ?? $this->formatLinkText($value, $options, $item, $key);

        return '<a '.$attributes.'>'.e($text).'</a>';
    }

    /**
     * Format the link label without changing the value used to build the URL.
     */
    protected function formatLinkText($value, array $options, $item = null, $key = null): string
    {
        $formatter = $options['text_formatter'] ?? $options['text_formats'] ?? null;

        if (! is_string($formatter) && ! is_array($formatter)) {
            return (string) $value;
        }

        return (string) $this->formatFormatterChain(
            $value,
            $this->normalizeFormatterSteps($formatter),
            $item,
            $key
        );
    }

    /**
     * @return array<int, mixed>
     */
    protected function normalizeFormatterSteps(array|string $formatter): array
    {
        if (is_string($formatter)) {
            return $this->splitFormatterChain($formatter);
        }

        if (isset($formatter['formats']) && is_array($formatter['formats'])) {
            return $formatter['formats'];
        }

        if ($formatter !== [] && array_is_list($formatter)) {
            return $formatter;
        }

        return [$formatter];
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
        // The checkbox column has no underlying value; render the selection control instead.
        if ($this->isCheckboxFormatter($key) && $this->isCheckboxColumn($key)) {
            return $this->renderSelectionCheckbox($item, $key);
        }

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
            if (isset($formatter['formats']) && is_array($formatter['formats'])) {
                return $this->formatChainResult($value, $formatter['formats'], $item, $key);
            }

            if ($formatter !== [] && array_is_list($formatter)) {
                return $this->formatChainResult($value, $formatter, $item, $key);
            }

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
            $steps = $this->splitFormatterChain($formatter);

            if (count($steps) > 1 || str_contains($formatter, '(')) {
                return $this->formatChainResult($value, $steps, $item, $key);
            }

            $result = $this->formatSimpleValue($value, $formatter, $this->getFormatterOptions($key));

            return e($result);
        }

        return e($value);
    }

    protected function isCheckboxFormatter($key): bool
    {
        $formatter = $this->formatters[$key] ?? null;

        return $formatter === 'checkbox'
            || (is_array($formatter) && ($formatter['type'] ?? null) === 'checkbox');
    }

    protected function getFormatterOptions($key): array
    {
        return $this->formatterOptions[$key] ?? [];
    }
}
