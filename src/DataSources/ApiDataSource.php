<?php

namespace Developerawam\LivewireDatatable\DataSources;

use Illuminate\Support\Collection;
use Illuminate\Http\Client\Factory as Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;

class ApiDataSource implements DataSourceInterface
{
    protected $url;
    protected $method;
    protected $headers;
    protected $queryParams;
    protected $responseKey;
    protected $perPage;
    protected $currentPage;
    protected $totalKey;
    protected $dataKey;
    protected $searchParam;
    protected $sortParam;
    protected $sortDirectionParam;
    protected $perPageParam;
    protected $pageParam;
    protected Http $http;

    public function __construct(array $config)
    {
        $this->http = new Http();
        $this->url = $config['url'];
        $this->method = $config['method'] ?? 'GET';
        $this->headers = $config['headers'] ?? [];
        $this->queryParams = $config['query_params'] ?? [];
        $this->responseKey = $config['response_key'] ?? null;
        $this->perPage = $config['per_page'] ?? 10;
        $this->currentPage = $config['current_page'] ?? 1;
        $this->totalKey = $config['total_key'] ?? 'total';
        $this->dataKey = $config['data_key'] ?? 'data';
        $this->searchParam = $config['search_param'] ?? 'search';
        $this->sortParam = $config['sort_param'] ?? 'sort';
        $this->sortDirectionParam = $config['sort_direction_param'] ?? 'direction';
        $this->perPageParam = $config['per_page_param'] ?? 'per_page';
        $this->pageParam = $config['page_param'] ?? 'page';
        $this->currentPageParam = $config['current_page_param'] ?? 'current_page';
    }

    protected function makeRequest(array $queryParams = []): array
    {
        $response = $this->http
            ->withHeaders($this->headers)
            ->{strtolower($this->method)}($this->url, $queryParams);

        if (!$response->successful()) {
            throw new \RuntimeException('API request failed: ' . $response->body());
        }

        $data = $response->json();

        if ($this->responseKey) {
            $data = Arr::get($data, $this->responseKey);
        }

        return $data;
    }

    public function getData(array $params = []): LengthAwarePaginator|Paginator
    {
        $perPage = $params['per_page'] ?? $this->perPage;
        $isAllRecords = $perPage === 'all' || $perPage === null || $perPage === -1;
        $page = $params['page'] ?? $this->currentPage;

        // Base query parameters
        $baseParams = [
            $this->searchParam => $params['search'] ?? '',
            $this->sortParam => $params['sort_field'] ?? '',
            $this->sortDirectionParam => $params['sort_direction'] ?? '',
        ];

        if ($isAllRecords) {
            // First, get total count if requesting all records
            $countParams = array_merge($this->queryParams, $baseParams, [
                $this->perPageParam => 1,
                $this->pageParam => 1,
            ]);

            $countData = $this->makeRequest($countParams);
            $total = Arr::get($countData, $this->totalKey, 0);

            // Now get all records in a single request
            $queryParams = array_merge($this->queryParams, $baseParams, [
                $this->perPageParam => $total > 0 ? $total : 1,
                $this->pageParam => 1,
            ]);
        } else {
            $queryParams = array_merge($this->queryParams, $baseParams, [
                $this->perPageParam => $perPage,
                $this->pageParam => $page,
            ]);
        }

        $response = $this->makeRequest($queryParams);

        $formatter = new ApiResponseFormatter([
            'data_key' => $this->dataKey,
            'total_key' => $this->totalKey,
            'response_key' => $this->responseKey,
            'per_page_key' => $this->perPageParam,
            'current_page_key' => $this->currentPageParam,
        ]);

        $formatted = $formatter->format($response);

        $paginationType = config('livewire-datatable.default_pagination', 'default');
        $paginationOptions = [
            'path' => request()->url(),
            'pageName' => 'page',
            'query' => [
                'search' => $queryParams[$this->searchParam] ?? null,
                'sort' => $queryParams[$this->sortParam] ?? null,
                'direction' => $queryParams[$this->sortDirectionParam] ?? null,
                'per_page' => $queryParams[$this->perPageParam] ?? null,
            ]
        ];

        if ($paginationType === 'simplePaginate') {
            $paginator = new Paginator(
                $formatted['data'],
                $formatted['meta']['per_page'],
                $formatted['meta']['current_page'],
                $paginationOptions
            );

            // Add total count to the simple paginator for consistency
            $paginator->total = $formatted['meta']['total'];

            // Calculate if there are more pages
            $currentItems = count($formatted['data']);
            $currentPage = $formatted['meta']['current_page'];
            $perPage = $formatted['meta']['per_page'];
            $total = $formatted['meta']['total'];

            // Set hasMorePagesWhen based on total count and current page
            $paginator->hasMorePagesWhen(
                ($currentPage * $perPage) < $total
            );
        } else {
            $paginator = new LengthAwarePaginator(
                $formatted['data'],
                $formatted['meta']['total'],
                $formatted['meta']['per_page'],
                $formatted['meta']['current_page'],
                $paginationOptions
            );
        }

        // Set current path for pagination links
        $paginator->withPath(request()->url());

        return $paginator;
    }

    public function search(string $term): Collection
    {
        $params = array_merge($this->queryParams, [
            $this->searchParam => $term,
            $this->perPageParam => $this->perPage,
        ]);

        $response = $this->makeRequest($params);

        return collect(Arr::get($response, $this->dataKey, []));
    }

    public function sort(string $field, string $direction): Collection
    {
        $params = array_merge($this->queryParams, [
            $this->sortParam => $field,
            $this->sortDirectionParam => $direction,
            $this->perPageParam => $this->perPage,
        ]);

        $response = $this->makeRequest($params);

        return collect(Arr::get($response, $this->dataKey, []));
    }

    public static function make(array $config): self
    {
        return new static($config);
    }
}
