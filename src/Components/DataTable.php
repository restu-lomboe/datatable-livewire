<?php

namespace Developerawam\LivewireDatatable\Components;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;
use Developerawam\LivewireDatatable\Traits\WithFormatters;
use Developerawam\LivewireDatatable\Traits\WithExport;
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


    protected function ensureDataSourceInitialized(): void
    {
        if (!$this->dataSource) {
            $this->initializeDataSource();
        }
    }

    public function mount($model = null, $apiConfig = null, $scope = null, $columns = [], $scopeParams = [], $searchable = [], $unsortable = [], $theme = [], $customColumns = [], $formatters = [], $formatterOptions = [])
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
        // By default, all columns are sortable except those in unsortable array
        $this->sortable = array_diff(array_keys($columns), $unsortable);

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

    protected function initializeDataSource(): void
    {
        if ($this->model) {
            $this->dataSource = new ModelDataSource($this->model, $this->scope, $this->scopeParams, $this->searchable, $this->sortable, $this->perPage);
        } else {
            $this->dataSource = new ApiDataSource($this->apiConfig);
        }
    }

    #[Computed]
    protected function getQuery()
    {
        $this->ensureDataSourceInitialized();

        $result = $this->dataSource->getData([
            'search' => $this->search,
            'sort_field' => $this->sortField,
            'sort_direction' => $this->sortDirection,
            'per_page' => $this->perPage,
            'page' => $this->page,
        ]);

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
