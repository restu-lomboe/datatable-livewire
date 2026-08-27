@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Carbon;
    $visibleColumns = array_filter($columns, fn ($key) => ! in_array($key, ['action', 'actions'], true), ARRAY_FILTER_USE_KEY);
    $columnCount = max(1, count($visibleColumns));
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
                                        $formatDate = function ($val, $fmt) {
                                            if (! $val) return $val;
                                            try {
                                                $dt = $val instanceof \DateTimeInterface ? $val : Carbon::parse($val);
                                                return $dt->format($fmt);
                                            } catch (\Throwable $e) {
                                                return $val;
                                            }
                                        };
                                        if (is_string($formatter)) {
                                            switch ($formatter) {
                                                case 'date':
                                                    $value = $formatDate($value, $options['format'] ?? 'Y-m-d');
                                                    break;
                                                case 'datetime':
                                                    $value = $formatDate($value, $options['format'] ?? 'Y-m-d H:i:s');
                                                    break;
                                                case 'time':
                                                    $value = $formatDate($value, $options['format'] ?? 'H:i:s');
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
                                                case 'uppercase':
                                                    $value = Str::upper($value);
                                                    break;
                                                case 'lowercase':
                                                    $value = Str::lower($value);
                                                    break;
                                                default:
                                                    break;
                                            }
                                        } elseif (is_array($formatter)) {
                                            $type = $formatter['type'] ?? null;
                                            $typeOptions = array_merge($options, $formatter['options'] ?? []);
                                            switch ($type) {
                                                case 'date':
                                                    $value = $formatDate($value, $typeOptions['format'] ?? 'Y-m-d');
                                                    break;
                                                case 'datetime':
                                                    $value = $formatDate($value, $typeOptions['format'] ?? 'Y-m-d H:i:s');
                                                    break;
                                                case 'time':
                                                    $value = $formatDate($value, $typeOptions['format'] ?? 'H:i:s');
                                                    break;
                                                case 'number':
                                                    $value = is_numeric($value) ? number_format($value, $typeOptions['decimals'] ?? 0, $typeOptions['decimal_point'] ?? '.', $typeOptions['thousand_sep'] ?? ',') : $value;
                                                    break;
                                                case 'currency':
                                                case 'money':
                                                    $value = is_numeric($value) ? ($typeOptions['symbol'] ?? 'Rp ') . number_format($value, $typeOptions['decimals'] ?? 2, $typeOptions['decimal_point'] ?? '.', $typeOptions['thousand_sep'] ?? ',') : $value;
                                                    break;
                                                case 'boolean':
                                                    $value = $value ? ($typeOptions['true'] ?? 'Yes') : ($typeOptions['false'] ?? 'No');
                                                    break;
                                                case 'limit':
                                                    $value = Str::limit($value, $typeOptions['length'] ?? 50, $typeOptions['end'] ?? '...');
                                                    break;
                                                case 'words':
                                                    $value = Str::words($value, $typeOptions['words'] ?? 10, $typeOptions['end'] ?? '...');
                                                    break;
                                                case 'uppercase':
                                                    $value = Str::upper($value);
                                                    break;
                                                case 'lowercase':
                                                    $value = Str::lower($value);
                                                    break;
                                                case 'markdown':
                                                    // Strip HTML for PDF clean rendering
                                                    $value = strip_tags(Str::markdown($value));
                                                    break;
                                                case 'link':
                                                    // PDF keeps original value (no URL)
                                                    break;
                                                default:
                                                    break;
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
