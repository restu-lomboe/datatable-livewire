<?php

namespace Developerawam\LivewireDatatable\Components;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class DataTable extends Component
{
    use WithPagination;

    public $model;
    public $columns = [];
    public $searchable = [];
    public $sortable = [];
    public $unsortable = [];
    public $search = '';
    public $perPage;
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $pageOptions;
    public $theme = [];
    public $customColumns = [];

    protected function queryString()
    {
        return [
            'search' => ['except' => ''],
            'sortField' => ['except' => 'id'],
            'sortDirection' => ['except' => 'asc'],
            'perPage' => ['except' => $this->pageOptions[0] ?? 10],
        ];
    }

    public function mount($model, $columns = [], $searchable = [], $unsortable = [], $theme = [], $customColumns = [])
    {
        $this->model = $model;
        $this->columns = $columns;
        $this->searchable = $searchable;
        $this->unsortable = $unsortable;
        $this->customColumns = $customColumns;
        // By default, all columns are sortable except those in unsortable array
        $this->sortable = array_diff(array_keys($columns), $unsortable);

        // Initialize pagination options from config
        $this->pageOptions = config('livewire-datatable.per_page_options', [10, 25, 50, 100]);
        $this->perPage = $this->pageOptions[0] ?? 10;

        // Load theme from config and merge with any custom theme passed
        $this->theme = array_merge(
            config('livewire-datatable.theme', []),
            $theme
        );
    }

    public function getClass($element)
    {
        return $this->theme[$element] ?? '';
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    protected function getQuery(): Builder
    {
        $query = $this->model::query();

        if ($this->search && !empty($this->searchable)) {
            $query->where(function ($query) {
                foreach ($this->searchable as $field) {
                    if (Str::contains($field, '.')) {
                        // Handle relationship search
                        [$relation, $relationField] = explode('.', $field);
                        $query->orWhereHas($relation, function ($query) use ($relationField) {
                            $query->where($relationField, 'like', '%' . $this->search . '%');
                        });
                    } else {
                        $query->orWhere($field, 'like', '%' . $this->search . '%');
                    }
                }
            });
        }

        if ($this->sortField && in_array($this->sortField, $this->sortable)) {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        return $query;
    }

    public function formatValue($value, $column)
    {
        return $value;
    }

    public function render()
    {
        $items = $this->getQuery()->paginate($this->perPage);

        return view('livewire-datatable::datatable', [
            'items' => $items,
        ]);
    }
}
