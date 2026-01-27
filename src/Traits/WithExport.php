<?php

namespace Developerawam\LivewireDatatable\Traits;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Developerawam\LivewireDatatable\Exports\DataTableExport;

trait WithExport
{
    public function bootWithExport(): void
    {
        $this->enableExport = config('livewire-datatable.export.enabled', true);
        $this->exportTypes = config('livewire-datatable.export.types', ['excel', 'pdf']);
    }

    public function export(string $type)
    {
        $this->ensureDataSourceInitialized();

        // Get all data based on whether filtering is active
        if ($this->filterDataSearch) {
            // Export filtered data
            $query = $this->model::query();
            foreach ($this->filterBy as $i => $column) {
                $value = trim($this->query[$i]) ?? null;
                if (!$value) continue;

                // RELATION FILTER: user.name / user.profile.country.name
                if (str_contains($column, '.')) {
                    $parts = explode('.', $column);
                    $field = array_pop($parts);   // last part = column name
                    $relationPath = implode('.', $parts);

                    $query->whereHas($relationPath, function ($q) use ($field, $value) {
                        $q->where($field, 'LIKE', "%{$value}%");
                    });
                }
                // NORMAL COLUMN FILTER
                else {
                    $query->where($column, 'LIKE', "%{$value}%");
                }
            }

            // Apply sorting when filtering is active
            if (!empty($this->sortField) && in_array($this->sortField, $this->sortable)) {
                $query->orderBy($this->sortField, $this->sortDirection);
            } elseif ($this->defaultSortField) {
                $query->orderBy($this->defaultSortField, $this->defaultSortDirection);
            }

            $data = $query->get();

            $filename = Str::slug(class_basename($this->model ?? 'DataTable'));
            $filename .= '-filtered-' . now()->format('Y-m-d-H-i-s');
        } else {
            // Export searched data
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
        }

        switch ($type) {
            case 'excel':
                return $this->exportToExcel($data, $filename);
            case 'pdf':
                return $this->exportToPdf($data, $filename);
            default:
                throw new \InvalidArgumentException("Export type '{$type}' not supported");
        }
    }

    protected function exportToExcel(array|object $data, string $filename)
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

    protected function exportToPdf(array|object $data, string $filename)
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
