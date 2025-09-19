<?php

namespace Developerawam\LivewireDatatable\DataSources;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;

interface DataSourceInterface
{
    /**
     * Get paginated data based on the provided parameters
     *
     * @param array $params
     * @return LengthAwarePaginator|Paginator
     */
    public function getData(array $params = []): LengthAwarePaginator|Paginator;

    /**
     * Search through the data source
     *
     * @param string $term
     * @return Collection
     */
    public function search(string $term): Collection;

    /**
     * Sort the data source
     *
     * @param string $field
     * @param string $direction
     * @return Collection
     */
    public function sort(string $field, string $direction): Collection;
}
