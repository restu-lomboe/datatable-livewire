<?php

namespace Developerawam\LivewireDatatable\Components;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Schema;
use Developerawam\LivewireDatatable\Traits\WithExport;
use Developerawam\LivewireDatatable\Traits\WithFormatters;
use Developerawam\LivewireDatatable\DataSources\ApiDataSource;
use Developerawam\LivewireDatatable\DataSources\ModelDataSource;
use Developerawam\LivewireDatatable\DataSources\DataSourceInterface;

#[Lazy]
class DataTable extends Component
{
    use WithPagination, WithFormatters, WithExport;

    public $model;
    public $apiConfig;
    public $columns = [];
    public $searchable = [];
    public $sortable = [];
    public $unsortable = [];
    public $search = '';
    public $perPage;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $defaultSortField = 'created_at';
    public $defaultSortDirection = 'desc';
    public $pageOptions;
    public $theme = [];
    public $customColumns = [];
    public $formatters = [];
    public $formatterOptions = [];
    public $scope;
    public $scopeParams = [];
    public $totals;
    public $page = 1;
    public $enableExport;
    public $exportTypes = [];
    protected $dataSource;

    public $filter = false;
    public $filterDataSearch = false;
    public $filterBy = [];
    public $query = [];
    public $disabledAddFilterButton = false;


    protected function ensureDataSourceInitialized(): void
    {
        if (!$this->dataSource) {
            $this->initializeDataSource();
        }
    }

    public function mount($model = null, $apiConfig = null, $scope = null, $columns = [], $scopeParams = [], $searchable = [], $unsortable = [], $theme = [], $customColumns = [], $formatters = [], $formatterOptions = [], $defaultSortField = 'created_at', $defaultSortDirection = 'desc')
    {
        if (!$model && !$apiConfig) {
            throw new \InvalidArgumentException('Either model or apiConfig must be provided');
        }

        $this->model = $model;
        $this->apiConfig = $apiConfig;
        $this->scope = $scope;
        $this->scopeParams = $scopeParams;
        $this->columns = $columns;
        $this->searchable = $searchable;
        $this->unsortable = $unsortable;
        $this->customColumns = $customColumns;
        $this->formatters = $formatters;
        $this->formatterOptions = $formatterOptions;
        $this->defaultSortField = $defaultSortField;
        $this->defaultSortDirection = $defaultSortDirection;
        $this->sortField = $defaultSortField;
        $this->sortDirection = $defaultSortDirection;
        // By default, all columns are sortable except those in unsortable array
        $this->sortable = array_values(array_diff(array_keys($columns), $unsortable));

        // Initialize pagination options from config
        $this->pageOptions = config('livewire-datatable.per_page_options', [10, 25, 50, 100]);
        $this->perPage = $this->pageOptions[0] ?? 10;

        // Load theme from config and merge with any custom theme passed
        $this->theme = array_merge(config('livewire-datatable.theme', []), $theme);

        // Initialize export settings from config
        $this->enableExport = config('livewire-datatable.export.enabled', true);
        $this->exportTypes = config('livewire-datatable.export.types', ['excel', 'pdf']);

        // Initialize the appropriate data source
        $this->initializeDataSource();
    }

    public function getClass($element)
    {
        return $this->theme[$element] ?? '';
    }

    public function sortBy($field)
    {
        $this->ensureDataSourceInitialized();

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->ensureDataSourceInitialized();
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->ensureDataSourceInitialized();
        $this->resetPage();
    }

    public function updatingPage($page)
    {
        $this->page = $page;
    }

    #[On('reset-table')]
    public function resetTable()
    {
        $this->resetPage();
    }

    #[Computed]
    protected function filterByColumn()
    {
        $model = new $this->model;
        $table = $model->getTable();

        // MAIN TABLE COLUMNS
        $columns = collect(Schema::getColumnListing($table))
            ->reject(fn ($c) => in_array($c, ['id', 'created_at', 'updated_at']))
            ->mapWithKeys(fn ($c) => [$c => Str::headline($c)])
            ->toArray();

        // GET RELATIONS FROM `$with`
        foreach (array_keys($model->getEagerLoads()) as $relationPath) {

            $relationColumns = $this->getRelationColumns($model, $relationPath);

            foreach ($relationColumns as $key => $label) {
                $columns[$key] = $label;
            }
        }

        return $columns;
    }

