<?php

namespace Developerawam\LivewireDatatable\Components;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Lazy]
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
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $pageOptions;
    public $theme = [];
    public $customColumns = [];
    public $scope;

    public function mount($model, $scope = null, $columns = [], $searchable = [], $unsortable = [], $theme = [], $customColumns = [])
    {
        $this->model = $model;
        $this->scope = $scope;
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
        $this->theme = array_merge(config('livewire-datatable.theme', []), $theme);
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

    #[Computed]
    protected function getQuery()
    {
        $query = $this->model::query();

        // check if custom query is set
        if ($this->scope) {
            $query = $query->{$this->scope}();
        }

        // Apply search if provided
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
            // remove order from query if already set
            $hasOrder = !empty($query->getQuery()->orders);
            if ($hasOrder) {
                $query->getQuery()->orders = null;
            }

            $selects = [$this->model::getModel()->getTable() . '.*']; // start with all user columns

            if (str_contains($this->sortField, '.')) {
                $parts = explode('.', $this->sortField);
                $column = array_pop($parts);

                $modelInstance = new $this->model();
                foreach ($parts as $relationName) {
                    $relationInstance = $modelInstance->$relationName();
                    $relatedTable = $relationInstance->getRelated()->getTable();

                    if ($relationInstance instanceof BelongsTo) {
                        $foreign = $relationInstance->getForeignKeyName();
                        $ownerKey = $relationInstance->getOwnerKeyName();
                        $query->leftJoin($relatedTable, $modelInstance->getTable() . '.' . $foreign, '=', $relatedTable . '.' . $ownerKey);
                    } else {
                        $foreign = $relationInstance->getQualifiedForeignKeyName();
                        $local = $relationInstance->getQualifiedParentKeyName();
                        $query->leftJoin($relatedTable, $foreign, '=', $local);
                    }

                    // alias all columns from related table to avoid collision
                    foreach ($relationInstance->getRelated()->getFillable() as $field) {
                        $selects[] = $relatedTable . '.' . $field . ' as ' . $relatedTable . '_' . $field;
                    }

                    $modelInstance = $relationInstance->getRelated();
                }
                $query->select($selects)->orderBy($relatedTable . '.' . $column, $this->sortDirection);
            } else {
                // check when order by no
                if ($this->sortField == 'no') {
                    $this->sortField = 'no';
                    $this->sortDirection = $this->sortDirection;

                    $query->orderBy('created_at', $this->sortDirection);
                } else {
                    $query->orderBy($this->sortField, $this->sortDirection);
                }
            }
        }

        return $query->{config('livewire-datatable.default_pagination')}($this->perPage);
    }

    public function placeholder()
    {
        return view('livewire-datatable::placeholders.datatable');
    }

    public function render()
    {
        return view('livewire-datatable::datatable');
    }
}
