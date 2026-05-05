<?php

namespace Developerawam\LivewireDatatable\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait WithFiltering
 *
 * Improvement #1: Centralize filter logic yang sebelumnya duplikat
 * antara DataTable::getQuery() dan WithExport::export().
 *
 * Improvement #3: Perbaikan nama property showFiterButton → showFilterButton
 */
trait WithFiltering
{
    /**
     * Build query with active filters applied.
     * Used by both getQuery() and export() to avoid code duplication.
     */
    protected function buildFilteredQuery(): Builder
    {
        $query = $this->model::query();

        // Apply scope
        if (!empty($this->scopeParams)) {
            $query = $query->{$this->scope}(...$this->scopeParams);
        } elseif ($this->scope) {
            $query = $query->{$this->scope}();
        }

        // Apply each active filter
        foreach ($this->filterBy as $i => $column) {
            $value = trim($this->query[$i] ?? '');
            $this->query[$i] = $value;

            if (!$value || !$column) {
                continue;
            }

            if (str_contains($column, '.')) {
                // Relation filter: user.name, user.profile.country
                $parts = explode('.', $column);
                $field = array_pop($parts);
                $relationPath = implode('.', $parts);

                $query->whereHas($relationPath, function ($q) use ($field, $value, $column) {
                    $q->where($field, $this->resolveFilterOperator($column, $value), $this->resolveFilterValue($column, $value));
                });
            } else {
                // Direct column filter with smart operator based on cast type
                $query->where(
                    $column,
                    $this->resolveFilterOperator($column, $value),
                    $this->resolveFilterValue($column, $value)
                );
            }
        }

        // Apply sorting
        if (!empty($this->sortField) && in_array($this->sortField, $this->sortable)) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } elseif ($this->defaultSortField) {
            $query->orderBy($this->defaultSortField, $this->defaultSortDirection);
        }

        return $query;
    }

    /**
     * Improvement #7: Detect cast type to use smart operator.
     * Boolean/integer columns use '=' instead of LIKE.
     */
    protected function resolveFilterOperator(string $column, mixed $value): string
    {
        $casts = $this->getModelCasts();
        $castType = $casts[$column] ?? null;

        if (in_array($castType, ['boolean', 'bool', 'integer', 'int', 'float', 'double'])) {
            return '=';
        }

        return 'LIKE';
    }

    /**
     * Improvement #7: Format filter value based on column cast type.
     */
    protected function resolveFilterValue(string $column, mixed $value): mixed
    {
        $casts = $this->getModelCasts();
        $castType = $casts[$column] ?? null;

        if (in_array($castType, ['boolean', 'bool'])) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        if (in_array($castType, ['integer', 'int'])) {
            return (int) $value;
        }

        if (in_array($castType, ['float', 'double'])) {
            return (float) $value;
        }

        return '%' . $value . '%';
    }

    /**
     * Get model $casts array safely.
     */
    protected function getModelCasts(): array
    {
        if (!$this->model) {
            return [];
        }

        try {
            return (new $this->model)->getCasts();
        } catch (\Throwable) {
            return [];
        }
    }

    public function showFilter(): void
    {
        $this->filter = true;

        if (count($this->filterBy) === 0) {
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
        $this->filterBy[] = '';
        $this->query[] = '';

        if (count($this->filterByColumn) === count($this->query)) {
            $this->disabledAddFilterButton = true;
        }
    }

    public function resetFilter(): void
    {
        $this->resetPage();
        $this->filterBy = [''];
        $this->query = [''];
        $this->disabledAddFilterButton = false;
        $this->filterDataSearch = false;
    }

    public function deleteFilter(int $index): void
    {
        if (count($this->filterByColumn) === count($this->query)) {
            $this->disabledAddFilterButton = false;
        }

        unset($this->filterBy[$index], $this->query[$index]);

        $this->filterBy = array_values($this->filterBy);
        $this->query = array_values($this->query);
    }
}
