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
use Developerawam\LivewireDatatable\Traits\WithFiltering;
use Developerawam\LivewireDatatable\Traits\WithSelection;
use Developerawam\LivewireDatatable\Traits\WithColumnVisibility;
use Developerawam\LivewireDatatable\Traits\WithColumnSearch;
use Developerawam\LivewireDatatable\Traits\WithSavedFilters;
use Developerawam\LivewireDatatable\Traits\WithMultiSort;
use Developerawam\LivewireDatatable\DataSources\ApiDataSource;
use Developerawam\LivewireDatatable\DataSources\ModelDataSource;

#[Lazy]
class DataTable extends Component
{
    use WithPagination, WithFormatters, WithExport,
        WithFiltering, WithSelection, WithColumnVisibility,
        WithColumnSearch, WithSavedFilters, WithMultiSort;

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
    // #3 FIX: typo showFiterButton -> showFilterButton (backward compat property kept)
    public $showFilterButton = false;
    /** @deprecated Use $showFilterButton */
    public $showFiterButton = false;
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

    public function mount(
        $model = null, $apiConfig = null, $scope = null,
        $columns = [], $scopeParams = [], $searchable = [], $unsortable = [],
        $theme = [], $customColumns = [], $formatters = [], $formatterOptions = [],
        $defaultSortField = 'created_at', $defaultSortDirection = 'desc',
        bool $selectable = false, bool $perColumnSearch = false,
        bool $columnVisibility = false, bool $multiSort = false,
        bool $savedFilters = false,
    ): void {
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
        $this->sortable = array_values(array_diff(array_keys($columns), $unsortable));

        $this->pageOptions = config('livewire-datatable.per_page_options', [10, 25, 50, 100]);
        $this->perPage = $this->pageOptions[0] ?? 10;

        $template = config('livewire-datatable.template', 'tailwind');
        $themeKey = $template === 'bootstrap' ? 'bootstrap_theme' : 'theme';
        $this->theme = array_merge(config("livewire-datatable.{$themeKey}", []), $theme);

        $this->enableExport = config('livewire-datatable.export.enabled', true);
        $this->exportTypes = config('livewire-datatable.export.types', ['excel', 'pdf']);

        // #3 FIX: sync both properties for backward compat
        $this->showFilterButton = config('livewire-datatable.advanced_filter', true);
        $this->showFiterButton = $this->showFilterButton;

        // Optional new features — prop value takes priority over config default.
        // These are set ONCE in mount(). After that, Livewire preserves the values
        // automatically via its state hydration. No boot() method should overwrite them.
        $this->selectable      = $selectable      ?: config('livewire-datatable.selection.enabled', false);
        $this->perColumnSearch = $perColumnSearch ?: config('livewire-datatable.per_column_search', false);
        $this->columnVisibility= $columnVisibility?: config('livewire-datatable.column_visibility', false);
        $this->multiSort       = $multiSort       ?: config('livewire-datatable.multi_sort', false);
        $this->savedFilters    = $savedFilters    ?: config('livewire-datatable.saved_filters.enabled', false);

        // LoadPresets only on first mount (not on every Livewire re-render)
        if ($this->savedFilters) {
            $this->loadPresets();
        }

        $this->initializeDataSource();
    }

    public function getClass(string $element): string
    {
        return $this->theme[$element] ?? '';
    }

    public function sortBy(string $field): void
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

    public function updatingSearch(): void { $this->ensureDataSourceInitialized(); $this->resetPage(); }
    public function updatingPerPage(): void { $this->ensureDataSourceInitialized(); $this->resetPage(); }
    public function updatingPage($page): void { $this->page = $page; }

    #[On('reset-table')]
    public function resetTable(): void { $this->resetPage(); }

    // #4 FIX: use cached schema via WithColumnSearch
    #[Computed]
    protected function filterByColumn(): array
    {
        return $this->buildFilterByColumn();
    }

    protected function getRelationColumns($model, string $relationPath): array
    {
        $parts = explode('.', $relationPath);
        $relationName = array_shift($parts);
        if (!method_exists($model, $relationName)) return [];

        $relation = $model->{$relationName}();
        $relatedModel = $relation->getRelated();
        $relatedTable = $relatedModel->getTable();

        $columns = collect(Schema::getColumnListing($relatedTable))
            ->reject(fn ($c) => in_array($c, ['id', 'password', 'email_verified_at', 'remember_token', 'created_at', 'updated_at']));

        $result = [];
        foreach ($columns as $col) {
            $key = "{$relationPath}.{$col}";
            $result[$key] = Str::headline(str_replace('.', ' ', "{$relationPath} {$col}"));
        }

        if (!empty($parts)) {
            foreach ($this->getRelationColumns($relatedModel, implode('.', $parts)) as $k => $l) {
                $result["{$relationName}.{$k}"] = $l;
            }
        }
        return $result;
    }

    protected function initializeDataSource(): void
    {
        if ($this->model) {
            $this->dataSource = new ModelDataSource(
                $this->model, $this->scope, $this->scopeParams,
                $this->searchable, $this->sortable, $this->perPage,
                $this->defaultSortField, $this->defaultSortDirection
            );
        } else {
            $this->dataSource = new ApiDataSource($this->apiConfig);
        }
    }

    #[Computed]
    protected function getQuery()
    {
        $hasColumnSearch = $this->perColumnSearch && !empty(array_filter($this->columnSearch));

        if ($this->filterDataSearch) {
            $query = $this->buildFilteredQuery();
            if ($hasColumnSearch) {
                $query = $this->applyColumnSearch($query);
            }
            $result = $query->paginate($this->perPage, ['*'], 'page', $this->page);

        } elseif ($hasColumnSearch) {
            $this->ensureDataSourceInitialized();
            $result = $this->dataSource->getData([
                'search'         => $this->search,
                'sort_field'     => $this->sortField,
                'sort_direction' => $this->sortDirection,
                'sort_stack'     => $this->multiSort && !empty($this->sortStack) ? $this->sortStack : [],
                'per_page'       => $this->perPage,
                'page'           => $this->page,
                'column_search'  => $this->columnSearch,  // pass ke ModelDataSource
            ]);

        } else {
            $this->ensureDataSourceInitialized();
            $this->search = trim($this->search);
            $result = $this->dataSource->getData([
                'search'         => $this->search,
                'sort_field'     => $this->sortField,
                'sort_direction' => $this->sortDirection,
                'sort_stack'     => $this->multiSort && !empty($this->sortStack) ? $this->sortStack : [],
                'per_page'       => $this->perPage,
                'page'           => $this->page,
            ]);
        }

        if (config('livewire-datatable.default_pagination') === 'simplePaginate') {
            $this->totals = $result->total ?? $result->total();
        }

        return $result;
    }

    // #6: expose only visible columns to view
    public function getActiveColumnsProperty(): array
    {
        return $this->columnVisibility ? $this->getVisibleColumns() : $this->columns;
    }

    public function placeholder()
    {
        $template = config('livewire-datatable.template', 'tailwind');
        return view(match ($template) {
            'bootstrap' => 'livewire-datatable::placeholders.templates.bootstrap.datatable',
            default     => 'livewire-datatable::placeholders.templates.tailwind.datatable',
        });
    }

    public function render()
    {
        $template = config('livewire-datatable.template', 'tailwind');
        return view(match ($template) {
            'bootstrap' => 'livewire-datatable::templates.bootstrap.datatable',
            default     => 'livewire-datatable::templates.tailwind.datatable',
        });
    }
}
