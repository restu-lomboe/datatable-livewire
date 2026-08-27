<?php

namespace Developerawam\LivewireDatatable\Components;

use Developerawam\LivewireDatatable\DataSources\ApiDataSource;
use Developerawam\LivewireDatatable\DataSources\ModelDataSource;
use Developerawam\LivewireDatatable\Traits\WithExport;
use Developerawam\LivewireDatatable\Traits\WithFormatters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
class DataTable extends Component
{
    use WithExport, WithFormatters, WithPagination;

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

    public $showCustomExport = false;

    public $customExportColumns = [];

    public $customExportType = 'excel';

    public $customExportPaperSize = 'a4';

    public $customExportOrientation = 'portrait';

    protected $dataSource;

    public $filter = false;

    public $showFiterButton = false;

    public $filterDataSearch = false;

    public $filterBy = [];

    public $query = [];

    public $disabledAddFilterButton = false;

    public $showDateFilter = false;

    public $dateFilterColumn = '';

    public $dateFilterStart = '';

    public $dateFilterEnd = '';

    public $dateFilterEnabled = false;

    protected function ensureDataSourceInitialized(): void
    {
        if (! $this->dataSource) {
            $this->initializeDataSource();
        }
    }

    public function mount($model = null, $apiConfig = null, $scope = null, $columns = [], $scopeParams = [], $searchable = [], $unsortable = [], $theme = [], $customColumns = [], $formatters = [], $formatterOptions = [], $defaultSortField = 'created_at', $defaultSortDirection = 'desc'): void
    {
        if (! $model && ! $apiConfig) {
            throw new \InvalidArgumentException('Either model or apiConfig must be provided');
        }

        if ($model) {
            if (! class_exists($model)) {
                throw new \InvalidArgumentException("Model class [{$model}] does not exist.");
            }
            if (! is_subclass_of($model, Model::class)) {
                throw new \InvalidArgumentException("Model [{$model}] must be an Eloquent Model.");
            }
            if ($scope && ! method_exists($model, 'scope'.Str::studly($scope))) {
                throw new \InvalidArgumentException("Scope [{$scope}] does not exist on model [{$model}].");
            }
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

        // Load theme from config based on template and merge with any custom theme passed
        $template = config('livewire-datatable.template', 'tailwind');
        $themeKey = $template === 'bootstrap' ? 'bootstrap_theme' : 'theme';
        $this->theme = array_merge(config("livewire-datatable.{$themeKey}", []), $theme);

        // Initialize export settings from config
        $this->enableExport = config('livewire-datatable.export.enabled', true);
        $this->exportTypes = config('livewire-datatable.export.types', ['excel', 'pdf']);

        // show filter button
        $this->showFiterButton = config('livewire-datatable.advanced_filter', true);

        // Initialize the appropriate data source
        $this->initializeDataSource();
    }

    public function getClass(string $element): string
    {
        return $this->theme[$element] ?? '';
    }

    /**
     * Cached column listing to avoid repeated information_schema queries.
     */
    protected function cachedColumnListing(string $table): array
    {
        $ttl = (int) config('livewire-datatable.schema_cache_ttl', 3600);

        // Use in-memory static cache per request + persistent cache for cross-request
        static $memory = [];

        if (isset($memory[$table])) {
            return $memory[$table];
        }

        $key = "livewire-datatable:schema:{$table}:columns";

        if ($ttl <= 0) {
            $columns = Schema::getColumnListing($table);

            return $memory[$table] = $columns;
        }

        try {
            $columns = Cache::remember($key, $ttl, fn () => Schema::getColumnListing($table));
        } catch (\Throwable $e) {
            $columns = Schema::getColumnListing($table);
        }

        return $memory[$table] = $columns;
    }

    protected function cachedColumnType(string $table, string $column): string
    {
        $ttl = (int) config('livewire-datatable.schema_cache_ttl', 3600);
        $key = "livewire-datatable:schema:{$table}:type:{$column}";

        if ($ttl <= 0) {
            return Schema::getColumnType($table, $column);
        }

        try {
            return Cache::remember($key, $ttl, fn () => Schema::getColumnType($table, $column));
        } catch (\Throwable $e) {
            return Schema::getColumnType($table, $column);
        }
    }

    public static function clearSchemaCache(?string $table = null): void
    {
        if ($table) {
            Cache::forget("livewire-datatable:schema:{$table}:columns");

            // Column types are wildcard; clear via pattern is driver-dependent, so just forget listing
            return;
        }

        // Best-effort: clear all datatable schema keys if cache supports tags/pattern
        try {
            if (method_exists(Cache::getStore(), 'flush')) {
                // Do not flush entire cache; only forget known tables from models is safer
            }
        } catch (\Throwable $e) {
            // ignore
        }
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

    public function updatingSearch(): void
    {
        $this->ensureDataSourceInitialized();
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->ensureDataSourceInitialized();
        $this->resetPage();
    }

    public function updatingPage($page): void
    {
        $this->page = $page;
    }

    #[On('reset-table')]
    public function resetTable(): void
    {
        $this->resetPage();
    }

    public function showCustomExportPanel(): void
    {
        $this->customExportColumns = array_keys($this->exportColumns);
        $this->customExportType = 'excel';
        $this->customExportPaperSize = config('livewire-datatable.export.paper_size', 'a4');
        $this->customExportOrientation = config('livewire-datatable.export.orientation', 'portrait');
        $this->showCustomExport = true;
    }

    public function closeCustomExport(): void
    {
        $this->showCustomExport = false;
        $this->customExportColumns = [];
    }

    public function toggleCustomExportColumn(string $column): void
    {
        if (in_array($column, $this->customExportColumns)) {
            $this->customExportColumns = array_values(array_filter($this->customExportColumns, fn ($c) => $c !== $column));
        } else {
            $this->customExportColumns[] = $column;
        }
    }

    public function selectAllExportColumns(): void
    {
        $this->customExportColumns = array_keys($this->exportColumns);
    }

    public function deselectAllExportColumns(): void
    {
        $this->customExportColumns = [];
    }

    public function customExport()
    {
        $this->validate([
            'customExportColumns' => 'required|array|min:1',
            'customExportType' => 'required|in:excel,pdf',
        ]);

        $columns = collect($this->exportColumns)
            ->filter(fn ($label, $key) => in_array($key, $this->customExportColumns))
            ->toArray();

        $type = $this->customExportType;
        $paperSize = $this->customExportPaperSize;
        $orientation = $this->customExportOrientation;

        $this->closeCustomExport();

        return $this->export($type, $columns, $paperSize, $orientation);
    }

    #[Computed]
    protected function exportColumns(): array
    {
        if (! $this->model) {
            return [];
        }

        $model = new $this->model;
        $table = $model->getTable();

        $exclude = ['id', 'updated_at', 'deleted_at', 'password', 'remember_token'];

        $columns = collect($this->cachedColumnListing($table))
            ->reject(fn ($c) => in_array($c, $exclude))
            ->mapWithKeys(fn ($c) => [$c => Str::headline($c)])
            ->toArray();

        foreach (array_keys($model->getEagerLoads()) as $relationPath) {
            $relationColumns = $this->getRelationExportColumns($model, $relationPath);

            foreach ($relationColumns as $key => $label) {
                $columns[$key] = $label;
            }
        }

        return $columns;
    }

    protected function getRelationExportColumns($model, string $relationPath): array
    {
        $parts = explode('.', $relationPath);
        $relationName = array_shift($parts);

        if (! method_exists($model, $relationName)) {
            return [];
        }

        $relation = $model->{$relationName}();
        $relatedModel = $relation->getRelated();
        $relatedTable = $relatedModel->getTable();

        $exclude = ['id', 'updated_at', 'deleted_at', 'password', 'remember_token'];

        $columns = collect($this->cachedColumnListing($relatedTable))
            ->reject(fn ($c) => in_array($c, $exclude));

        $result = [];

        foreach ($columns as $col) {
            $key = "{$relationPath}.{$col}";
            $label = Str::headline(str_replace('.', ' ', "{$relationPath} {$col}"));
            $result[$key] = $label;
        }

        if (! empty($parts)) {
            $nestedPath = implode('.', $parts);
            $nestedFields = $this->getRelationExportColumns($relatedModel, $nestedPath);

            foreach ($nestedFields as $nestedKey => $nestedLabel) {
                $result["{$relationName}.{$nestedKey}"] = $nestedLabel;
            }
        }

        return $result;
    }

    #[Computed]
    protected function filterByColumn(): array
    {
        $model = new $this->model;
        $table = $model->getTable();

        // MAIN TABLE COLUMNS
        $columns = collect($this->cachedColumnListing($table))
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

    protected function getRelationColumns($model, string $relationPath): array
    {
        $parts = explode('.', $relationPath);
        $relationName = array_shift($parts);

        if (! method_exists($model, $relationName)) {
            return [];
        }

        $relation = $model->{$relationName}();
        $relatedModel = $relation->getRelated();
        $relatedTable = $relatedModel->getTable();

        // Get columns for this table
        $columns = collect($this->cachedColumnListing($relatedTable))
            ->reject(fn ($c) => in_array($c, ['id', 'password', 'email_verified_at', 'remember_token', 'created_at', 'updated_at']));

        $result = [];

        // Level 1 fields (e.g. user.name, user.email)
        foreach ($columns as $col) {
            $key = "{$relationPath}.{$col}";
            $label = Str::headline(str_replace('.', ' ', "{$relationPath} {$col}"));
            $result[$key] = $label;
        }

        // If nested → recurse
        if (! empty($parts)) {
            $nestedPath = implode('.', $parts);
            $nestedFields = $this->getRelationColumns($relatedModel, $nestedPath);

            foreach ($nestedFields as $nestedKey => $nestedLabel) {
                $result["{$relationName}.{$nestedKey}"] = $nestedLabel;
            }
        }

        return $result;
    }

    public function showFilter(): void
    {
        $this->filter = true;

        // check if filterBy not empty
        if (count($this->filterBy) == 0) {
            $this->filterBy[] = '';
            $this->query[] = '';
        }

    }

    public function closeFilter(): void
    {
        $this->filter = false;
        $this->filterDataSearch = false;
        $this->resetPage();
    }

    public function filterData(): void
    {
        $this->filterDataSearch = true;
        $this->reset('search');
        $this->sortField = $this->defaultSortField;
        $this->sortDirection = $this->defaultSortDirection;
        $this->resetPage();
    }

    public function addFilter(): void
    {
        // check if filterByColumn count == filterBy count
        // button should be disabled

        $this->filterBy[] = '';
        $this->query[] = '';

        if (count($this->filterByColumn) == count($this->query)) {
            $this->disabledAddFilterButton = true;
        }
    }

    public function resetFilter(): void
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

    public function deleteFilter(int $index): void
    {
        if (count($this->filterByColumn) == count($this->query)) {
            $this->disabledAddFilterButton = false;
        }

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

    #[Computed]
    protected function dateFilterableColumns(): array
    {
        if (! $this->model) {
            return [];
        }

        $columns = [];
        $model = new $this->model;
        $table = $model->getTable();

        foreach ($this->cachedColumnListing($table) as $column) {
            $type = $this->cachedColumnType($table, $column);
            if (in_array($type, ['date', 'datetime', 'timestamp'])) {
                $columns[$column] = Str::headline($column);
            }
        }

        foreach (array_keys($model->getEagerLoads()) as $relationPath) {
            $relationColumns = $this->getDateFilterRelationColumns($model, $relationPath);
            foreach ($relationColumns as $key => $label) {
                $columns[$key] = $label;
            }
        }

        return $columns;
    }

    protected function getDateFilterRelationColumns($model, string $relationPath): array
    {
        $parts = explode('.', $relationPath);
        $relationName = array_shift($parts);

        if (! method_exists($model, $relationName)) {
            return [];
        }

        $relation = $model->{$relationName}();
        $relatedModel = $relation->getRelated();
        $relatedTable = $relatedModel->getTable();

        $columns = collect($this->cachedColumnListing($relatedTable))
            ->filter(fn ($col) => in_array($this->cachedColumnType($relatedTable, $col), ['date', 'datetime', 'timestamp']));

        $result = [];
        foreach ($columns as $col) {
            $key = "{$relationPath}.{$col}";
            $label = Str::headline(str_replace('.', ' ', "{$relationPath} {$col}"));
            $result[$key] = $label;
        }

        if (! empty($parts)) {
            $nestedPath = implode('.', $parts);
            $nestedFields = $this->getDateFilterRelationColumns($relatedModel, $nestedPath);
            foreach ($nestedFields as $nestedKey => $nestedLabel) {
                $result["{$relationName}.{$nestedKey}"] = $nestedLabel;
            }
        }

        return $result;
    }

    public function showDateFilterPanel(): void
    {
        $this->showDateFilter = true;
    }

    public function closeDateFilterPanel(): void
    {
        $this->showDateFilter = false;
    }

    public function updatedDateFilterEnd($value): void
    {
        if ($this->dateFilterStart && $value && $value < $this->dateFilterStart) {
            $this->addError('dateFilterEnd', 'End date must be greater than or equal to start date.');
        } elseif ($this->dateFilterStart && $value) {
            $this->resetValidation('dateFilterEnd');
        }
    }

    public function updatedDateFilterStart(): void
    {
        if ($this->dateFilterEnd && $this->dateFilterStart && $this->dateFilterEnd < $this->dateFilterStart) {
            $this->addError('dateFilterEnd', 'End date must be greater than or equal to start date.');
        } elseif ($this->getErrorBag()->has('dateFilterEnd')) {
            $this->resetValidation('dateFilterEnd');
        }
    }

    public function applyDateFilter(): void
    {
        $this->validate([
            'dateFilterColumn' => 'required|string',
            'dateFilterStart' => 'nullable|date',
            'dateFilterEnd' => ['nullable', 'date', function ($attribute, $value, $fail) {
                if ($this->dateFilterStart && $value && $value < $this->dateFilterStart) {
                    $fail('End date must be greater than or equal to start date.');
                }
            }],
        ]);

        if (! $this->dateFilterStart && ! $this->dateFilterEnd) {
            return;
        }

        $this->dateFilterEnabled = true;
        $this->showDateFilter = false;
        $this->resetPage();
    }

    public function resetDateFilter(): void
    {
        $this->dateFilterEnabled = false;
        $this->dateFilterColumn = '';
        $this->dateFilterStart = '';
        $this->dateFilterEnd = '';
        $this->resetValidation(['dateFilterStart', 'dateFilterEnd']);
        $this->resetPage();
    }

    protected function applyDateRangeQuery($query, string $column): void
    {
        // Use whereDate to keep behavior consistent for date/datetime/timestamp,
        // but handle time boundaries correctly for datetime columns
        if ($this->dateFilterStart && $this->dateFilterEnd) {
            $query->whereDate($column, '>=', $this->dateFilterStart)
                ->whereDate($column, '<=', $this->dateFilterEnd);
        } elseif ($this->dateFilterStart) {
            $query->whereDate($column, '>=', $this->dateFilterStart);
        } elseif ($this->dateFilterEnd) {
            $query->whereDate($column, '<=', $this->dateFilterEnd);
        }
    }

    protected function initializeDataSource(): void
    {
        if ($this->model) {
            $this->dataSource = new ModelDataSource($this->model, $this->scope, $this->scopeParams, $this->searchable, $this->sortable, $this->perPage, $this->defaultSortField, $this->defaultSortDirection);
        } else {
            $this->dataSource = new ApiDataSource($this->apiConfig);
        }
    }

    /**
     * Build base filtered query — single source of truth for listing & export.
     * Used by getQuery() and WithExport::export() to avoid duplication.
     */
    public function buildFilteredQuery(): Builder
    {
        $query = $this->model::query();

        // Apply scope if exists
        if (! empty($this->scopeParams)) {
            $query = $query->{$this->scope}(...$this->scopeParams);
        } elseif ($this->scope) {
            $query = $query->{$this->scope}();
        }

        // Apply advanced filters (if active)
        if ($this->filterDataSearch) {
            foreach ($this->filterBy as $i => $column) {
                $value = trim((string) ($this->query[$i] ?? '')) ?: null;
                // Normalize stored query value
                $this->query[$i] = $value ?? '';
                if (! $value) {
                    continue;
                }

                if (str_contains($column, '.')) {
                    $parts = explode('.', $column);
                    $field = array_pop($parts);
                    $relationPath = implode('.', $parts);
                    $query->whereHas($relationPath, fn ($q) => $q->where($field, 'LIKE', "%{$value}%"));
                } else {
                    $query->where($column, 'LIKE', "%{$value}%");
                }
            }
        }

        // Apply search when advanced filter is not active (covers date-filter + search case)
        if (! $this->filterDataSearch && ! empty($this->search)) {
            $search = trim((string) $this->search);
            $query->where(function ($q) use ($search) {
                foreach ($this->searchable as $field) {
                    if (str_contains($field, '.')) {
                        $parts = explode('.', $field);
                        $relationField = array_pop($parts);
                        $relations = $parts;
                        $q->orWhereHas($relations[0], function ($subQ) use ($relations, $relationField, $search) {
                            if (count($relations) > 1) {
                                $subQ->whereHas(implode('.', array_slice($relations, 1)), fn ($sq) => $sq->where($relationField, 'like', '%'.$search.'%'));
                            } else {
                                $subQ->where($relationField, 'like', '%'.$search.'%');
                            }
                        });
                    } else {
                        $q->orWhere($field, 'like', '%'.$search.'%');
                    }
                }
            });
        }

        // Apply date filter
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

        // Apply sorting
        if (! empty($this->sortField) && in_array($this->sortField, $this->sortable)) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } elseif ($this->defaultSortField) {
            $query->orderBy($this->defaultSortField, $this->defaultSortDirection);
        }

        return $query;
    }

    #[Computed]
    protected function getQuery()
    {
        $useManualQuery = $this->filterDataSearch || $this->dateFilterEnabled;

        if ($useManualQuery) {
            $query = $this->buildFilteredQuery();
            $result = $query->paginate($this->perPage, ['*'], 'page', $this->page);
        } else {
            $this->ensureDataSourceInitialized();
            $this->search = trim((string) $this->search);
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
        $template = config('livewire-datatable.template', 'tailwind');
        $viewName = match ($template) {
            'bootstrap' => 'livewire-datatable::placeholders.templates.bootstrap.datatable',
            'tailwind' => 'livewire-datatable::placeholders.templates.tailwind.datatable',
            default => 'livewire-datatable::placeholders.templates.tailwind.datatable',
        };

        return view($viewName);
    }

    public function render()
    {
        $template = config('livewire-datatable.template', 'tailwind');
        $viewName = match ($template) {
            'bootstrap' => 'livewire-datatable::templates.bootstrap.datatable',
            'tailwind' => 'livewire-datatable::templates.tailwind.datatable',
            default => 'livewire-datatable::templates.tailwind.datatable',
        };

        return view($viewName);
    }
}
