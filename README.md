# Laravel Livewire DataTable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/developerawam/livewire-datatable.svg?style=flat-square)](https://packagist.org/packages/developerawam/livewire-datatable)
[![Total Downloads](https://img.shields.io/packagist/dt/developerawam/livewire-datatable.svg?style=flat-square)](https://packagist.org/packages/developerawam/livewire-datatable)
[![Donate on Saweria](https://img.shields.io/badge/Donate-Saweria-orange)](https://saweria.co/developerawam)

A powerful and flexible DataTable component for Laravel Livewire that transforms your data into beautiful, interactive tables with zero configuration required.

![Laravel Livewire DataTable](./datatable.png)

## 🚀 Why Choose This DataTable?

- **Zero Configuration**: Works out of the box with just your Eloquent model
- **Lightning Fast**: Server-side rendering with optimized queries
- **Developer Friendly**: Intuitive API with extensive customization options
- **Production Ready**: Built for real-world applications with proper error handling

## 📚 Table of Contents

1. [Features](#-features)
2. [Requirements](#-requirements)
3. [Installation](#-installation)
4. [Quick Start](#-quick-start)
5. [Basic Usage](#-basic-usage)
6. [Advanced Features](#-advanced-features)
   - [Default Sort Configuration](#default-sort-configuration)
   - [Advanced Dynamic Filtering](#advanced-dynamic-filtering)
7. [Export Features](#-export-features)
8. [Customization](#-customization)
   - [Theme Configuration](#theme-configuration)
   - [Dynamic CSS Classes](#dynamic-css-classes)
9. [Examples](#-examples)
10. [Support](#-support)

## ✨ Features

- ⚡ **Server-side Rendering** - Handle thousands of records efficiently
- 🔍 **Smart Search** - Live search with intelligent debouncing
- 📊 **Column Sorting** - Sort by any column, including relationships
- 🔤 **Advanced Filtering** - Multi-column filtering with dynamic UI
- 🎯 **Default Sorting** - Customize default sort field and direction
- 📄 **Dynamic Pagination** - Customizable pagination with "Show All" option
- 📤 **Data Export** - Export to Excel and PDF with proper formatting
- 🎨 **Fully Dynamic Styling** - All CSS classes configurable from config
- 🌙 **Dark Mode** - Automatic dark mode support
- 📱 **Responsive Design** - Perfect on all screen sizes
- 🔗 **Relationships** - Display and sort by related model data
- ⚡ **Real-time Updates** - Automatic updates with Livewire
- 🎯 **Custom Templates** - Create custom cell content with ease
- 🛠 **Event System** - Built-in event handling for interactions

## 📋 Requirements

- **PHP**: ^8.2
- **Laravel**: ^12.0
- **Livewire**: ^3.0
- **Tailwind CSS**: ^3.0

### Browser Support

Modern browsers including Chrome, Firefox, Safari, and Edge (latest versions)

## 📦 Installation

### Step 1: Install the Package

```bash
composer require developerawam/livewire-datatable
```

### Step 2: Configure Tailwind CSS

Add the package's views to your Tailwind configuration to ensure all styles are included if you are using Tailwind v3:

```js
// tailwind.config.js
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./vendor/developerawam/livewire-datatable/resources/views/**/*.blade.php",
  ],
  // ... rest of your config
};
```

### Step 3: Optional Configuration

Publish the configuration file to customize default settings:

```bash
php artisan vendor:publish --tag="livewire-datatable-config"
```

## 🚀 Quick Start

Get a DataTable running in under 2 minutes:

### 1. Create a Livewire Component

```bash
php artisan make:livewire UsersTable
```

### 2. Set Up Your Component

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
                'no' => '#',
                'name' => 'Name',
                'email' => 'Email',
                'created_at' => 'Created At'
            ],
            'searchable' => ['name', 'email']
        ]);
    }
}
```

### 3. Create the View

```blade
{{-- resources/views/livewire/users-table.blade.php --}}
<div>
    <livewire:livewire-datatable
        :model="$model"
        :columns="$columns"
        :searchable="$searchable" />
</div>
```

### 4. Use in Your Blade Template

```blade
{{-- In any blade file --}}
<livewire:users-table />
```

**That's it!** You now have a fully functional DataTable with search, sorting, and pagination.

## 📖 Basic Usage

### Value Formatting

The DataTable supports powerful value formatting options through the `formatters` and `formatterOptions` parameters. This allows you to format dates, numbers, text, and more in a variety of ways.

#### Simple Formatters

```php
class UsersTable extends Component
{
    public function render()
    {
        return view('livewire.users-table', [
            'columns' => [
                'name' => 'Name',
                'created_at' => 'Created At',
                'updated_at' => 'Updated At',
                'balance' => 'Balance',
                'is_active' => 'Status',
            ],
            'formatters' => [
                'created_at' => 'datetime',    // Format as datetime
                'updated_at' => 'date',        // Format as date
                'balance' => 'currency',       // Format as currency
                'is_active' => 'boolean',      // Format as Yes/No
            ],
        ]);
    }
}
```

#### Advanced Formatters

For more complex formatting needs, use the array syntax with type and options:

```php
'formatters' => [
    'description' => [
        'type' => 'words',
        'options' => [
            'words' => 10,
            'end' => '...'
        ]
    ],
    'title' => [
        'type' => 'limit',
        'options' => [
            'length' => 50,
            'end' => '...'
        ]
    ],
    'price' => [
        'type' => 'money',
        'options' => [
            'symbol' => '€',
            'decimals' => 2,
            'decimal_point' => '.',
            'thousand_sep' => ','
        ]
    ],
]
```

#### Available Formatters

1. Simple Formatters (string):

   - `date` - Format as date (Y-m-d)
   - `datetime` - Format as datetime (Y-m-d H:i:s)
   - `time` - Format as time (H:i:s)
   - `number` - Format with thousands separator
   - `currency` - Format as currency with prefix
   - `boolean` - Convert to Yes/No
   - `uppercase` - Convert to uppercase
   - `lowercase` - Convert to lowercase

2. Complex Formatters (array):
   - `limit` - Limit string length
     ```php
     [
         'type' => 'limit',
         'options' => [
             'length' => 50,    // Max characters
             'end' => '...'     // Ending
         ]
     ]
     ```
   - `words` - Limit by word count
     ```php
     [
         'type' => 'words',
         'options' => [
             'words' => 10,     // Max words
             'end' => '...'     // Ending
         ]
     ]
     ```
   - `markdown` - Convert markdown to HTML
     ```php
     [
         'type' => 'markdown'
     ]
     ```
   - `money` - Advanced currency formatting
     ```php
     [
         'type' => 'money',
         'options' => [
             'symbol' => '$',
             'decimals' => 2,
             'decimal_point' => '.',
             'thousand_sep' => ','
         ]
     ]
     ```
   - `date` - Custom date formatting
     ```php
     [
         'type' => 'date',
         'options' => [
             'format' => 'l, F j, Y'  // Any PHP date format
         ]
     ]
     ```

#### Formatter Options

Each formatter type supports specific options:

1. Date Formatters (`date`, `datetime`, `time`):

   ```php
   'formatterOptions' => [
       'created_at' => [
           'format' => 'Y-m-d H:i:s'  // Any PHP date format string
       ]
   ]
   ```

2. Number/Currency Formatters:

   ```php
   'formatterOptions' => [
       'price' => [
           'symbol' => '$',           // Currency symbol
           'decimals' => 2,           // Decimal places
           'decimal_point' => '.',    // Decimal separator
           'thousand_sep' => ','      // Thousands separator
       ]
   ]
   ```

3. Boolean Formatter:
   ```php
   'formatterOptions' => [
       'is_active' => [
           'true' => 'Yes',          // Custom true label
           'false' => 'No'           // Custom false label
       ]
   ]
   ```

### Understanding Columns

The `columns` array defines what data to display and how to label it:

```php
'columns' => [
    'no' => '#',            // Model attribute => Column label
    'name' => 'Full Name',     // Custom label
    'email' => 'Email Address',
    'created_at' => 'Joined Date'
]
```

### Adding Search Functionality

Make columns searchable by listing them in the `searchable` array:

```php
'searchable' => ['name', 'email', 'phone']
```

Users can then search across these fields using the search input that appears automatically.

### Controlling Sortable Columns

By default, all columns are sortable. To prevent sorting on specific columns:

```php
'unsortable' => ['actions', 'avatar']
```

## 🚀 Advanced Features

### Default Sort Configuration

Customize the default sort field and direction for your datatable:

```php
<livewire:livewire-datatable
    :model="User::class"
    :columns="[
        'id' => 'ID',
        'name' => 'Name',
        'email' => 'Email',
        'created_at' => 'Created At'
    ]"
    defaultSortField="created_at"
    defaultSortDirection="desc" />
```

#### Features:

- **defaultSortField**: Column key to sort by initially (default: 'created_at')
- **defaultSortDirection**: 'asc' or 'desc' (default: 'desc')
- Applies automatically on component load
- Can be overridden by user sorting
- Works with relationships using dot notation

#### Example with Relationships:

```php
<livewire:livewire-datatable
    :model="Order::class"
    :columns="[
        'id' => 'Order ID',
        'customer.name' => 'Customer',
        'total' => 'Total',
        'created_at' => 'Date'
    ]"
    defaultSortField="created_at"
    defaultSortDirection="desc" />
```

### Advanced Dynamic Filtering

Filter data across multiple columns with a powerful, user-friendly interface:

#### Enable/Disable Filtering

Control filter availability in your configuration:

```php
// config/livewire-datatable.php
return [
    'advanced_filter' => true, // Enable advanced filtering (default: true)
];
```

#### How It Works:

The datatable provides a collapsible filter panel where users can:

1. **Add Filters** - Click "Filter.." to add new filter conditions
2. **Filter by Column** - Select which column to filter
3. **Enter Value** - Input search/filter value
4. **Multiple Conditions** - Add multiple filters (AND logic)
5. **Reset** - Clear all filters at once
6. **Apply** - Apply filters to the table

#### Filter Features:

- **Multi-Column Filtering**: Filter by multiple columns simultaneously
- **Real-time Application**: Filters apply instantly with visual feedback
- **Sort Integration**: Sorting works seamlessly with active filters
- **Export Integration**: Exports respect active filters
- **Filter State**: Filter state persists during pagination
- **Reset Functionality**: Reset all filters or individual filters

#### Example Usage in Blade:

```blade
<livewire:users-table
    :model="User::class"
    :columns="[
        'id' => 'ID',
        'name' => 'Name',
        'email' => 'Email',
        'status' => 'Status'
    ]"
    :searchable="['name', 'email']" />
```

The filter button automatically appears in the controls, allowing users to filter data.

#### Filter Panel UI Elements:

The filter panel includes:

- **Filter Toggle Button** - Visible when model is set
- **Filter Header** - Shows "View" label with close button
- **Filter Items** - List of active filters
  - Text input for filter value
  - Dropdown for column selection
  - Delete button for individual filters
- **Action Buttons**:
  - **Filter..** - Add new filter condition
  - **Reset** - Clear all filters
  - **Filter** - Apply filters to table

#### CSS Classes for Customization:

All filter elements have configurable CSS classes in `config/livewire-datatable.php`:

```php
'theme' => [
    // Filter panel
    'filter_panel' => 'transition duration-300 ease-in-out p-4 border-r border-gray-200 dark:border-gray-700',
    'filter_header' => 'flex justify-between items-center',
    'filter_content' => 'flex flex-col mt-4 space-y-4',

    // Filter items
    'filter_items' => 'max-w-sm space-y-3',
    'filter_item' => 'flex gap-2 items-center justify-between',

    // Filter inputs
    'filter_input' => 'py-2.5 px-4 block w-full border-gray-200 rounded-lg',
    'filter_select' => 'text-sm block w-30 border-l border-gray-200 rounded',

    // Action buttons
    'filter_add_button' => 'py-2 pl-2 pr-3 inline-flex items-center gap-x-1 text-sm font-medium',
    'filter_reset_button' => 'py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium',
    'filter_apply_button' => 'py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium',
]
```

## 🌐 API Integration

### Using with API Data Source

The DataTable can work with both Eloquent models and API endpoints. Here's how to set up an API-powered DataTable:

### 1. Create a Livewire Component for API

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
            'headers' => [
                'Accept' => 'application/json',
            ],
            'response_key' => null,      // Use if response is nested, e.g., 'data.todos'
            'data_key' => 'data',        // Where to find items in response
            'total_key' => 'total',      // Where to find total count
            'per_page_key' => 'per_page',
            'current_page_key' => 'current_page',
            'search_param' => 'search',   // Query parameter for search
            'sort_param' => 'sort',       // Query parameter for sort field
            'sort_direction_param' => 'direction', // Query parameter for sort direction
            'per_page_param' => 'per_page', // Query parameter for items per page
            'page_param' => 'page',       // Query parameter for current page
        ];

        $columns = [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'created_at' => 'Created At',
        ];

        return view('livewire.todo-table-api', [
            'apiConfig' => $apiConfig,    // Pass apiConfig instead of model
            'columns' => $columns,
            'searchable' => ['title', 'description'],
            'unsortable' => ['description'],
        ]);
    }
}
```

### 2. Create the View

```blade
{{-- resources/views/livewire/todo-table-api.blade.php --}}
<div>
    <livewire:livewire-datatable
        :api-config="$apiConfig"
        :columns="$columns"
        :searchable="$searchable"
        :unsortable="$unsortable" />
</div>
```

### 3. Required API Response Format

Your API endpoint must return responses in this format:

```json
{
  "data": [
    {
      "id": 1,
      "title": "Complete task",
      "description": "Need to complete this task",
      "created_at": "2025-09-19T10:00:00.000000Z"
    }
  ],
  "total": 100, // Total number of records (for pagination)
  "per_page": 10, // Items per page
  "current_page": 1, // Current page number
  "last_page": 10, // Total number of pages
  "from": 1, // Starting record number
  "to": 10 // Ending record number
}
```

### 4. API Query Parameters

The DataTable will send these query parameters to your API:

```
/api/todos?search=keyword&sort=title&direction=asc&per_page=10&page=1
```

- `search`: Search term entered by user
- `sort`: Column to sort by
- `direction`: Sort direction (asc/desc)
- `per_page`: Items per page
- `page`: Current page number

### 5. Custom API Configuration

You can customize the API behavior:

```php
$apiConfig = [
    'url' => url('/api/todos'),
    'method' => 'GET',           // HTTP method (default: GET)
    'headers' => [              // Custom headers
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ],
    'query_params' => [         // Additional query parameters
        'status' => 'active',
        'type' => 'task',
    ],
    // Custom parameter names
    'search_param' => 'q',      // Changes ?search= to ?q=
    'sort_param' => 'orderBy',  // Changes ?sort= to ?orderBy=
];
```

### 6. Nested Response Data

If your API response is nested, use `response_key`:

```json
{
    "status": "success",
    "data": {
        "todos": {
            "data": [...],
            "total": 100
        }
    }
}
```

Configure with:

```php
$apiConfig = [
    'response_key' => 'data.todos',
    'data_key' => 'data',
    'total_key' => 'total',
];
```

## 🔥 Advanced Features

### Working with Relationships

Display data from related models seamlessly:

#### 1. Prepare Your Model

Add relationships to the model's `$with` property for optimal loading:

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
    'no' => '#',
    'name' => 'Name',
    'department.name' => 'Department',     // Related model data
    'role.name' => 'Role',                 // Another relationship
    'department.location' => 'Office'       // Nested relationship data
]
```

The DataTable will automatically handle the relationships and make them sortable too!

### Custom Query Scopes

Apply filters or constraints using Eloquent scopes:

#### 1. Define a Scope in Your Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeFromDepartment(Builder $query, string $department): Builder
    {
        return $query->whereHas('department', function ($q) use ($department) {
            $q->where('name', $department);
        });
    }

    public function scopeStatus(Builder $query, string $status, bool $isCompleted) : Builder
    {
        return $query->where('status', $status)
                     ->where('is_completed', $isCompleted);
    }
}
```

#### 2. Apply the Scope

```php
public function render()
{
    return view('livewire.users-table', [
        'model' => User::class,
        'scope' => 'active',  // Apply the 'active' scope
        'columns' => [
            'no' => '#',
            'name' => 'Name',
            'email' => 'Email',
            'status' => 'Status'
        ]
    ]);
}
```

#### 3. Apply the Scope with Params

```php
public function render()
{
    return view('livewire.users-table', [
        'model' => User::class,
        'scope' => 'status',  // Apply the 'active' scope
        'scopeParams' => ['compeleted', true] // apply params
        'columns' => [
            'no' => '#',
            'name' => 'Name',
            'email' => 'Email',
            'status' => 'Status'
        ]
    ]);
}
```

#### 4. Use in Your View

```blade
<livewire:livewire-datatable
    :model="$model"
    :scope="$scope"
    :scopeParams="$scopeParams"
    :columns="$columns" />
```

### Custom Cell Templates

Create rich, interactive cell content with custom templates:

#### 1. Define Custom Columns

```php
public function render()
{
    return view('livewire.users-table', [
        'model' => User::class,
        'columns' => [
            'no' => '#',
            'name' => 'Name',
            'status' => 'Status',
            'actions' => 'Actions'        // Custom column
        ],
        'customColumns' => [
            'status' => 'components.table.status-badge',    // Custom status display
            'actions' => 'components.table.user-actions'    // Custom action buttons
        ],
        'unsortable' => ['actions']  // Actions shouldn't be sortable
    ]);
}
```

#### 2. Create Status Badge Template

```blade
{{-- resources/views/components/table/status-badge.blade.php --}}
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

#### 3. Create Action Buttons Template

```blade
{{-- resources/views/components/table/user-actions.blade.php --}}
<div class="flex items-center space-x-2">
    <button
        wire:click="$dispatch('user-edit', { id: {{ $item->id }} })"
        class="inline-flex items-center px-2 py-1 text-sm text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded"
        title="Edit User">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
    </button>

    <button
        wire:click="$dispatch('user-delete', { id: {{ $item->id }} })"
        wire:confirm="Are you sure you want to delete this user?"
        class="inline-flex items-center px-2 py-1 text-sm text-red-600 hover:text-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 rounded"
        title="Delete User">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
        </svg>
    </button>
</div>
```

#### 4. Handle Events in Your Component

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
        $user = User::find($id);

        // Redirect to edit page or open modal
        $this->dispatch('reset-table'); // Refresh table after edit
        $this->redirect(route('users.edit', $user));

        // Or dispatch another event to open a modal
        // $this->dispatch('open-edit-modal', userId: $id);
    }

    #[On('user-delete')]
    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            // Show success message
            session()->flash('message', 'User deleted successfully!');

            $this->dispatch('reset-table'); // Refresh table after deletion
        } catch (\Exception $e) {
            // Show error message
            session()->flash('error', 'Failed to delete user.');
        }
    }

    public function render()
    {
        return view('livewire.users-table', [
            'model' => User::class,
            'columns' => [
                'no' => '#',
                'name' => 'Name',
                'email' => 'Email',
                'status' => 'Status',
                'actions' => 'Actions'
            ],
            'searchable' => ['name', 'email'],
            'customColumns' => [
                'status' => 'components.table.status-badge',
                'actions' => 'components.table.user-actions'
            ],
            'unsortable' => ['actions']
        ]);
    }
}
```

**Template Variables**: Your custom templates receive:

- `$item`: The current model instance (e.g., User object)
- `$value`: The value of the current column
- `$this->dispatch('reset-table')`: Refresh (auto refresh) table after action done

## 📤 Export Features

### Export Features

- 📊 **Multiple Formats**: Export to Excel and PDF
- 🔄 **All Data Export**: Exports all records, not just current page
- 🔍 **Search Integration**: Exports respect current search filters
- 📝 **Smart Formatting**: Maintains data formatting in exports
- 🎯 **Action Column Handling**: Automatically excludes action columns
- 📋 **Custom Filenames**: Includes search context in filenames
- 🎨 **PDF Styling**: Clean, professional PDF layouts
- 📱 **Responsive UI**: Export buttons adapt to screen size

### Export Options

1. **Format Selection**

   - Excel: `.xlsx` format with proper data types
   - PDF: Clean, formatted PDF documents

2. **Data Scope**

   - All records are exported regardless of pagination
   - Search filters are respected in exports
   - Sort order is maintained

3. **Formatting**

   - Dates are properly formatted
   - Numbers and currency maintain formatting
   - Boolean values use configured labels
   - Relationships are properly resolved

4. **UI Options**

   - Configurable button position
   - Dropdown or separate buttons
   - Customizable button text and styling
   - Dark mode support

### Export Configuration

Configure export options in your `config/livewire-datatable.php`:

```php
'export' => [
    'enabled' => true,
    'types' => ['excel', 'pdf'],
    'orientation' => 'portrait',
    'paper_size' => 'a4',
    'dropdown' => [
        'position' => 'top', // top, bottom, both
        'trigger_class' => 'inline-flex items-center ...',
        'menu_class' => 'absolute left-0 z-10 mt-2 ...',
        'item_class' => 'block w-full p...',
        'trigger_text' => 'Export',
        'excel_text' => 'Export Excel',
        'pdf_text' => 'Export PDF',
    ],
],
```

## 🎨 Customization

### Theme Configuration

#### Global Theme Settings

Customize the default appearance by editing `config/livewire-datatable.php`:

```php
<?php

return [
    'theme' => [
        // Main container
        'wrapper' => 'w-full',
        'table_wrapper' => 'overflow-hidden shadow-lg rounded-lg',
        'table' => 'min-w-full divide-y divide-gray-200',

        // Search section
        'search_wrapper' => 'mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between',
        'search_input' => 'block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',

        // Table headers
        'thead' => 'bg-gray-50',
        'th' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider',
        'th_sort_button' => 'group inline-flex items-center hover:text-gray-700',

        // Table body
        'tbody' => 'bg-white divide-y divide-gray-200',
        'tr' => 'hover:bg-gray-50 transition-colors duration-200',
        'td' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-900',

        // Empty state
        'empty_wrapper' => 'text-center py-12',
        'empty_text' => 'text-gray-500 text-lg',
        'empty_description' => 'text-gray-400 text-sm mt-2',

        // Pagination
        'pagination_wrapper' => 'mt-6 flex items-center justify-between',
    ]
];
```

#### Component-Level Theme Overrides

Override theme settings for specific tables:

```php
public function render()
{
    return view('livewire.users-table', [
        'model' => User::class,
        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'actions' => 'Actions'
        ],
        'theme' => [
            'table' => 'min-w-full divide-y divide-blue-200',
            'tr' => 'hover:bg-blue-50',
            'td_id' => 'font-mono text-gray-500 text-xs',
            'td_actions' => 'text-right'
        ]
    ]);
}
```

Use in your view:

```blade
<livewire:livewire-datatable
    :model="$model"
    :columns="$columns"
    :theme="$theme" />
```

### Dynamic CSS Classes

#### Overview

Every single element in the datatable view is fully configurable from the configuration file. No need to modify the Blade templates to customize styling - all CSS classes are defined in `config/livewire-datatable.php`.

#### How It Works

1. **Configuration-Driven**: All CSS classes are stored in `config/livewire-datatable.php`
2. **Dynamic Binding**: Classes are applied using Laravel Livewire's `@class` directive
3. **Data Attributes**: Each element has a `data-class` attribute matching its config key
4. **Easy Debugging**: Inspect elements to see which config key controls the styling

#### Using Data Attributes for Debugging

Each element has a `data-class` attribute that matches its config key. Inspect elements in your browser to see which configuration keys control specific styling:

```blade
<!-- Example: Filter panel with data-class attribute -->
<div data-class="filter_panel" @class([$this->getClass('filter_panel')])>
    <div data-class="filter_header" @class([$this->getClass('filter_header')])>
        <!-- Content -->
    </div>
</div>
```

This makes it easy to:

1. Identify which config key controls a specific element
2. Customize styling without touching templates
3. Build theme packages
4. Debug styling issues

#### Column-Specific Styling

Style individual columns using column keys:

```php
'theme' => [
    'td_id' => 'font-mono text-gray-500 text-xs',
    'td_email' => 'font-medium text-blue-600',
    'td_status' => 'text-center font-semibold',
    'td_actions' => 'text-right space-x-2',
]
```

See the [complete theme configuration reference](#complete-theme-configuration-reference) for all available class keys.

### Pagination Options

Configure pagination behavior:

```php
// In config/livewire-datatable.php
return [
    'default_pagination' => 'paginate', // or 'simplePaginate'
    'per_page_options' => [10, 25, 50, 100]
];
```

Or override per component:

```php
'pagination' => 'simplePaginate',
'perPageOptions' => [5, 15, 30]
```

### Dark Mode Support

The package automatically supports Tailwind's dark mode. Just enable dark mode in your application:

```html
<html class="dark">
  <!-- Your app content -->
</html>
```

Or use dynamic dark mode switching:

```js
// Toggle dark mode
document.documentElement.classList.toggle("dark");
```

#### Complete Theme Configuration Reference

Here's the complete reference of all available CSS class configuration keys:

```php
// config/livewire-datatable.php
return [
    'theme' => [
        // Main wrapper and layouts
        'wrapper' => 'w-full border border-gray-200 rounded-sm dark:border-gray-700 grid grid-cols-4',
        'main_wrapper' => 'col-span-4',
        'main_wrapper_with_filter' => 'col-span-3',

        // Filter panel elements
        'filter_panel' => 'transition duration-300 ease-in-out p-4 border-r border-gray-200 dark:border-gray-700',
        'filter_header' => 'flex justify-between items-center',
        'filter_header_title' => 'text-sm text-gray-600 dark:text-gray-400',
        'filter_close_button' => 'inline-flex items-center text-sm font-medium text-gray-800 dark:text-white cursor-pointer hover:text-gray-500 dark:hover:text-gray-300',
        'filter_close_button_icon' => 'shrink-0 size-5',
        'filter_content' => 'flex flex-col mt-4 space-y-4',
        'filter_label' => 'text-sm text-gray-600 dark:text-gray-400',
        'filter_list' => 'list-filter',
        'filter_items' => 'max-w-sm space-y-3',
        'filter_item' => 'flex gap-2 items-center justify-between',
        'filter_input_wrapper' => 'relative',
        'filter_input' => 'py-2.5 sm:py-3 px-4 ps-33 block w-full border-gray-200 rounded-lg sm:text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400',
        'filter_select_wrapper' => 'absolute inset-y-0 start-0 flex items-center text-gray-500',
        'filter_select' => 'text-sm block w-30 border-l border-gray-200 dark:border-gray-700 rounded dark:text-gray-500 dark:bg-gray-900 py-3',
        'filter_select_label' => 'sr-only',
        'filter_delete_button' => 'inline-flex items-center text-xs font-medium text-gray-800 dark:text-white cursor-pointer hover:text-red-500 dark:hover:text-red-300',
        'filter_delete_button_wrapper' => '',
        'filter_delete_button_icon' => 'shrink-0 size-5',
        'filter_actions' => 'flex items-center justify-between',
        'filter_add_button' => 'py-2 pl-2 pr-3 inline-flex items-center gap-x-1 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700',
        'filter_add_button_icon' => 'shrink-0 size-5',
        'filter_reset_button' => 'py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-yellow-200 bg-white text-yellow-800 shadow-2xs hover:bg-yellow-50 focus:outline-hidden focus:bg-yellow-50 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-yellow-400 dark:hover:bg-gray-700',
        'filter_reset_button_icon' => 'shrink-0 size-5',
        'filter_apply_button' => 'py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-green-200 bg-white text-green-800 shadow-2xs hover:bg-green-50 focus:outline-hidden focus:bg-green-50 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-green-400 dark:hover:bg-gray-700',
        'filter_apply_button_icon' => 'shrink-0 size-5',
        'filter_button' => 'py-2 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-sm border border-gray-200 text-gray-800 hover:border-gray-500 hover:text-gray-500 focus:outline-hidden focus:border-gray-500 focus:text-gray-500 disabled:opacity-50 dark:border-gray-700 dark:text-white dark:hover:text-gray-300 dark:hover:border-gray-300',
        'filter_button_icon' => 'shrink-0 size-5',

        // Search and controls
        'search_wrapper' => 'mb-4 flex flex-col sm:flex-row items-center justify-between gap-4',
        'controls_wrapper' => 'flex justify-between items-center gap-4 w-full pt-3 px-3',
        'controls_layout_top' => 'flex items-center justify-between gap-4',
        'controls_layout_bottom' => 'flex items-center justify-between gap-2',

        // Per page controls
        'per_page_wrapper' => 'flex items-center gap-2',
        'per_page_select' => 'w-20 py-2.5 px-4 block border border-gray-300 rounded-sm text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 disabled:opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200',
        'per_page_text' => 'text-sm text-gray-400 dark:text-gray-500',

        // Search input
        'search_input_wrapper' => 'w-full sm:w-auto relative',
        'search_input' => 'w-full sm:w-auto pl-10 rounded-sm border px-2 py-1.5 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 shadow-sm disabled:cursor-not-allowed disabled:opacity-50',
        'search_icon_wrapper' => 'absolute inset-y-0 left-0 flex items-center pl-3',
        'search_icon' => 'h-5 w-5 text-gray-400 dark:text-gray-500',

        // Export controls
        'export_wrapper' => 'flex gap-2 items-center',
        'export_dropdown_wrapper' => 'relative',
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
```

## 📝 Complete Example

Here's a comprehensive example showing multiple features:

```php
<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;

class AdvancedUsersTable extends Component
{
    public $departmentFilter = '';

    #[On('user-edit')]
    public function editUser($id)
    {
        $this->dispatch('open-edit-modal', userId: $id);
    }

    #[On('user-status-toggle')]
    public function toggleUserStatus($id)
    {
        $user = User::find($id);
        $user->update(['status' => $user->status === 'active' ? 'inactive' : 'active']);
        session()->flash('message', 'User status updated!');
    }

    public function render()
    {
        return view('livewire.advanced-users-table', [
            'model' => User::class,
            'scope' => 'active',
            'columns' => [
                'no' => '#',
                'avatar' => '',
                'name' => 'Name',
                'email' => 'Email',
                'department.name' => 'Department',
                'role.name' => 'Role',
                'status' => 'Status',
                'created_at' => 'Joined',
                'actions' => 'Actions'
            ],
            'searchable' => ['name', 'email', 'department.name'],
            'unsortable' => ['avatar', 'actions'],
            'customColumns' => [
                'avatar' => 'components.table.user-avatar',
                'status' => 'components.table.status-badge',
                'created_at' => 'components.table.date-format',
                'actions' => 'components.table.user-actions'
            ],
            'theme' => [
                'td_avatar' => 'w-12',
                'td_actions' => 'text-right w-24'
            ]
        ]);
    }
}
```

## ✅ Summary

Here's a quick reference of all available features and how they work together:

### **Core Parameters**

- **`model`** → Your Eloquent model class (data source)

  ```php
  'model' => User::class
  ```

- **`scope`** → Apply custom query constraints using model scopes

  ```php
  'scope' => 'active' // Uses User::scopeActive()

  ```

- **`scopeParams`** → Apply custom query with params constraints using model scopes

  ```php
  'scopeParams' => ['completed'] // Uses User::scopeStatus('completed')
  ```

- **`columns`** → Define which fields to display and their labels

  ```php
  'columns' => [
    'no' => '#',
    'name' => 'Full Name',
    'department.name' => 'Department' // Relationship data
  ]
  ```

- **`searchable`** → Make specific fields searchable via the search input

  ```php
  'searchable' => ['name', 'email', 'department.name']
  ```

- **`unsortable`** → Prevent sorting on certain columns (like actions)

  ```php
  'unsortable' => ['actions', 'avatar']
  ```

- **`customColumns`** → Use custom Blade templates for rich cell content

  ```php
  'customColumns' => [
      'status' => 'components.table.status-badge',
      'actions' => 'components.table.action-buttons'
  ]
  ```

- **`theme`** → Customize styling globally or per-column
  ```php
  'theme' => [
      'table' => 'min-w-full divide-y divide-gray-200',
      'td_actions' => 'text-right w-24'
  ]
  ```

### **Complete Example**

```php
<livewire:livewire-datatable
    :model="User::class"
    :scope="active"
    :scopeParams="['...', '....']"
    :columns="[
        'no' => '#',
        'name' => 'Name',
        'department.name' => 'Department',
        'status' => 'Status',
        'actions' => 'Actions'
    ]"
    :searchable="['name', 'department.name']"
    :unsortable="['actions']"
    :custom-columns="[
        'status' => 'components.table.status-badge',
        'actions' => 'components.table.user-actions'
    ]"
    :theme="['td_actions' => 'text-right']" />
```

By combining these features, you can build a **dynamic, interactive, and highly customizable DataTable** that handles everything from simple listings to complex data presentations with custom actions, relationship data, and beautiful styling.

## ❓ Troubleshooting

### Common Issues

**Search not working on relationship columns:**

- Ensure the relationship is loaded with `$with` in your model
- Check that the relationship method exists and is correctly named

**Custom columns not displaying:**

- Verify the custom view file exists at the specified path
- Check that the view receives `$item` and `$value` variables

**Styles not applying:**

- Make sure Tailwind CSS is properly configured
- Verify the package views are included in Tailwind's content array

### Getting Help

1. Check the [GitHub issues](https://github.com/developerawam/livewire-datatable/issues)
2. Review the examples in this documentation
3. Join our community discussions

## 💝 Support

If this package has helped your project, consider supporting its continued development:

[![Donate on Saweria](https://img.shields.io/badge/Donate-Saweria-orange)](https://saweria.co/developerawam)

## 🔒 Security

Please report security vulnerabilities to info@developerawam.com instead of using the public issue tracker.

## 👥 Credits

- [Restu](https://github.com/restu-lomboe) - Lead Developer
- [Developer Awam](https://github.com/developerawam) - Organization

## 📄 License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

---

**Ready to build amazing data tables?** [Get started now](#-quick-start) and transform your Laravel applications with beautiful, interactive DataTables!
