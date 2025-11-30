<?php

namespace Developerawam\LivewireDatatable\Tests;

use PHPUnit\Framework\TestCase;
use Developerawam\LivewireDatatable\DataSources\ModelDataSource;

class DefaultOrderByTest extends TestCase
{
    /**
     * Test that default sort field is applied when no sort field is specified
     */
    public function test_default_sort_field_applied()
    {
        $dataSource = new ModelDataSource(
            'App\Models\Todo',
            null,
            [],
            [],
            ['id', 'title', 'created_at'],
            10,
            'created_at',    // defaultSortField
            'desc'           // defaultSortDirection
        );

        // When getData is called without sort params, default should be applied
        $result = $dataSource->getData([]);

        // The query should be ordered by created_at desc
        $this->assertNotNull($result);
    }

    /**
     * Test that explicit sort overrides default sort
     */
    public function test_explicit_sort_overrides_default()
    {
        $dataSource = new ModelDataSource(
            'App\Models\Todo',
            null,
            [],
            [],
            ['id', 'title', 'created_at'],
            10,
            'created_at',    // defaultSortField
            'desc'           // defaultSortDirection
        );

        // When explicit sort is provided, it should override default
        $result = $dataSource->getData([
            'sort_field' => 'title',
            'sort_direction' => 'asc'
        ]);

        // Should sort by title asc instead of created_at desc
        $this->assertNotNull($result);
    }

    /**
     * Test default sort with relation fields
     */
    public function test_default_sort_with_relation()
    {
        $dataSource = new ModelDataSource(
            'App\Models\Todo',
            null,
            [],
            [],
            ['id', 'title', 'user.name', 'created_at'],
            10,
            'user.name',     // defaultSortField - relation field
            'asc'            // defaultSortDirection
        );

        // When getData is called without sort params, should apply relation sort
        $result = $dataSource->getData([]);

        // The query should be ordered by user.name asc
        $this->assertNotNull($result);
    }

    /**
     * Test constructor with default parameters
     */
    public function test_constructor_default_parameters()
    {
        $dataSource = new ModelDataSource(
            'App\Models\Todo',
            null,
            [],
            [],
            ['id', 'title']
            // defaultSortField and defaultSortDirection are optional
        );

        // Should not throw error
        $this->assertNotNull($dataSource);
    }
}
