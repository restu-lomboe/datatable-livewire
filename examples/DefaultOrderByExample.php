<?php

namespace Developerawam\LivewireDatatable\Examples;

/**
 * Example: Default Order By Feature
 *
 * This example demonstrates how to set default ordering for your datatable.
 * The datatable will automatically sort by the specified field and direction
 * when it first loads, or when no explicit sort is applied by the user.
 */

// Example 1: Basic Usage - Default sort by 'created_at' descending
class TodosTableBasic
{
    public function render()
    {
        return view('livewire.todos-table', [
            'model' => 'App\Models\Todo',
            'columns' => [
                'id' => '#',
                'title' => 'Title',
                'description' => 'Description',
                'status' => 'Status',
                'created_at' => 'Created At',
            ],
            'searchable' => ['title', 'description'],
            'defaultSortField' => 'created_at',      // Sort by created_at
            'defaultSortDirection' => 'desc',         // In descending order
        ]);
    }
}

// Example 2: Sort by a relation field
class TodosTableWithRelation
{
    public function render()
    {
        return view('livewire.todos-table', [
            'model' => 'App\Models\Todo',
            'columns' => [
                'id' => '#',
                'title' => 'Title',
                'status' => 'Status',
                'user.name' => 'User Name',          // Related model field
                'created_at' => 'Created At',
            ],
            'searchable' => ['title', 'user.name'],
            'defaultSortField' => 'user.name',       // Sort by related user's name
            'defaultSortDirection' => 'asc',         // In ascending order
        ]);
    }
}

// Example 3: Sort by status, then by created date
class TodosTableByStatus
{
    public function render()
    {
        return view('livewire.todos-table', [
            'model' => 'App\Models\Todo',
            'columns' => [
                'id' => '#',
                'title' => 'Title',
                'status' => 'Status',
                'created_at' => 'Created At',
            ],
            'searchable' => ['title'],
            'defaultSortField' => 'status',          // Primary sort by status
            'defaultSortDirection' => 'asc',
        ]);
    }
}

// Example 4: Complex example with all features
class UsersTableComplex
{
    public function render()
    {
        return view('livewire.users-table', [
            'model' => 'App\Models\User',
            'columns' => [
                'id' => '#',
                'name' => 'Name',
                'email' => 'Email',
                'role' => 'Role',
                'posts.count' => 'Posts Count',      // Relation count
                'created_at' => 'Joined Date',
            ],
            'searchable' => ['name', 'email', 'role'],
            'unsortable' => ['posts.count'],         // This field won't be sortable
            'defaultSortField' => 'created_at',      // Default: newest first
            'defaultSortDirection' => 'desc',
            'formatters' => [
                'created_at' => 'date',
            ],
        ]);
    }
}

// Blade template example: livewire/todos-table.blade.php
/*
<div>
    <livewire:livewire-datatable
        :model="$model"
        :columns="$columns"
        :searchable="$searchable"
        :defaultSortField="$defaultSortField"
        :defaultSortDirection="$defaultSortDirection"
    />
</div>
*/

// Usage Notes:
// 1. defaultSortField parameter accepts:
//    - Simple column names: 'id', 'title', 'status'
//    - Related model fields: 'user.name', 'category.title'
//    - Nested relations: 'user.profile.bio' (if supported)
//
// 2. defaultSortDirection can be:
//    - 'asc' for ascending order
//    - 'desc' for descending order
//
// 3. Default values (if not specified):
//    - defaultSortField: 'created_at'
//    - defaultSortDirection: 'desc'
//
// 4. When a user clicks on a column header to sort, that becomes the active sort.
//    The default sort is only applied when no explicit sort has been set.
//
// 5. The feature works seamlessly with:
//    - Search functionality
//    - Pagination
//    - Export (Excel/PDF)
//    - All formatter options
