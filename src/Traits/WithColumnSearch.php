<?php

namespace Developerawam\LivewireDatatable\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Trait WithColumnSearch
 *
 * FIX: bootWithColumnSearch() dihapus.
 * $perColumnSearch adalah Livewire public property — nilainya dipertahankan
 * antar request. boot() yang menulis ulang dari config akan reset state
 * yang sudah di-set saat mount().
 */
trait WithColumnSearch
{
    /** Per-column search terms: ['column_key' => 'search_value'] */
    public array $columnSearch = [];

    /** Whether per-column search inputs are shown — diset saat mount(), dipertahankan Livewire */
    public bool $perColumnSearch = false;

    public function updatedColumnSearch(): void
    {
        $this->resetPage();
    }

    public function clearColumnSearch(): void
    {
        $this->columnSearch = [];
        $this->resetPage();
    }

    public function clearColumnSearchFor(string $column): void
    {
        $encodedKey = str_replace('.', '___', $column);
        unset($this->columnSearch[$encodedKey]);
        $this->columnSearch = $this->columnSearch;
        $this->resetPage();
    }

    protected function applyColumnSearch(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        foreach ($this->columnSearch as $rawKey => $term) {
            // Decode encoded dot separator back to real dot
            $column = str_replace('___', '.', $rawKey);

            // Guard: skip jika term bukan string (nested array akibat dot notation leak)
            if (!is_string($term)) {
                continue;
            }

            $term = trim($term);
            if (!$term) {
                continue;
            }

            if (str_contains($column, '.')) {
                $parts = explode('.', $column);
                $field = array_pop($parts);
                $relationPath = implode('.', $parts);

                $query->whereHas($relationPath, function ($q) use ($field, $term) {
                    $q->where($field, 'LIKE', "%{$term}%");
                });
            } else {
                $query->where($column, 'LIKE', "%{$term}%");
            }
        }

        return $query;
    }

    /**
     * Schema cache — tidak terpengaruh state, tetap aman di sini.
     */
    protected function getCachedColumnListing(string $table): array
    {
        $ttl = config('livewire-datatable.schema_cache_ttl', 300);

        if ($ttl <= 0) {
            return Schema::getColumnListing($table);
        }

        return Cache::remember(
            "datatable_schema_{$table}",
            $ttl,
            fn () => Schema::getColumnListing($table)
        );
    }

    protected function buildFilterByColumn(): array
    {
        if (!$this->model) {
            return [];
        }

        $model = new $this->model;
        $table = $model->getTable();

        $columns = collect($this->getCachedColumnListing($table))
            ->reject(fn ($c) => in_array($c, ['id', 'created_at', 'updated_at']))
            ->mapWithKeys(fn ($c) => [$c => Str::headline($c)])
            ->toArray();

        foreach (array_keys($model->getEagerLoads()) as $relationPath) {
            $relationColumns = $this->getRelationColumns($model, $relationPath);
            foreach ($relationColumns as $key => $label) {
                $columns[$key] = $label;
            }
        }

        return $columns;
    }
}
