<?php

namespace Developerawam\LivewireDatatable\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

trait WithFormatters
{
    protected function formatSimpleValue($value, string $formatter, array $options = [])
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
                return ($options['symbol'] ?? 'Rp ') . number_format(
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

    protected function formatComplexValue($value, array $formatter)
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
                return ($options['symbol'] ?? 'Rp ') . number_format(
                    $value,
                    $options['decimals'] ?? 2,
                    $options['decimal_point'] ?? '.',
                    $options['thousand_sep'] ?? ','
                );
            case 'date':
                return $this->formatDate($value, $options['format'] ?? 'Y-m-d');
            default:
                return $value;
        }
    }

    protected function formatDate($value, string $format)
    {
        if (!$value) {
            return '';
        }

        try {
            return Carbon::parse($value)->format($format);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function formatValue($key, $value)
    {
        if (!isset($this->formatters[$key])) {
            return $value;
        }

        $formatter = $this->formatters[$key];

        // Handle complex formatter array with type and options
        if (is_array($formatter)) {
            return $this->formatComplexValue($value, $formatter);
        }

        // Handle simple string formatter
        if (is_string($formatter)) {
            return $this->formatSimpleValue($value, $formatter, $this->getFormatterOptions($key));
        }

        return $value;
    }

    protected function getFormatterOptions($key)
    {
        return $this->formatterOptions[$key] ?? [];
    }
}
