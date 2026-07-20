<?php

namespace Developerawam\LivewireDatatable\DataSources;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

interface DataSourceInterface
{
    /**
     * Get paginated data based on the provided parameters
     */
    public function getData(array $params = []): LengthAwarePaginator|Paginator;

    /**
     * Search through the data source
     */
    public function search(string $term): Collection;

    /**
     * Sort the data source
     */
    public function sort(string $field, string $direction): Collection;
}
