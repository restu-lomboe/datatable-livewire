<?php

namespace Developerawam\LivewireDatatable\Traits;

use Barryvdh\DomPDF\Facade\Pdf;
use Developerawam\LivewireDatatable\Exports\DataTableExport;
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

        if ($this->filterDataSearch || $this->dateFilterEnabled) {
            $query = $this->model::query();

            if (! empty($this->scopeParams)) {
                $query = $query->{$this->scope}(...$this->scopeParams);
            } elseif ($this->scope) {
                $query = $query->{$this->scope}();
            }

            if ($this->filterDataSearch) {
                foreach ($this->filterBy as $i => $column) {
                    $value = trim($this->query[$i]) ?? null;
                    if (! $value) {
                        continue;
                    }

                    if (str_contains($column, '.')) {
                        $parts = explode('.', $column);
                        $field = array_pop($parts);
                        $relationPath = implode('.', $parts);

                        $query->whereHas($relationPath, function ($q) use ($field, $value) {
                            $q->where($field, 'LIKE', "%{$value}%");
                        });
                    } else {
                        $query->where($column, 'LIKE', "%{$value}%");
                    }
                }
            }

            if (! $this->filterDataSearch && ! empty($this->search)) {
                $query->where(function ($q) {
                    foreach ($this->searchable as $field) {
                        if (str_contains($field, '.')) {
                            $parts = explode('.', $field);
                            $relationField = array_pop($parts);
                            $relations = $parts;
                            $q->orWhereHas($relations[0], function ($subQ) use ($relations, $relationField) {
                                if (count($relations) > 1) {
                                    $subQ->whereHas(implode('.', array_slice($relations, 1)), fn ($sq) => $sq->where($relationField, 'like', '%'.$this->search.'%'));
                                } else {
                                    $subQ->where($relationField, 'like', '%'.$this->search.'%');
                                }
                            });
                        } else {
                            $q->orWhere($field, 'like', '%'.$this->search.'%');
                        }
                    }
                });
            }

            if ($this->dateFilterEnabled && $this->dateFilterColumn) {
                $column = $this->dateFilterColumn;
                if (str_contains($column, '.')) {
                    $parts = explode('.', $column);
                    $field = array_pop($parts);
                    $relationPath = implode('.', $parts);
                    $query->whereHas($relationPath, fn ($q) => $this->applyDateRangeQuery($q, $field));
                } else {
                    $this->applyDateRangeQuery($query, $column);
                }
            }

            if (! empty($this->sortField) && in_array($this->sortField, $this->sortable)) {
                $query->orderBy($this->sortField, $this->sortDirection);
            } elseif ($this->defaultSortField) {
                $query->orderBy($this->defaultSortField, $this->defaultSortDirection);
            }

            $data = $query->get();
        } else {
            $params = [
                'search' => $this->search,
                'sort_field' => $this->sortField,
                'sort_direction' => $this->sortDirection,
                'per_page' => 'all',
            ];

            $data = $this->dataSource->getData($params)->items();
        }

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

        switch ($type) {
            case 'excel':
                return $this->exportToExcel(collect($data), $filename, $columns);
            case 'pdf':
                return $this->exportToPdf(collect($data), $filename, $columns, $paperSize, $orientation);
            default:
                throw new \InvalidArgumentException("Export type '{$type}' not supported");
        }
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
}
