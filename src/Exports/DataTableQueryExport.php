<?php

namespace Developerawam\LivewireDatatable\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataTableQueryExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected Builder $query;

    protected array $columns;

    protected array $formatters;

    protected array $formatterOptions;

    protected int $chunkSize;

    protected int $currentRow = 0;

    public function __construct(Builder $query, array $columns, ?array $formatters = [], ?array $formatterOptions = [], int $chunkSize = 1000)
    {
        $this->query = $query;
        $this->columns = $columns;
        $this->formatters = $formatters;
        $this->formatterOptions = $formatterOptions;
        $this->chunkSize = $chunkSize;
    }

    public function chunkSize(): int
    {
        return $this->chunkSize;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return array_values(array_filter($this->columns, function ($key) {
            return ! in_array($key, ['action', 'actions'], true);
        }, ARRAY_FILTER_USE_KEY));
    }

    protected function formatValue($value, $formatter, $options = [])
    {
        if (is_string($formatter)) {
            return $this->formatSimpleValue($value, $formatter, $options);
        } elseif (is_array($formatter)) {
            return $this->formatComplexValue($value, $formatter);
        }

        return $value;
    }

    protected function formatSimpleValue($value, string $formatter, array $options = [])
    {
        switch ($formatter) {
            case 'date':
                return $value instanceof \DateTimeInterface
                    ? $value->format($options['format'] ?? 'Y-m-d')
                    : $value;
            case 'datetime':
                return $value instanceof \DateTimeInterface
                    ? $value->format($options['format'] ?? 'Y-m-d H:i:s')
                    : $value;
            case 'number':
                return is_numeric($value) ? number_format(
                    $value,
                    $options['decimals'] ?? 0,
                    $options['decimal_point'] ?? '.',
                    $options['thousand_sep'] ?? ','
                ) : $value;
            case 'currency':
                return is_numeric($value) ? ($options['symbol'] ?? 'Rp ').number_format(
                    $value,
                    $options['decimals'] ?? 2,
                    $options['decimal_point'] ?? '.',
                    $options['thousand_sep'] ?? ','
                ) : $value;
            case 'boolean':
                return $value ? ($options['true'] ?? 'Yes') : ($options['false'] ?? 'No');
            case 'strip':
            case 'html':
            case 'plain':
                return strip_tags((string) $value, $options['allowed'] ?? '');
            default:
                return $value;
        }
    }

    protected function formatComplexValue($value, array $formatter)
    {
        $type = $formatter['type'] ?? null;
        $options = $formatter['options'] ?? [];

        if ($type === 'link') {
            return $value;
        }

        if (in_array($type, ['strip', 'html', 'plain'], true)) {
            return strip_tags((string) $value, $options['allowed'] ?? '');
        }

        return $this->formatSimpleValue($value, $type, $options);
    }

    public function map($row): array
    {
        $this->currentRow++;
        $result = [];

        foreach (array_keys($this->columns) as $column) {
            if (in_array($column, ['action', 'actions'], true)) {
                continue;
            }

            if ($column === 'no') {
                $value = $this->currentRow;
            } else {
                $value = data_get($row, $column);

                if (isset($this->formatters[$column])) {
                    $formatter = $this->formatters[$column];
                    $options = $this->formatterOptions[$column] ?? [];
                    $value = $this->formatValue($value, $formatter, $options);
                }
            }

            $result[] = $value;
        }

        return $result;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8E8E8'],
                ],
            ],
        ];
    }
}
