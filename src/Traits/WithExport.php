<?php

namespace Developerawam\LivewireDatatable\Traits;

use Barryvdh\DomPDF\Facade\Pdf;
use Developerawam\LivewireDatatable\Exports\DataTableExport;
use Developerawam\LivewireDatatable\Exports\DataTableQueryExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

trait WithExport
{
    public function bootWithExport(): void
    {
        $this->enableExport = config('livewire-datatable.export.enabled', true);
        $this->exportTypes = config('livewire-datatable.export.types', ['excel', 'pdf']);
    }

    public function export(string $type, ?array $selectedColumns = null, ?string $paperSize = null, ?string $orientation = null)
    {
        $this->ensureDataSourceInitialized();

        $columns = $selectedColumns ?? $this->columns;

        // Build filename
        $filename = Str::slug(class_basename($this->model ?? 'DataTable'));
        if ($this->filterDataSearch) {
            $filename .= '-filtered';
        }
        if ($this->dateFilterEnabled) {
            $filename .= '-date-filtered';
        }
        if (! empty($this->search)) {
            $filename .= '-search-'.Str::slug($this->search);
        }
        if ($selectedColumns) {
            $filename .= '-custom';
        }
        $filename .= '-'.now()->format('Y-m-d-H-i-s');

        // Model source: use unified filtered query (memory-safe via cursor/query)
        if ($this->model) {
            /** @var Builder $query */
            $query = $this->buildFilteredQuery();

            return match ($type) {
                'excel' => $this->exportToExcelQuery($query, $filename, $columns),
                'pdf' => $this->exportToPdfQuery($query, $filename, $columns, $paperSize, $orientation),
                default => throw new \InvalidArgumentException("Export type '{$type}' not supported"),
            };
        }

        // API source: fallback to dataSource (must load all)
        $params = [
            'search' => $this->search,
            'sort_field' => $this->sortField,
            'sort_direction' => $this->sortDirection,
            'per_page' => 'all',
        ];

        $data = $this->dataSource->getData($params)->items();

        return match ($type) {
            'excel' => $this->exportToExcel(collect($data), $filename, $columns),
            'pdf' => $this->exportToPdf(collect($data), $filename, $columns, $paperSize, $orientation),
            default => throw new \InvalidArgumentException("Export type '{$type}' not supported"),
        };
    }

    protected function exportToExcel(Collection $data, string $filename, array $columns)
    {
        return Excel::download(
            new DataTableExport(
                $data,
                $columns,
                $this->formatters,
                $this->formatterOptions
            ),
            $filename.'.xlsx'
        );
    }

    protected function exportToExcelQuery(Builder $query, string $filename, array $columns)
    {
        return Excel::download(
            new DataTableQueryExport(
                $query,
                $columns,
                $this->formatters,
                $this->formatterOptions
            ),
            $filename.'.xlsx'
        );
    }

    protected function exportToPdf(Collection $data, string $filename, array $columns, ?string $paperSize = null, ?string $orientation = null)
    {
        $html = view('livewire-datatable::exports.pdf', [
            'data' => $data,
            'columns' => $columns,
            'formatters' => $this->formatters,
            'formatterOptions' => $this->formatterOptions,
        ])->render();

        return response()->streamDownload(function () use ($html, $paperSize, $orientation) {
            $pdf = Pdf::loadHtml($html);
            $pdf->setPaper(
                $paperSize ?? config('livewire-datatable.export.paper_size', 'a4'),
                $orientation ?? config('livewire-datatable.export.orientation', 'portrait')
            );
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'isFontSubsettingEnabled' => true,
                'defaultFont' => 'serif',
                'dpi' => 96,
            ]);
            echo $pdf->output();
        }, $filename.'.pdf');
    }

    protected function exportToPdfQuery(Builder $query, string $filename, array $columns, ?string $paperSize = null, ?string $orientation = null)
    {
        // Use cursor for memory-safe iteration; view handles LazyCollection
        $data = $query->cursor();

        $html = view('livewire-datatable::exports.pdf', [
            'data' => $data,
            'columns' => $columns,
            'formatters' => $this->formatters,
            'formatterOptions' => $this->formatterOptions,
        ])->render();

        return response()->streamDownload(function () use ($html, $paperSize, $orientation) {
            $pdf = Pdf::loadHtml($html);
            $pdf->setPaper(
                $paperSize ?? config('livewire-datatable.export.paper_size', 'a4'),
                $orientation ?? config('livewire-datatable.export.orientation', 'portrait')
            );
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'isFontSubsettingEnabled' => true,
                'defaultFont' => 'serif',
                'dpi' => 96,
            ]);
            echo $pdf->output();
        }, $filename.'.pdf');
    }
}
