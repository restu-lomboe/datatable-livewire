@php
    $visibleColumns = array_filter($columns, fn ($key) => $key !== 'action', ARRAY_FILTER_USE_KEY);
    $columnCount = count($visibleColumns);
    $baseFontSize = max(6, min(10, 140 / $columnCount));
    $cellPadding = max(1, min(6, 80 / $columnCount));
@endphp

<div class="container">
    <table class="table" style="font-size: {{ $baseFontSize }}pt;">
        <thead>
            <tr>
                @foreach ($visibleColumns as $label)
                    <th>{{ $label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $row)
                <tr>
                    @foreach (array_keys($visibleColumns) as $column)
                        @if ($column === 'no')
                            <td>{{ $index + 1 }}</td>
                        @else
                            <td>
                                @php
                                    $value = data_get($row, $column);
                                    if (isset($formatters[$column])) {
                                        $formatter = $formatters[$column];
                                        $options = $formatterOptions[$column] ?? [];

                                        if (is_string($formatter)) {
                                            switch ($formatter) {
                                                case 'date':
                                                    $value = $value instanceof \DateTimeInterface ? $value->format($options['format'] ?? 'Y-m-d') : $value;
                                                    break;
                                                case 'datetime':
                                                    $value = $value instanceof \DateTimeInterface ? $value->format($options['format'] ?? 'Y-m-d H:i:s') : $value;
                                                    break;
                                                case 'number':
                                                    $value = is_numeric($value) ? number_format($value, $options['decimals'] ?? 0, $options['decimal_point'] ?? '.', $options['thousand_sep'] ?? ',') : $value;
                                                    break;
                                                case 'currency':
                                                    $value = is_numeric($value) ? ($options['symbol'] ?? 'Rp ') . number_format($value, $options['decimals'] ?? 2, $options['decimal_point'] ?? '.', $options['thousand_sep'] ?? ',') : $value;
                                                    break;
                                                case 'boolean':
                                                    $value = $value ? ($options['true'] ?? 'Yes') : ($options['false'] ?? 'No');
                                                    break;
                                            }
                                        } elseif (is_array($formatter)) {
                                            $type = $formatter['type'] ?? null;
                                            $typeOptions = array_merge($options, $formatter['options'] ?? []);
                                            if (in_array($type, ['date', 'datetime', 'number', 'currency', 'boolean'])) {
                                                $value = $value instanceof \DateTimeInterface ? $value->format($typeOptions['format'] ?? 'Y-m-d') : $value;
                                            }
                                        }
                                    }
                                @endphp
                                {{ $value }}
                            </td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<style>
    @page {
        margin: 10mm;
    }

    .container {
        font-family: Arial, sans-serif;
        width: 100%;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        table-layout: auto;
    }

    .table th,
    .table td {
        padding: {{ $cellPadding }}pt 2pt;
        border: 1px solid #ddd;
        text-align: left;
        word-break: break-word;
        overflow-wrap: break-word;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    .table tr:nth-child(even) {
        background-color: #f8f9fa;
    }
</style>