    protected function getRelationColumns($model, $relationPath)
    {
        $parts = explode('.', $relationPath);
        $relationName = array_shift($parts);

        if (!method_exists($model, $relationName)) {
            return [];
        }

        $relation = $model->{$relationName}();
        $relatedModel = $relation->getRelated();
        $relatedTable = $relatedModel->getTable();

        // Get columns for this table
        $columns = collect(Schema::getColumnListing($relatedTable))
            ->reject(fn ($c) => in_array($c, ['id', 'password', 'email_verified_at', 'remember_token', 'created_at', 'updated_at']));

        $result = [];

        // Level 1 fields (e.g. user.name, user.email)
        foreach ($columns as $col) {
            $key = "{$relationPath}.{$col}";
            $label = Str::headline(str_replace('.', ' ', "{$relationPath} {$col}"));
            $result[$key] = $label;
        }

        // If nested → recurse
        if (!empty($parts)) {
            $nestedPath = implode('.', $parts);
            $nestedFields = $this->getRelationColumns($relatedModel, $nestedPath);

            foreach ($nestedFields as $nestedKey => $nestedLabel) {
                $result["{$relationName}.{$nestedKey}"] = $nestedLabel;
            }
        }

        return $result;
    }

    public function showFilter()
    {
        $this->filter = true;

        // check if filterBy not empty
        if(count($this->filterBy) == 0) {
            $this->filterBy[] = '';
            $this->query[] = '';
        }

    }

    public function closeFilter()
    {
        $this->filter = false;
        $this->filterDataSearch = false;
        $this->resetPage();
    }

    public function filterData()
    {
        $this->filterDataSearch = true;
        $this->reset('search');
        $this->sortField = $this->defaultSortField;
        $this->sortDirection = $this->defaultSortDirection;
        $this->resetPage();
    }

    public function addFilter()
    {
        // check if filterByColumn count == filterBy count
        // button should be disabled

        $this->filterBy[] = '';
        $this->query[] = '';

        if (count($this->filterByColumn) == count($this->query)) {
            $this->disabledAddFilterButton = true;
        }
    }

    public function resetFilter()
    {
        // reset filterBy and query
        // just add one filterBy for default
        // reset disabledAddFilterButton

        $this->resetPage();
        $this->filterBy = [''];
        $this->query = [''];
        $this->disabledAddFilterButton = false;
        $this->filterDataSearch = false;
    }

    public function deleteFilter($index)
    {
        if (isset($this->filterBy[$index])) {
            unset($this->filterBy[$index]);
        }

        if (isset($this->query[$index])) {
            unset($this->query[$index]);
        }

        // Re-index array to avoid gaps
        $this->filterBy = array_values($this->filterBy);
        $this->query = array_values($this->query);
    }

    protected function initializeDataSource(): void
    {
        if ($this->model) {
            $this->dataSource = new ModelDataSource($this->model, $this->scope, $this->scopeParams, $this->searchable, $this->sortable, $this->perPage, $this->defaultSortField, $this->defaultSortDirection);
        } else {
            $this->dataSource = new ApiDataSource($this->apiConfig);
        }
    }

    #[Computed]
    protected function getQuery()
    {
        // check if filterBy not empty
        if($this->filterDataSearch) {
            $query = $this->model::query();
            foreach ($this->filterBy as $i => $column) {

                $value = trim($this->query[$i]) ?? null;
                $this->query[$i] = $value;
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

            $result = $query->paginate($this->perPage, ['*'], 'page', $this->page);
        } else {

            $this->ensureDataSourceInitialized();
            $this->search = trim($this->search);
            $result = $this->dataSource->getData([
                'search' => $this->search,
                'sort_field' => $this->sortField,
                'sort_direction' => $this->sortDirection,
                'per_page' => $this->perPage,
                'page' => $this->page,
            ]);
        }

        // For simple pagination, store the total in the component
        if (config('livewire-datatable.default_pagination') === 'simplePaginate') {
            $this->totals = $result->total ?? $result->total();
        }

        return $result;
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
