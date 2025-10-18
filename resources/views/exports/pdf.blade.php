<div class="container">
    <table class="table">
        <thead>
            <tr>
                @foreach ($columns as $key => $label)
                    @if ($key !== 'action')
                        <th>{{ $label }}</th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $row)
                <tr>
                    @foreach (array_keys($columns) as $column)
                        @if ($column !== 'action')
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
                                                        $value =
                                                            $value instanceof \DateTimeInterface
                                                                ? $value->format($options['format'] ?? 'Y-m-d')
                                                                : $value;
                                                        break;
                                                    case 'datetime':
                                                        $value =
                                                            $value instanceof \DateTimeInterface
                                                                ? $value->format($options['format'] ?? 'Y-m-d H:i:s')
                                                                : $value;
                                                        break;
                                                    case 'number':
                                                        $value = is_numeric($value)
                                                            ? number_format(
                                                                $value,
                                                                $options['decimals'] ?? 0,
                                                                $options['decimal_point'] ?? '.',
                                                                $options['thousand_sep'] ?? ',',
                                                            )
                                                            : $value;
                                                        break;
                                                    case 'currency':
                                                        $value = is_numeric($value)
                                                            ? ($options['symbol'] ?? 'Rp ') .
                                                                number_format(
                                                                    $value,
                                                                    $options['decimals'] ?? 2,
                                                                    $options['decimal_point'] ?? '.',
                                                                    $options['thousand_sep'] ?? ',',
                                                                )
                                                            : $value;
                                                        break;
                                                    case 'boolean':
                                                        $value = $value
                                                            ? $options['true'] ?? 'Yes'
                                                            : $options['false'] ?? 'No';
                                                        break;
                                                }
                                            } elseif (is_array($formatter)) {
                                                $type = $formatter['type'] ?? null;
                                                $typeOptions = array_merge($options, $formatter['options'] ?? []);

                                                switch ($type) {
                                                    case 'date':
                                                    case 'datetime':
                                                    case 'number':
                                                    case 'currency':
                                                    case 'boolean':
                                                        $value =
                                                            $value instanceof \DateTimeInterface
                                                                ? $value->format($typeOptions['format'] ?? 'Y-m-d')
                                                                : $value;
                                                        break;
                                                }
                                            }
                                        }
                                    @endphp
                                    {{ $value }}
                                </td>
                            @endif
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<style>
    .container {
        padding: 20px;
        font-family: Arial, sans-serif;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1rem;
    }

    .table th,
    .table td {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    .table tr:nth-child(even) {
        background-color: #f8f9fa;
    }
</style>
