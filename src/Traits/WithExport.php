<?php

namespace Developerawam\LivewireDatatable\Traits;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Developerawam\LivewireDatatable\Exports\DataTableExport;

trait WithExport
{
    public function bootWithExport()
    {
        $this->enableExport = config('livewire-datatable.export.enabled', true);
        $this->exportTypes = config('livewire-datatable.export.types', ['excel', 'pdf']);
    }

    public function export($type)
    {
        $this->ensureDataSourceInitialized();

        // Store current per page value
        $currentPerPage = $this->perPage;

        // Get the total count for all records
        $params = [
            'search' => $this->search,
            'sort_field' => $this->sortField,
            'sort_direction' => $this->sortDirection,
            'per_page' => 'all' // This will get all records
        ];

        // Get all data
        $data = $this->dataSource->getData($params)->items();

        $filename = Str::slug(class_basename($this->model ?? 'DataTable'));
        if (!empty($this->search)) {
            $filename .= '-search-' . Str::slug($this->search);
        }
        $filename .= '-' . now()->format('Y-m-d-H-i-s');

        switch ($type) {
            case 'excel':
                return $this->exportToExcel($data, $filename);
            case 'pdf':
                return $this->exportToPdf($data, $filename);
            default:
                throw new \InvalidArgumentException("Export type '{$type}' not supported");
        }
    }

    protected function exportToExcel($data, $filename)
    {
        return Excel::download(
            new DataTableExport(
                collect($data),
                $this->columns,
                $this->formatters,
                $this->formatterOptions
            ),
            $filename . '.xlsx'
        );
    }

    protected function exportToPdf($data, $filename)
    {
        $html = view('livewire-datatable::exports.pdf', [
            'data' => $data,
            'columns' => $this->columns,
            'formatters' => $this->formatters,
            'formatterOptions' => $this->formatterOptions
        ])->render();

        return response()->streamDownload(function () use ($html) {
            $pdf = PDF::loadHtml($html)
                ->setPaper(
                    config('livewire-datatable.export.paper_size', 'a4'),
                    config('livewire-datatable.export.orientation', 'portrait')
                );
            echo $pdf->output();
        }, $filename . '.pdf');
    }
}
