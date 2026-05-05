<?php

namespace Developerawam\LivewireDatatable\Traits;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Developerawam\LivewireDatatable\Exports\DataTableExport;

/**
 * Improvement #1: export() now uses buildFilteredQuery() from WithFiltering.
 * Zero duplication of filter logic.
 */
trait WithExport
{
    /**
     * FIX: boot() dihapus — $enableExport dan $exportTypes diset di mount() DataTable,
     * bukan di boot() yang dijalankan setiap request.
     */
    public function export(string $type)
    {
        $this->ensureDataSourceInitialized();

        if ($this->filterDataSearch) {
            // #1 FIX: reuse centralized buildFilteredQuery() - no duplication
            $data = $this->buildFilteredQuery()->get();
            $filename = Str::slug(class_basename($this->model ?? 'DataTable'));
            $filename .= '-filtered-' . now()->format('Y-m-d-H-i-s');
        } else {
            $data = $this->dataSource->getData([
                'search'         => $this->search,
                'sort_field'     => $this->sortField,
                'sort_direction' => $this->sortDirection,
                'per_page'       => 'all',
            ])->items();

            $filename = Str::slug(class_basename($this->model ?? 'DataTable'));
            if (!empty($this->search)) {
                $filename .= '-search-' . Str::slug($this->search);
            }
            $filename .= '-' . now()->format('Y-m-d-H-i-s');
        }

        return match ($type) {
            'excel' => $this->exportToExcel($data, $filename),
            'pdf'   => $this->exportToPdf($data, $filename),
            default => throw new \InvalidArgumentException("Export type '{$type}' not supported"),
        };
    }

    protected function exportToExcel(array|object $data, string $filename)
    {
        return Excel::download(
            new DataTableExport(collect($data), $this->columns, $this->formatters, $this->formatterOptions),
            $filename . '.xlsx'
        );
    }

    protected function exportToPdf(array|object $data, string $filename)
    {
        $html = view('livewire-datatable::exports.pdf', [
            'data'             => $data,
            'columns'          => $this->columns,
            'formatters'       => $this->formatters,
            'formatterOptions' => $this->formatterOptions,
        ])->render();

        return response()->streamDownload(function () use ($html) {
            echo PDF::loadHtml($html)
                ->setPaper(
                    config('livewire-datatable.export.paper_size', 'a4'),
                    config('livewire-datatable.export.orientation', 'portrait')
                )
                ->output();
        }, $filename . '.pdf');
    }
}
