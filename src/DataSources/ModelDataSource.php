<?php

namespace Developerawam\LivewireDatatable\DataSources;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModelDataSource implements DataSourceInterface
{
    protected $model;
    protected $scope;
    protected $searchable;
    protected $sortable;
    protected $perPage;

    public function __construct(string $model, ?string $scope = null, array $searchable = [], array $sortable = [], int $perPage = 10)
    {
        $this->model = $model;
        $this->scope = $scope;
        $this->searchable = $searchable;
        $this->sortable = $sortable;
        $this->perPage = $perPage;
    }

    public function getData(array $params = []): LengthAwarePaginator|\Illuminate\Pagination\Paginator
    {
        $query = $this->model::query();

        if ($this->scope) {
            $query = $query->{$this->scope}();
        }

        if (!empty($params['search']) && !empty($this->searchable)) {
            $query = $this->applySearch($query, $params['search']);
        }

        if (!empty($params['sort_field']) && in_array($params['sort_field'], $this->sortable)) {
            $query = $this->applySort($query, $params['sort_field'], $params['sort_direction'] ?? 'asc');
        }

        $paginationType = config('livewire-datatable.default_pagination', 'paginate');
        $perPage = $params['per_page'] ?? $this->perPage;
        $page = $params['page'] ?? 1;

        // Handle 'all' option by getting total count
        if ($perPage === 'all' || $perPage === -1) {
            // For export or show all cases, get total count for pagination
            $total = $query->count();

            if ($paginationType === 'simplePaginate') {
                $paginator = $query->simplePaginate($total, ['*'], 'page', 1);
                $paginator->total = $total;
                return $paginator;
            }

            return $query->paginate($total, ['*'], 'page', 1);
        }

        if ($paginationType === 'simplePaginate') {
            // Get total count before pagination
            $total = $query->count();

            // Get paginated results
            $paginator = $query->simplePaginate($perPage, ['*'], 'page', $page);

            // Add total to paginator instance
            $paginator->total = $total;

            return $paginator;
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function search(string $term): Collection
    {
        $query = $this->model::query();

        if ($this->scope) {
            $query = $query->{$this->scope}();
        }

        return $this->applySearch($query, $term)->get();
    }

    public function sort(string $field, string $direction): Collection
    {
        $query = $this->model::query();

        if ($this->scope) {
            $query = $query->{$this->scope}();
        }

        return $this->applySort($query, $field, $direction)->get();
    }

    protected function applySearch(Builder $query, string $searchTerm): Builder
    {
        return $query->where(function ($query) use ($searchTerm) {
            foreach ($this->searchable as $field) {
                if (Str::contains($field, '.')) {
                    [$relation, $relationField] = explode('.', $field);
                    $query->orWhereHas($relation, function ($query) use ($relationField, $searchTerm) {
                        $query->where($relationField, 'like', '%' . $searchTerm . '%');
                    });
                } else {
                    $query->orWhere($field, 'like', '%' . $searchTerm . '%');
                }
            }
        });
    }

    protected function applySort(Builder $query, string $field, string $direction): Builder
    {
        if (str_contains($field, '.')) {
            return $this->applySortWithRelation($query, $field, $direction);
        }

        return $query->orderBy($field, $direction);
    }

    protected function applySortWithRelation(Builder $query, string $field, string $direction): Builder
    {
        $parts = explode('.', $field);
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

            $modelInstance = $relationInstance->getRelated();
        }

        return $query->orderBy($relatedTable . '.' . $column, $direction);
    }

    public static function make(string $model, ?string $scope = null, array $searchable = [], array $sortable = [], int $perPage = 10): self
    {
        return new static($model, $scope, $searchable, $sortable, $perPage);
    }
}
