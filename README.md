# Laravel Livewire DataTable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/developerawam/livewire-datatable.svg?style=flat-square)](https://packagist.org/packages/developerawam/livewire-datatable)
[![Total Downloads](https://img.shields.io/packagist/dt/developerawam/livewire-datatable.svg?style=flat-square)](https://packagist.org/packages/developerawam/livewire-datatable)
[![Donate on Saweria](https://img.shields.io/badge/Donate-Saweria-orange)](https://saweria.co/developerawam)

A powerful and flexible DataTable component for Laravel Livewire that transforms your data into beautiful, interactive tables with zero configuration required.

## 🎯 Quick Overview

- **Zero Configuration**: Works out of the box with just your Eloquent model
- **Server-Side Rendering**: Handles thousands of records efficiently
- **Feature-Rich**: Search, sort, filter, paginate, and export with ease
- **Fully Customizable**: Configure every aspect via config or per-component
- **Production Ready**: Built for real-world applications with proper error handling

## 📚 Table of Contents

- [Features](#-features)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [Basic Usage](#-basic-usage)
  - [Columns](#columns)
  - [Searching](#searching)
  - [Sorting](#sorting)
  - [Value Formatting](#value-formatting)
- [Advanced Features](#-advanced-features)
  - [Relationships](#relationships)
  - [Custom Query Scopes](#custom-query-scopes)
  - [Custom Cell Templates](#custom-cell-templates)
  - [Default Sort Configuration](#default-sort-configuration)
  - [Advanced Dynamic Filtering](#advanced-dynamic-filtering)
- [Exporting Data](#-exporting-data)
- [Customization](#-customization)
  - [Template System](#template-system)
  - [Theme Configuration](#theme-configuration)
  - [Dynamic CSS Classes](#dynamic-css-classes)
  - [Dark Mode Support](#dark-mode-support)
  - [Pagination Options](#pagination-options)
- [API Integration](#-api-integration)
- [Troubleshooting](#-troubleshooting)
- [Support](#-support)

## ✨ Features

| Feature                       | Description                                                     |
| ----------------------------- | --------------------------------------------------------------- |
| ⚡ **Server-Side Rendering**  | Handle thousands of records efficiently                         |
| 🔍 **Smart Search**           | Live search with intelligent debouncing across multiple columns |
| 📊 **Column Sorting**         | Sort by any column, including relationship data                 |
| 🔤 **Advanced Filtering**     | Multi-column filtering with intuitive UI                        |
| 📄 **Pagination**             | Fully customizable pagination with per-page options             |
| 📤 **Data Export**            | Export to Excel and PDF while respecting filters                |
| 🎨 **Dynamic Styling**        | All CSS classes configurable from config file                   |
| 🌙 **Dark Mode**              | Automatic dark mode support with Tailwind                       |
| 📱 **Responsive Design**      | Mobile-friendly on all screen sizes                             |
| 🔗 **Relationships**          | Display and sort by related model data using dot notation       |
| 🎯 **Custom Templates**       | Create custom cell content with Blade components                |
| 🛠 **Event System**           | Built-in event handling for user interactions                   |
| 🔧 **Zero Config**            | Works out of the box with sensible defaults                     |
| 🎨 **Multi-Template Support** | Tailwind CSS and Bootstrap 5+ templates built-in                |
| 📋 **Row Numbering**          | Smart "no" column with consistent sequential numbering          |

## 📋 Requirements

- **PHP**: ^8.2
- **Laravel**: ^12.0
- **Livewire**: ^3.0
- **CSS Framework**: Tailwind CSS ^3.0+ OR Bootstrap 5+

### Browser Support

All modern browsers (Chrome, Firefox, Safari, Edge)

## 📦 Installation

### 1. Install Package

```bash
composer require developerawam/livewire-datatable
```

### 2. Configure Your CSS Framework

#### For Tailwind CSS

Add the package's views to your Tailwind configuration:

```js
// tailwind.config.js
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./vendor/developerawam/livewire-datatable/resources/views/**/*.blade.php",
  ],
};
```

#### For Bootstrap 5+

No additional configuration needed! Bootstrap is automatically detected and used.

### 3. (Optional) Publish Configuration

```bash
php artisan vendor:publish --tag="livewire-datatable-config"
```

This allows you to customize default settings in `config/livewire-datatable.php`

### 4. (Optional) Set Template System

Choose your CSS framework template in `.env`:

````env
DATATABLE_TEMPLATE=tailwind    # Default
# or
DATATABLE_TEMPLATE=bootstrap
```.

## 🚀 Quick Start

Create a fully functional DataTable in under 2 minutes.

### 1. Create Livewire Component

```bash
php artisan make:livewire UsersTable
````

### 2. Setup Component

```php
<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class UsersTable extends Component
{
    public function render()
    {
        return view('livewire.users-table', [
            'model' => User::class,
            'columns' => [
                'id' => 'ID',
                'name' => 'Name',
                'email' => 'Email',
                'created_at' => 'Joined'
            ],
            'searchable' => ['name', 'email']
        ]);
    }
}
```

### 3. Create View

```blade
{{-- resources/views/livewire/users-table.blade.php --}}
<div>
    <livewire:livewire-datatable
        :model="$model"
        :columns="$columns"
        :searchable="$searchable" />
</div>
```

### 4. Use in Blade

```blade
<livewire:users-table />
```

**Done!** You now have a fully functional DataTable with search, sorting, and pagination.

## 📖 Basic Usage

### Columns

Define what data to display and how to label it:

```php
'columns' => [
    'id' => 'ID',
    'name' => 'Full Name',
    'email' => 'Email Address',
    'created_at' => 'Joined Date',
    'department.name' => 'Department',  // Relationship data
]
```

### Searching

Make columns searchable:

```php
'searchable' => ['name', 'email', 'department.name']
```

### Sorting

Control which columns can be sorted:

```php
// By default, all columns are sortable
// Prevent sorting on specific columns:
'unsortable' => ['actions', 'avatar']
```

### Value Formatting

Format column values automatically using formatters.

#### Simple Formatters

Use simple string formatters for common formats:

```php
'formatters' => [
    'created_at' => 'datetime',    // Format as datetime
    'updated_at' => 'date',        // Format as date
    'balance' => 'currency',       // Format as currency
    'is_active' => 'boolean',      // Format as Yes/No
]
```

#### Advanced Formatters

For complex formatting, use array syntax:

```php
'formatters' => [
    'description' => [
        'type' => 'words',
        'options' => ['words' => 10, 'end' => '...']
    ],
    'title' => [
        'type' => 'limit',
        'options' => ['length' => 50, 'end' => '...']
    ],
    'price' => [
        'type' => 'money',
        'options' => [
            'symbol' => '$',
            'decimals' => 2,
            'decimal_point' => '.',
            'thousand_sep' => ','
        ]
    ],
]
```

#### Available Formatters

| Formatter   | Usage                    | Options                                         |
| ----------- | ------------------------ | ----------------------------------------------- |
| `date`      | Format as date           | `format: 'Y-m-d'`                               |
| `datetime`  | Format as datetime       | `format: 'Y-m-d H:i:s'`                         |
| `time`      | Format as time           | `format: 'H:i:s'`                               |
| `number`    | Add thousands separator  | —                                               |
| `currency`  | Format as currency       | `symbol, decimals, decimal_point, thousand_sep` |
| `boolean`   | Convert to Yes/No        | `true, false`                                   |
| `uppercase` | Uppercase text           | —                                               |
| `lowercase` | Lowercase text           | —                                               |
| `limit`     | Limit string length      | `length, end`                                   |
| `words`     | Limit by word count      | `words, end`                                    |
| `markdown`  | Convert markdown to HTML | —                                               |
| `money`     | Advanced currency        | `symbol, decimals, decimal_point, thousand_sep` |

## � Advanced Features

### Relationships

Display and sort data from related models using dot notation.

#### 1. Setup Model with Relationships

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $with = ['department', 'role'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
```

#### 2. Use Dot Notation in Columns

```php
'columns' => [
    'id' => 'ID',
    'name' => 'Name',
    'department.name' => 'Department',
    'role.name' => 'Role',
    'department.location' => 'Office',
]
```

DataTable automatically handles relationships and makes them sortable!

### Custom Query Scopes

Apply filters and constraints using Eloquent query scopes.

#### 1. Define Scope on Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class User extends Model
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeFromDepartment(Builder $query, string $department): Builder
    {
        return $query->whereHas('department', fn ($q) => $q->where('name', $department));
    }
}
```

#### 2. Apply Scope to DataTable

```php
public function render()
{
    return view('livewire.users-table', [
        'model' => User::class,
        'scope' => 'active',  // Single scope
        'columns' => [...],
    ]);
}
```

#### 3. Apply Scope with Parameters

```php
public function render()
{
    return view('livewire.users-table', [
        'model' => User::class,
        'scope' => 'fromDepartment',
        'scopeParams' => ['Engineering'],
        'columns' => [...],
    ]);
}
```

### Custom Cell Templates

Create rich, interactive cell content with custom Blade templates.

#### 1. Define Custom Columns

```php
public function render()
{
    return view('livewire.users-table', [
        'model' => User::class,
        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'status' => 'Status',
            'actions' => 'Actions'
        ],
        'customColumns' => [
            'status' => 'components.table.status-badge',
            'actions' => 'components.table.user-actions'
        ],
        'unsortable' => ['actions']
    ]);
}
```

#### 2. Create Custom Templates

**Status Badge** (`resources/views/components/table/status-badge.blade.php`):

```blade
@php
    $statusColors = [
        'active' => 'bg-green-100 text-green-800',
        'inactive' => 'bg-red-100 text-red-800',
        'pending' => 'bg-yellow-100 text-yellow-800'
    ];
    $colorClass = $statusColors[$value] ?? 'bg-gray-100 text-gray-800';
@endphp

<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $colorClass }}">
    {{ ucfirst($value) }}
</span>
```

**Action Buttons** (`resources/views/components/table/user-actions.blade.php`):

```blade
<div class="flex items-center space-x-2">
    <button
        wire:click="$dispatch('user-edit', { id: {{ $item->id }} })"
        class="text-blue-600 hover:text-blue-800">
        Edit
    </button>
    <button
        wire:click="$dispatch('user-delete', { id: {{ $item->id }} })"
        wire:confirm="Delete this user?"
        class="text-red-600 hover:text-red-800">
        Delete
    </button>
</div>
```

#### 3. Handle Events in Component

```php
<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;

class UsersTable extends Component
{
    #[On('user-edit')]
    public function editUser($id)
    {
        $this->redirect(route('users.edit', $id));
    }

    #[On('user-delete')]
    public function deleteUser($id)
    {
        try {
            User::findOrFail($id)->delete();
            session()->flash('message', 'User deleted successfully!');
            // Refresh table after deletion
            $this->dispatch('reset-table');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete user.');
        }
    }

    #[On('user-update-status')]
    public function updateUserStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['status' => $user->status === 'active' ? 'inactive' : 'active']);
            session()->flash('message', 'User status updated!');
            // Refresh table after status update
            $this->dispatch('reset-table');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update user status.');
        }
    }

    public function render()
    {
        return view('livewire.users-table', [
            'model' => User::class,
            'columns' => [...],
            'customColumns' => [...],
        ]);
    }
}
```

**Available Variables in Custom Templates:**

- `$item` - Current model instance
- `$value` - Current column value

**Refresh Table After Actions:**

```php
// Dispatch event to refresh the table after modifications
$this->dispatch('reset-table');
```

### Default Sort Configuration

Customize the default sort field and direction.

```php
<livewire:livewire-datatable
    :model="User::class"
    :columns="[...]"
    defaultSortField="created_at"
    defaultSortDirection="desc" />
```

Works with relationships using dot notation:

```php
defaultSortField="department.name"
defaultSortDirection="asc"
```

### Advanced Dynamic Filtering

Filter data across multiple columns with an intuitive interface.

#### Enable/Disable Filtering

```php
// config/livewire-datatable.php
return [
    'advanced_filter' => true,  // Default: true
];
```

#### How It Works

Users can:

- Click "Filter.." to add filter conditions
- Select columns to filter by
- Enter filter values
- Add multiple conditions (AND logic)
- Reset all filters at once

#### Customization

All filter elements have configurable CSS classes:

```php
// config/livewire-datatable.php
'theme' => [
    'filter_panel' => 'p-4 border-r border-gray-200',
    'filter_items' => 'space-y-3',
    'filter_input' => 'py-2.5 px-4 border-gray-200 rounded-lg',
    'filter_add_button' => 'py-2 px-3 text-sm font-medium',
    'filter_reset_button' => 'py-2 px-3 text-sm font-medium',
    'filter_apply_button' => 'py-2 px-3 text-sm font-medium',
]
```

### Row Numbering ("no" Column)

The "no" column provides sequential row numbering that works intelligently with sorting and pagination.

#### Behavior

- **Sequential numbering**: Always displays 1, 2, 3... regardless of sort order
- **Pagination-aware**: Continues numbering across pages (page 2 shows 11, 12, 13...)
- **Sort-independent**: Doesn't reverse or change based on sort direction
- **Consistent**: Maintains the same numbering regardless of active filters or searches

#### Example

```
Page 1 (perPage: 10):
No | Name              | Email
1  | John Doe         | john@example.com
2  | Jane Smith       | jane@example.com
...
10 | Mike Johnson     | mike@example.com

Page 2:
No | Name              | Email
11 | Sarah Williams   | sarah@example.com
12 | Tom Brown        | tom@example.com
```

Even when sorting by different columns, the "no" column always displays sequential numbering.

## 🌐 API Integration

The DataTable supports both Eloquent models and API endpoints for flexibility.

### Setup API DataTable

```php
<?php

namespace App\Livewire;

use Livewire\Component;

class TodoTableApi extends Component
{
    public function render()
    {
        $apiConfig = [
            'url' => url('/api/todos'),
            'headers' => ['Accept' => 'application/json'],
            'data_key' => 'data',           // Where to find items
            'total_key' => 'total',         // Where to find total count
            'search_param' => 'search',
            'sort_param' => 'sort',
            'sort_direction_param' => 'direction',
            'per_page_param' => 'per_page',
            'page_param' => 'page',
        ];

        return view('livewire.todo-table-api', [
            'apiConfig' => $apiConfig,
            'columns' => ['id' => 'ID', 'title' => 'Title'],
            'searchable' => ['title'],
        ]);
    }
}
```

### API View

```blade
<div>
    <livewire:livewire-datatable
        :api-config="$apiConfig"
        :columns="$columns"
        :searchable="$searchable" />
</div>
```

### Required Response Format

Your API must return:

```json
{
  "data": [{ "id": 1, "title": "Task", "created_at": "2025-01-27T10:00:00Z" }],
  "total": 100,
  "per_page": 10,
  "current_page": 1,
  "last_page": 10,
  "from": 1,
  "to": 10
}
```

### API Query Parameters

DataTable sends these parameters:

```
GET /api/todos?search=keyword&sort=title&direction=asc&per_page=10&page=1
```

### Custom API Configuration

```php
$apiConfig = [
    'url' => url('/api/todos'),
    'method' => 'GET',
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ],
    'query_params' => ['status' => 'active'],
    'response_key' => 'data.todos',  // For nested responses
];
```

## 📤 Exporting Data

Export your DataTable data to Excel and PDF formats.

### Export Features

- Export to Excel (`.xlsx`) and PDF
- Exports all records (respects pagination)
- Respects active search filters
- Maintains data formatting (dates, currency, etc.)
- Automatically excludes action columns
- Responsive UI with dark mode support

### Configuration

```php
// config/livewire-datatable.php
'export' => [
    'enabled' => true,
    'types' => ['excel', 'pdf'],
    'orientation' => 'portrait',
    'paper_size' => 'a4',
];
```

### How to Use

1. Click the "Export" dropdown in the controls
2. Select "Export Excel" or "Export PDF"
3. File downloads automatically

### Example with Custom Options

```php
'export' => [
    'enabled' => true,
    'types' => ['excel', 'pdf'],
    'orientation' => 'landscape',
    'paper_size' => 'a4',
    'dropdown' => [
        'position' => 'top',
        'trigger_text' => 'Download',
    ],
],
```

## 🎨 Customization

### Template System

The DataTable supports multiple CSS frameworks. Switch between them easily:

#### Available Templates

- **tailwind** - Tailwind CSS (default)
- **bootstrap** - Bootstrap 5+

#### Switch Template

Set in `config/livewire-datatable.php`:

```php
'template' => env('DATATABLE_TEMPLATE', 'tailwind'),
```

Or in `.env`:

```env
DATATABLE_TEMPLATE=bootstrap
```

#### Bootstrap Pagination

If you're using Bootstrap CSS framework instead of Tailwind CSS, you can configure Livewire to use Bootstrap pagination styles. See the [Livewire Bootstrap Pagination Documentation](https://livewire.laravel.com/docs/4.x/pagination#using-bootstrap-instead-of-tailwind) for detailed setup instructions.

#### Bootstrap Configuration

```php
'bootstrap_theme' => [
    'wrapper' => 'container-fluid card',
    'table' => 'table table-hover table-sm',
    'th' => 'table-light',
    'th_sort_button' => 'btn btn-sm btn-ghost',
    // ... more bootstrap classes
]
```

### Theme Configuration

Customize the default appearance via `config/livewire-datatable.php`.

#### Quick Example

```php
'theme' => [
    'table' => 'min-w-full divide-y divide-gray-200',
    'th' => 'px-6 py-3 bg-gray-50 text-left text-xs font-medium',
    'td' => 'px-6 py-4 whitespace-nowrap text-sm',
    'tr' => 'hover:bg-gray-50 transition',
]
```

#### Per-Column Styling

Style specific columns using column keys:

```php
'theme' => [
    'td_id' => 'font-mono text-gray-500 text-xs',
    'td_email' => 'font-medium text-blue-600',
    'td_status' => 'text-center font-semibold',
    'td_actions' => 'text-right space-x-2',
]
```

### Dynamic CSS Classes

Every element in the DataTable is fully configurable via CSS classes. All elements have `data-class` attributes for easy debugging.

#### Component-Level Overrides

Override theme for specific tables:

```php
public function render()
{
    return view('livewire.users-table', [
        'model' => User::class,
        'columns' => ['id' => 'ID', 'name' => 'Name'],
        'theme' => [
            'table' => 'min-w-full divide-y divide-blue-200',
            'tr' => 'hover:bg-blue-50',
            'td_id' => 'font-mono text-gray-500',
        ]
    ]);
}
```

Use in view:

```blade
<livewire:livewire-datatable
    :model="$model"
    :columns="$columns"
    :theme="$theme" />
```

### Dark Mode Support

Automatic dark mode support with Tailwind CSS. Simply add the `dark` class to your HTML element:

```html
<html class="dark">
  <!-- Your app -->
</html>
```

Or use dynamic switching:

```js
document.documentElement.classList.toggle("dark");
```

### Debugging Style Issues

Each element has a `data-class` attribute showing which config key controls it. Inspect in browser to find the right configuration option.

### Pagination Options

Customize pagination behavior and options.

#### Per-Page Options

Configure available per-page choices:

```php
// config/livewire-datatable.php
'per_page_options' => [10, 25, 50, 100, 'all']
```

The `'all'` option allows users to display all records at once.

#### Default Per Page

```php
// Set in component
public $perPage = 25;
```

Or let users choose with the per-page selector in the UI.

## 📝 Complete Example

Here's a comprehensive example with multiple features:
'export_dropdown_arrow' => '-mr-1 ml-2 h-5 w-5',
'export_button' => 'px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800',

        // Table structure
        'table_wrapper' => 'overflow-x-auto border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow',
        'table' => 'min-w-full divide-y divide-gray-200 dark:divide-gray-700',

        // Table headers
        'thead' => '',
        'thead_row' => '',
        'th' => 'px-6 py-3 bg-gray-50 dark:bg-gray-700/50 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider',
        'th_sort_button' => 'group inline-flex items-center gap-x-2 hover:text-gray-700 dark:hover:text-gray-200',
        'th_sort_icon_wrapper' => 'inline-flex rounded p-1 transition',
        'th_sort_icon_active' => 'size-4 text-blue-500',
        'th_sort_icon_inactive' => 'size-4 text-gray-400 dark:text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-200',
        'th_text' => 'text-gray-700 dark:text-gray-200 capitalize',

        // Table body
        'tbody' => 'divide-y divide-gray-200 dark:divide-gray-700',
        'tr' => 'hover:bg-gray-50 dark:hover:bg-gray-700/25 transition',
        'td' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200',

        // Empty state
        'empty_wrapper' => 'px-6 py-8 text-center',
        'empty_content' => 'flex flex-col items-center justify-center',
        'empty_icon' => 'size-16 text-gray-400 dark:text-gray-500 mb-2',
        'empty_text' => 'text-gray-500 dark:text-gray-400 text-sm font-medium',

        // Pagination
        'pagination_wrapper' => 'p-4',

        // Column-specific styling (optional)
        // 'td_id' => 'font-mono text-gray-500 text-xs',
        // 'td_email' => 'font-medium text-blue-600',
        // 'td_status' => 'text-center font-semibold',
        // 'td_actions' => 'text-right space-x-2',
    ]

];

````

## 📝 Complete Example

Here's a comprehensive example with multiple features:

```php
<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;

class AdvancedUsersTable extends Component
{
    #[On('user-edit')]
    public function editUser($id)
    {
        $this->redirect(route('users.edit', $id));
    }

    #[On('user-delete')]
    public function deleteUser($id)
    {
        try {
            User::findOrFail($id)->delete();
            session()->flash('message', 'User deleted!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete user.');
        }
    }

    public function render()
    {
        return view('livewire.advanced-users-table', [
            'model' => User::class,
            'scope' => 'active',
            'columns' => [
                'id' => 'ID',
                'name' => 'Name',
                'email' => 'Email',
                'department.name' => 'Department',
                'role.name' => 'Role',
                'status' => 'Status',
                'created_at' => 'Joined',
                'actions' => 'Actions'
            ],
            'searchable' => ['name', 'email'],
            'unsortable' => ['actions'],
            'customColumns' => [
                'status' => 'components.table.status-badge',
                'actions' => 'components.table.user-actions'
            ],
            'defaultSortField' => 'created_at',
            'defaultSortDirection' => 'desc',
        ]);
    }
}
````

## ✅ API Reference

Quick reference of all available parameters:

| Parameter              | Type   | Description              |
| ---------------------- | ------ | ------------------------ |
| `model`                | string | Eloquent model class     |
| `columns`              | array  | Field names and labels   |
| `searchable`           | array  | Searchable field names   |
| `unsortable`           | array  | Non-sortable field names |
| `customColumns`        | array  | Custom template paths    |
| `formatters`           | array  | Value formatters         |
| `scope`                | string | Query scope name         |
| `scopeParams`          | array  | Query scope parameters   |
| `defaultSortField`     | string | Initial sort field       |
| `defaultSortDirection` | string | 'asc' or 'desc'          |
| `theme`                | array  | CSS class overrides      |
| `apiConfig`            | array  | API configuration        |

## ❓ Troubleshooting

### Common Issues

**Search not working on relationships**

Ensure the relationship is eager loaded in your model using `$with`:

```php
protected $with = ['department', 'role'];
```

**Custom columns not displaying**

- Verify the view file exists at the specified path
- Check that the view receives `$item` and `$value` variables

**Styles not applying**

- Verify Tailwind CSS is properly configured
- Check that package views are in `tailwind.config.js` content array

**Export not working**

- Verify export is enabled in config
- Check that required packages are installed

### Getting Help

1. Check [GitHub issues](https://github.com/developerawam/livewire-datatable/issues)
2. Review the examples in this documentation
3. Inspect browser console for errors

## 💝 Support

If this package has helped your project, consider supporting its continued development:

[![Donate on Saweria](https://img.shields.io/badge/Donate-Saweria-orange)](https://saweria.co/developerawam)

## 🔒 Security

Please report security vulnerabilities to info@developerawam.com instead of using the public issue tracker.

## 👥 Credits

- [Developer Awam](https://github.com/developerawam) - Package Author
- [Restu](https://github.com/restu-lomboe) - Lead Developer

## 📄 License

Licensed under the MIT License - see [LICENSE.md](LICENSE.md) for details.

---

**Ready to build amazing DataTables?** [Get started now](#-quick-start) and transform your Laravel applications with beautiful, interactive tables!
