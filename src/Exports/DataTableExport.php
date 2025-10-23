<?php

namespace Developerawam\LivewireDatatable\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataTableExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $columns;
    protected $formatters;
    protected $formatterOptions;

    public function __construct(Collection $data, array $columns, ?array $formatters = [], ?array $formatterOptions = [])
    {
        $this->data = $data;
        $this->columns = $columns;
        $this->formatters = $formatters;
        $this->formatterOptions = $formatterOptions;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return array_values(array_filter($this->columns, function($key) {
            return $key !== 'action';
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
                return is_numeric($value) ? ($options['symbol'] ?? 'Rp ') . number_format(
                    $value,
                    $options['decimals'] ?? 2,
                    $options['decimal_point'] ?? '.',
                    $options['thousand_sep'] ?? ','
                ) : $value;
            case 'boolean':
                return $value ? ($options['true'] ?? 'Yes') : ($options['false'] ?? 'No');
            default:
                return $value;
        }
    }

    protected function formatComplexValue($value, array $formatter)
    {
        $type = $formatter['type'] ?? null;
        $options = $formatter['options'] ?? [];

        return $this->formatSimpleValue($value, $type, $options);
    }

    protected $currentRow = 0;

    public function map($row): array
    {
        $this->currentRow++;
        $result = [];

        foreach (array_keys($this->columns) as $column) {
            // Skip action column
            if ($column === 'action') {
                continue;
            }

            if ($column === 'no') {
                $value = $this->currentRow;
            } else {
                $value = data_get($row, $column);

                // Apply formatter if exists
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

    /**
     * Style the Excel sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (header)
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8E8E8']
                ]
            ],
        ];
    }
}
