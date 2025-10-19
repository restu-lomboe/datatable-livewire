<?php

namespace Developerawam\LivewireDatatable\DataSources;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ApiResponseFormatter
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'data_key' => 'data',
            'total_key' => 'total',
            'response_key' => null,
            'per_page_key' => 'per_page',
            'current_page_key' => 'current_page',
        ], $config);
    }

    /**
     * Format API response data into a standard structure
     *
     * @param array $response The raw API response
     * @return array
     */
    public function format(array $response): array
    {
        $baseResponse = $this->config['response_key'] ?
            Arr::get($response, $this->config['response_key'], []) :
            $response;

        return [
            'data' => $this->extractData($baseResponse),
            'meta' => $this->extractMeta($baseResponse),
        ];
    }

    /**
     * Extract data from the response
     *
     * @param array $response
     * @return Collection
     */
    protected function extractData(array $response): Collection
    {
        $data = Arr::get($response, $this->config['data_key'], []);
        return collect($data)->map(function ($item) {
            return is_array($item) ? (object) $item : $item;
        });
    }

    /**
     * Extract metadata from the response
     *
     * @param array $response
     * @return array
     */
    protected function extractMeta(array $response): array
    {
        $meta = [];

        // Extract pagination information
        $meta['total'] = (int) Arr::get($response, $this->config['total_key'], 0);
        $meta['per_page'] = Arr::get($response, $this->config['per_page_key']);
        $meta['current_page'] = (int) Arr::get($response, $this->config['current_page_key'], 1);

        // Handle "all" or null per_page (show all records case)
        if ($meta['per_page'] === null || $meta['per_page'] === 'all') {
            $meta['per_page'] = $meta['total'];
            $meta['current_page'] = 1;
            $meta['last_page'] = 1;
            $meta['from'] = 1;
            $meta['to'] = $meta['total'];
        } else {
            $meta['per_page'] = (int) $meta['per_page'];
            // Calculate additional pagination metadata
            $meta['last_page'] = (int) ceil($meta['total'] / $meta['per_page']);
            $meta['from'] = ($meta['current_page'] - 1) * $meta['per_page'] + 1;
            $meta['to'] = min($meta['current_page'] * $meta['per_page'], $meta['total']);
        }

        return $meta;
    }

    /**
     * Create a new formatter instance with the given config
     *
     * @param array $config
     * @return static
     */
    public static function make(array $config = []): self
    {
        return new static($config);
    }
}
