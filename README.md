# Laravel Livewire DataTable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/developerawam/livewire-datatable.svg?style=flat-square)](https://packagist.org/packages/developerawam/livewire-datatable)
[![Total Downloads](https://img.shields.io/packagist/dt/developerawam/livewire-datatable.svg?style=flat-square)](https://packagist.org/packages/developerawam/livewire-datatable)
[![Donate on Saweria](https://img.shields.io/badge/Donate-Saweria-orange)](https://saweria.co/developerawam)

A powerful and flexible DataTable component for Laravel Livewire that transforms your data into beautiful, interactive tables with zero configuration required.

## 🎯 Quick Overview

- **Zero Configuration** — Works out of the box with just your Eloquent model
- **Server-Side Rendering** — Handles thousands of records efficiently
- **Feature-Rich** — Search, sort, filter, paginate, export, row selection, column visibility, and more
- **Fully Customizable** — Configure every aspect via config or per-component
- **Production Ready** — Built for real-world applications with proper error handling
- **Blaze Optimized** — Optional 91–97% rendering overhead reduction via `livewire/blaze`

---

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
  - [Row Selection](#row-selection)
  - [Per-Column Search](#per-column-search)
  - [Column Visibility Toggle](#column-visibility-toggle)
  - [Multi-Column Sort](#multi-column-sort)
  - [Saved Filter Presets](#saved-filter-presets)
  - [Row Numbering](#row-numbering-no-column)
- [Exporting Data](#-exporting-data)
- [API Integration](#-api-integration)
- [Performance: Blaze Integration](#-performance-blaze-integration)
- [Customization](#-customization)
  - [Template System](#template-system)
  - [Theme Configuration](#theme-configuration)
  - [Dynamic CSS Classes](#dynamic-css-classes)
  - [Dark Mode Support](#dark-mode-support)
  - [Pagination Options](#pagination-options)
- [API Reference](#-api-reference)
- [Troubleshooting](#-troubleshooting)
- [Support](#-support)

---

## ✨ Features

| Feature | Description |
|---|---|
| ⚡ **Server-Side Rendering** | Handle thousands of records efficiently |
| 🔍 **Smart Search** | Live search with debouncing across multiple columns |
| 🔎 **Per-Column Search** | Individual search inputs below each column header |
| 📊 **Column Sorting** | Sort by any column, including relationship data |
| 🔀 **Multi-Column Sort** | Sort by up to 3 columns simultaneously via Ctrl+Click |
| 🔤 **Advanced Filtering** | Multi-column filtering with smart cast-aware operators |
| 💾 **Saved Filter Presets** | Save and reload named filter combinations |
| ☑️ **Row Selection** | Per-row checkboxes with select-all and event dispatch |
| 👁️ **Column Visibility** | Toggle columns show/hide dynamically |
| 📄 **Pagination** | Fully customizable pagination with per-page options |
| 📤 **Data Export** | Export to Excel and PDF while respecting active filters |
| 🎨 **Dynamic Styling** | All CSS classes configurable from config file |
| 🌙 **Dark Mode** | Automatic dark mode support with Tailwind |
| 📱 **Responsive Design** | Mobile-friendly on all screen sizes |
| 🔗 **Relationships** | Display and sort by related model data using dot notation |
| 🎯 **Custom Templates** | Create custom cell content with Blade components |
| 🛠 **Event System** | Built-in event handling for user interactions |
| 🔧 **Zero Config** | Works out of the box with sensible defaults |
| 🎨 **Multi-Template** | Tailwind CSS and Bootstrap 5+ templates built-in |
| 🚀 **Blaze Ready** | Optional pre-compilation for 91-97% render speedup |

---

## 📋 Requirements

- **PHP**: ^8.2
- **Laravel**: ^12.0 \|\| ^13.0
- **Livewire**: ^4.0
- **CSS Framework**: Tailwind CSS ^3.0+ OR Bootstrap 5+

---

## 📦 Installation

### 1. Install Package

```bash
composer require developerawam/livewire-datatable
```

### 2. Configure Your CSS Framework

#### Tailwind CSS v3

```js
// tailwind.config.js
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./vendor/developerawam/livewire-datatable/resources/views/**/*.blade.php",
  ],
};
```

#### Tailwind CSS v4+

```css
/* resources/css/app.css */
@import "tailwindcss";
@source '../../vendor/developerawam/livewire-datatable/resources/views/**/*.blade.php';
```

#### Bootstrap 5+

No additional configuration needed — Bootstrap is detected automatically.

### 3. Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag="livewire-datatable-config"
```

### 4. Set Template (Optional)

```env
# .env
DATATABLE_TEMPLATE=tailwind   # default
DATATABLE_TEMPLATE=bootstrap
```

---

## 🚀 Quick Start

Create a fully functional DataTable in under 2 minutes.

### 1. Create Livewire Component

```bash
php artisan make:livewire UsersTable
```

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
            'model'      => User::class,
            'columns'    => [
                'id'         => 'ID',
                'name'       => 'Name',
                'email'      => 'Email',
                'created_at' => 'Joined',
            ],
            'searchable' => ['name', 'email'],
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

---

## 📖 Basic Usage

### Columns

Define what data to display and how to label it:

```php
'columns' => [
    'id'              => 'ID',
    'name'            => 'Full Name',
    'email'           => 'Email Address',
    'created_at'      => 'Joined Date',
    'department.name' => 'Department',   // Relationship via dot notation
]
```

### Searching

Make columns searchable:

```php
'searchable' => ['name', 'email', 'department.name']
```

### Sorting

All columns are sortable by default. Prevent sorting on specific columns:

```php
'unsortable' => ['actions', 'avatar']
```

### Value Formatting

#### Simple Formatters

```php
'formatters' => [
    'created_at' => 'datetime',
    'updated_at' => 'date',
    'balance'    => 'currency',
    'is_active'  => 'boolean',
    'name'       => 'uppercase',
]
```

> **Smart cast detection:** Boolean and integer columns automatically use exact match (`=`) instead of `LIKE` when filtering — no extra setup required.

#### Advanced Formatters

```php
'formatters' => [
    'description' => [
        'type'    => 'words',
        'options' => ['words' => 10, 'end' => '...'],
    ],
    'title' => [
        'type'    => 'limit',
        'options' => ['length' => 50, 'end' => '...'],
    ],
    'price' => [
        'type'    => 'money',
        'options' => [
            'symbol'       => 'Rp ',
            'decimals'     => 0,
            'decimal_point'=> ',',
            'thousand_sep' => '.',
        ],
    ],
]
```

#### Available Formatters

| Formatter | Description | Options |
|---|---|---|
| `date` | Format as date | `format: 'Y-m-d'` |
| `datetime` | Format as datetime | `format: 'Y-m-d H:i:s'` |
| `time` | Format as time | `format: 'H:i:s'` |
| `number` | Thousand separator | `decimals, decimal_point, thousand_sep` |
| `currency` | Currency format | `symbol, decimals, decimal_point, thousand_sep` |
| `boolean` | Yes / No | `true: 'Yes', false: 'No'` |
| `uppercase` | Uppercase text | — |
| `lowercase` | Lowercase text | — |
| `limit` | Truncate by characters | `length, end` |
| `words` | Truncate by word count | `words, end` |
| `markdown` | Markdown to HTML | — |
| `money` | Advanced currency | `symbol, decimals, decimal_point, thousand_sep` |

---

## 🔥 Advanced Features

### Relationships

Display and sort data from related models using dot notation.

#### 1. Setup Model

```php
class User extends Model
{
    protected $with = ['department', 'role'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
```

#### 2. Use Dot Notation

```php
'columns' => [
    'id'                  => 'ID',
    'name'                => 'Name',
    'department.name'     => 'Department',
    'department.location' => 'Office',
    'role.name'           => 'Role',
]
```

DataTable automatically handles relationship joins and makes them sortable.

---

### Custom Query Scopes

#### 1. Define Scope

```php
class User extends Model
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeFromDepartment(Builder $query, string $dept): Builder
    {
        return $query->whereHas('department', fn ($q) => $q->where('name', $dept));
    }
}
```

#### 2. Apply Scope

```php
// Simple scope
'scope' => 'active',

// Scope with parameters
'scope'       => 'fromDepartment',
'scopeParams' => ['Engineering'],
```

---

### Custom Cell Templates

#### 1. Define Custom Columns

```php
'customColumns' => [
    'status'  => 'components.table.status-badge',
    'actions' => 'components.table.user-actions',
],
'unsortable' => ['actions'],
```

#### 2. Create Template

```blade
{{-- resources/views/components/table/status-badge.blade.php --}}
@php
    $colors = [
        'active'   => 'bg-green-100 text-green-800',
        'inactive' => 'bg-red-100 text-red-800',
        'pending'  => 'bg-yellow-100 text-yellow-800',
    ];
@endphp
<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $colors[$value] ?? 'bg-gray-100 text-gray-800' }}">
    {{ ucfirst($value) }}
</span>
```

**Available variables in templates:** `$item` (model instance), `$value` (column value).

#### 3. Handle Events

```php
use Livewire\Attributes\On;

#[On('user-delete')]
public function deleteUser($id)
{
    User::findOrFail($id)->delete();
    $this->dispatch('reset-table'); // Refresh table
}
```

---

### Default Sort Configuration

```blade
<livewire:livewire-datatable
    :model="User::class"
    :columns="[...]"
    defaultSortField="created_at"
    defaultSortDirection="desc" />
```

Works with relationships:

```blade
defaultSortField="department.name"
defaultSortDirection="asc"
```

---

### Advanced Dynamic Filtering

Users can filter across multiple columns with an intuitive side panel.

#### Enable / Disable

```php
// config/livewire-datatable.php
'advanced_filter' => true,
```

#### How It Works

1. Click the **Filter** button to open the filter panel
2. Select a column and enter a value
3. Click **+ Filter..** to add more conditions (AND logic)
4. Click **Filter** to apply — or **Reset** to clear all
5. Export respects active filters

> **Smart operators:** Boolean/integer columns automatically use exact match (`=`). String columns use `LIKE %value%`. No extra config needed.

---

### Row Selection

Enable per-row checkboxes for bulk actions.

#### Enable

```blade
<livewire:livewire-datatable
    :model="User::class"
    :columns="[...]"
    :selectable="true" />
```

Or globally in config:

```php
// config/livewire-datatable.php
'selection' => ['enabled' => true],
```

#### Listen for Selection Changes

```php
use Livewire\Attributes\On;

#[On('datatable-selection-changed')]
public function onSelectionChanged(array $ids): void
{
    $this->selectedUserIds = $ids;
    // Now you can bulk delete, export, etc.
}
```

#### Available Methods (callable from JS or child components)

| Method | Description |
|---|---|
| `toggleSelect($id)` | Toggle a single row |
| `toggleSelectAll()` | Select / deselect all rows on current page |
| `clearSelection()` | Clear all selections |
| `isSelected($id)` | Check if an ID is selected |

---

### Per-Column Search

Show a search input below each column header — useful alongside global search.

#### Enable

```blade
<livewire:livewire-datatable
    :model="User::class"
    :columns="[...]"
    :per-column-search="true" />
```

Or globally:

```php
// config/livewire-datatable.php
'per_column_search' => true,
```

Per-column search is combined (AND) with any active global search and filters.

To clear programmatically:

```php
$this->dispatch('reset-table'); // Resets all
// Or call clearColumnSearch() / clearColumnSearchFor('email') on the component
```

---

### Column Visibility Toggle

Let users show/hide columns dynamically. State is persisted in session.

#### Enable

```blade
<livewire:livewire-datatable
    :model="User::class"
    :columns="[...]"
    :column-visibility="true" />
```

Or globally:

```php
// config/livewire-datatable.php
'column_visibility' => true,
```

A **columns** button appears in the toolbar. Users can uncheck any column to hide it. At least one column is always kept visible. Clicking **Show all** restores all columns.

---

### Multi-Column Sort

Sort by up to 3 columns simultaneously.

#### Enable

```blade
<livewire:livewire-datatable
    :model="User::class"
    :columns="[...]"
    :multi-sort="true" />
```

Or globally:

```php
// config/livewire-datatable.php
'multi_sort' => true,
```

#### How It Works

| Action | Result |
|---|---|
| **Click** a column header | Single-column sort (stack resets) |
| **Ctrl+Click** a column header | Add column to sort stack |
| **Ctrl+Click** an already-sorted column | Toggle its direction |
| Number badge on header | Sort priority (1 = primary, 2 = secondary, 3 = tertiary) |

Maximum 3 sort levels. Adding a 4th replaces the oldest.

---

### Saved Filter Presets

Save the current filter + sort state as a named preset and reload it later.

#### Enable

```blade
<livewire:livewire-datatable
    :model="User::class"
    :columns="[...]"
    :saved-filters="true" />
```

#### Driver: Session (default — no extra setup)

```php
// config/livewire-datatable.php
'saved_filters' => [
    'enabled' => true,
    'driver'  => 'session',
],
```

Presets are stored in PHP session — cleared on logout.

#### Driver: Database (persists across sessions, per user)

```php
// config/livewire-datatable.php
'saved_filters' => [
    'enabled' => true,
    'driver'  => 'database',
],
```

```bash
php artisan vendor:publish --tag="livewire-datatable-migrations"
php artisan migrate
```

This creates the `datatable_filter_presets` table.

#### How It Works

1. Set your filters and sort as desired
2. Click **+ Save current filter as preset** in the filter panel
3. Enter a name and click **Save**
4. Your preset appears in the list — click to reload it, **✕** to delete

---

### Row Numbering ("no" Column)

Add `'no' => 'No.'` to your columns for smart sequential numbering.

```php
'columns' => [
    'no'   => 'No.',
    'name' => 'Name',
    ...
]
```

- Continues correctly across pages (page 2 starts at 11, 21, etc.)
- Sort-direction aware when sorting by "no" column
- Not affected by active filters or search

---

## 📤 Exporting Data

Export to Excel (`.xlsx`) and PDF with one click.

### Configuration

```php
// config/livewire-datatable.php
'export' => [
    'enabled'     => true,
    'types'       => ['excel', 'pdf'],
    'orientation' => 'portrait',  // or 'landscape'
    'paper_size'  => 'a4',
    'dropdown'    => [
        'position'     => 'top',     // 'top', 'bottom', 'both'
        'trigger_text' => 'Export',
        'excel_text'   => 'Excel',
        'pdf_text'     => 'PDF',
    ],
],
```

### Behavior

- Exports **all records** (not just the current page)
- Respects active **search**, **filters**, and **sort**
- Filename includes search term or "filtered" suffix when applicable
- Formatting (dates, currency, etc.) is preserved in exports

---

## 🌐 API Integration

Use an external REST API as the data source instead of an Eloquent model.

### Setup

```php
class TodoTableApi extends Component
{
    public function render()
    {
        $apiConfig = [
            'url'                  => url('/api/todos'),
            'headers'              => ['Accept' => 'application/json'],
            'data_key'             => 'data',
            'total_key'            => 'total',
            'search_param'         => 'search',
            'sort_param'           => 'sort',
            'sort_direction_param' => 'direction',
            'per_page_param'       => 'per_page',
            'page_param'           => 'page',
        ];

        return view('livewire.todo-table-api', [
            'apiConfig'  => $apiConfig,
            'columns'    => ['id' => 'ID', 'title' => 'Title'],
            'searchable' => ['title'],
        ]);
    }
}
```

### View

```blade
<livewire:livewire-datatable
    :api-config="$apiConfig"
    :columns="$columns"
    :searchable="$searchable" />
```

### Required API Response Format

```json
{
  "data": [{ "id": 1, "title": "Task" }],
  "total": 100,
  "per_page": 10,
  "current_page": 1,
  "last_page": 10
}
```

### Query Parameters Sent

```
GET /api/todos?search=keyword&sort=title&direction=asc&per_page=10&page=1
```

### With Authentication

```php
$apiConfig = [
    'url'     => url('/api/todos'),
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Accept'        => 'application/json',
    ],
];
```

---

## 🚀 Performance: Blaze Integration

`livewire/blaze` pre-compiles anonymous Blade components into optimized PHP functions, eliminating 91–97% of rendering overhead.

> ⚠️ **Penting:** Blaze dirancang untuk **anonymous Blade components** (`x-component` syntax), **bukan** untuk Livewire component views. Jangan arahkan `Blaze::optimize()->in()` ke direktori Livewire views — ini akan menyebabkan error **"missing root tag"** karena Blaze dapat merusak root `<div>` yang dibutuhkan Livewire.

### Install

```bash
composer require livewire/blaze:^1.0
php artisan view:clear
```

### Cara Penggunaan yang Benar

Arahkan Blaze hanya ke direktori **anonymous Blade components** yang dipakai di dalam Livewire views kamu — bukan ke view Livewire itu sendiri.

```
resources/views/
├── livewire/           ← ❌ JANGAN arahkan Blaze ke sini
│   └── users-table.blade.php
└── components/         ← ✅ Arahkan Blaze ke sini
    ├── button.blade.php
    ├── badge.blade.php
    └── table/
        └── status-badge.blade.php
```

Konfigurasi di `AppServiceProvider`:

```php
use Livewire\Blaze\Blaze;

public function boot(): void
{
    if (class_exists(Blaze::class)) {
        Blaze::optimize()
            // ✅ Anonymous Blade components — aman
            ->in(resource_path('views/components'))

            // ✅ Ikon yang dirender berulang — cocok untuk memo
            ->in(resource_path('views/components/icons'), memo: true);

        // ❌ JANGAN lakukan ini — akan menyebabkan "missing root tag":
        // ->in(resource_path('views/livewire'))
        // ->in(resource_path('views/vendor/livewire-datatable'))
    }
}
```

### Strategi Optimasi

| Strategi | Kapan dipakai | Risiko |
|---|---|---|
| Standard compile | Komponen dengan props dinamis | Rendah |
| `memo: true` | Komponen yang dirender berulang dengan input sama (ikon, badge statis) | Sedang |
| `fold: true` | Komponen 100% statis tanpa variabel runtime | Tinggi — baca docs Blaze terlebih dahulu |

Untuk panduan lengkap, lihat [`docs/BLAZE.md`](docs/BLAZE.md).

---

## 🎨 Customization

### Template System

```php
// config/livewire-datatable.php
'template' => env('DATATABLE_TEMPLATE', 'tailwind'),
```

Available templates: `tailwind` (default), `bootstrap`.

For Bootstrap pagination styles, follow the [Livewire Bootstrap Pagination docs](https://livewire.laravel.com/docs/pagination#using-bootstrap-instead-of-tailwind).

### Theme Configuration

All CSS classes are configurable via `config/livewire-datatable.php`:

```php
'theme' => [
    'table'  => 'min-w-full divide-y divide-gray-200',
    'th'     => 'px-6 py-3 bg-gray-50 text-left text-xs font-medium uppercase',
    'td'     => 'px-6 py-4 whitespace-nowrap text-sm text-gray-700',
    'tr'     => 'hover:bg-gray-50 transition',
]
```

### Per-Column Styling

```php
'theme' => [
    'td_id'      => 'font-mono text-gray-500 text-xs',
    'td_email'   => 'font-medium text-blue-600',
    'td_status'  => 'text-center font-semibold',
    'td_actions' => 'text-right space-x-2',
]
```

### Component-Level Overrides

```php
public function render()
{
    return view('livewire.users-table', [
        'model'   => User::class,
        'columns' => ['id' => 'ID', 'name' => 'Name'],
        'theme'   => [
            'table' => 'min-w-full divide-y divide-blue-200',
            'tr'    => 'hover:bg-blue-50',
        ],
    ]);
}
```

### Dynamic CSS Classes

Every element has a `data-class` attribute for easy debugging. Inspect in browser DevTools to identify which config key controls each element.

### Dark Mode Support

```html
<!-- Enable dark mode -->
<html class="dark">...</html>
```

```js
// Toggle dynamically
document.documentElement.classList.toggle('dark');
```

### Pagination Options

```php
// config/livewire-datatable.php
'per_page_options' => [10, 25, 50, 100, 'all'],
```

The `'all'` option loads all records at once.

### Schema Cache

The package caches `Schema::getColumnListing()` to avoid redundant DB calls on every render:

```php
// config/livewire-datatable.php
'schema_cache_ttl' => 300, // seconds. Set to 0 to disable.
```

---

## 📝 Complete Example

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
        User::findOrFail($id)->delete();
        session()->flash('message', 'User deleted!');
        $this->dispatch('reset-table');
    }

    // Handle row selection changes
    #[On('datatable-selection-changed')]
    public function onSelectionChanged(array $ids): void
    {
        $this->selectedIds = $ids;
    }

    public function render()
    {
        return view('livewire.advanced-users-table', [
            'model'              => User::class,
            'scope'              => 'active',
            'columns'            => [
                'no'              => 'No.',
                'name'            => 'Name',
                'email'           => 'Email',
                'department.name' => 'Department',
                'role.name'       => 'Role',
                'status'          => 'Status',
                'created_at'      => 'Joined',
                'actions'         => 'Actions',
            ],
            'searchable'         => ['name', 'email'],
            'unsortable'         => ['actions'],
            'customColumns'      => [
                'status'  => 'components.table.status-badge',
                'actions' => 'components.table.user-actions',
            ],
            'formatters'         => [
                'created_at' => 'date',
                'status'     => 'uppercase',
            ],
            'defaultSortField'     => 'created_at',
            'defaultSortDirection' => 'desc',
        ]);
    }
}
```

```blade
{{-- resources/views/livewire/advanced-users-table.blade.php --}}
<div>
    <livewire:livewire-datatable
        :model="$model"
        :scope="$scope"
        :columns="$columns"
        :searchable="$searchable"
        :unsortable="$unsortable"
        :custom-columns="$customColumns"
        :formatters="$formatters"
        :default-sort-field="$defaultSortField"
        :default-sort-direction="$defaultSortDirection"
        :selectable="true"
        :column-visibility="true"
        :multi-sort="true"
        :per-column-search="true"
        :saved-filters="true" />
</div>
```

---

## ✅ API Reference

### Component Parameters

| Parameter | Type | Default | Description |
|---|---|---|---|
| `model` | `string` | `null` | Eloquent model class |
| `apiConfig` | `array` | `null` | API datasource config (use instead of `model`) |
| `columns` | `array` | `[]` | Column keys and labels |
| `searchable` | `array` | `[]` | Searchable field names |
| `unsortable` | `array` | `[]` | Non-sortable column keys |
| `customColumns` | `array` | `[]` | Map of column key → Blade view path |
| `formatters` | `array` | `[]` | Column formatter definitions |
| `formatterOptions` | `array` | `[]` | Options per formatter |
| `scope` | `string` | `null` | Eloquent scope name |
| `scopeParams` | `array` | `[]` | Parameters for scope |
| `defaultSortField` | `string` | `'created_at'` | Initial sort column |
| `defaultSortDirection` | `string` | `'desc'` | `'asc'` or `'desc'` |
| `theme` | `array` | `[]` | CSS class overrides |
| `selectable` | `bool` | `false` | Enable row checkboxes |
| `perColumnSearch` | `bool` | `false` | Enable per-column search inputs |
| `columnVisibility` | `bool` | `false` | Enable column show/hide toggle |
| `multiSort` | `bool` | `false` | Enable multi-column sort |
| `savedFilters` | `bool` | `false` | Enable save/load filter presets |

### Dispatched Events

| Event | Payload | Triggered When |
|---|---|---|
| `datatable-selection-changed` | `ids: array` | Row selection changes |
| `datatable-preset-saved` | `name: string` | A filter preset is saved |

### Listened Events

| Event | Description |
|---|---|
| `reset-table` | Resets pagination and refreshes the table |

---

## ❓ Troubleshooting

**Search not working on relationships**

Ensure the relation is eager-loaded on the model:

```php
protected $with = ['department', 'role'];
```

**Per-column search / filter not detecting columns**

Check that `schema_cache_ttl` is not serving stale schema. Run:

```bash
php artisan cache:clear
```

**Column visibility state not persisting**

Column visibility uses PHP session. Ensure sessions are configured and the user's session is active.

**Saved filter presets not saving (database driver)**

Run the migration:

```bash
php artisan vendor:publish --tag="livewire-datatable-migrations"
php artisan migrate
```

**Multi-sort Ctrl+Click not working**

Ensure your browser is not intercepting Ctrl+Click for other purposes. MetaKey (Cmd on Mac) also works.

**Error "missing root tag" setelah install Blaze**

Ini terjadi jika `Blaze::optimize()->in()` diarahkan ke direktori Livewire views. Blaze hanya boleh digunakan pada anonymous Blade components, **bukan** Livewire component views.

Pindahkan konfigurasi Blaze ke direktori `components/` saja:

```php
// ✅ Benar
Blaze::optimize()->in(resource_path('views/components'));

// ❌ Salah — akan menyebabkan "missing root tag"
Blaze::optimize()->in(resource_path('views/livewire'));
Blaze::optimize()->in(resource_path('views/vendor/livewire-datatable'));
```

Setelah memperbaiki, jalankan:

```bash
php artisan view:clear
```

Lihat [`docs/BLAZE.md`](docs/BLAZE.md) untuk panduan lengkap.

**Custom columns not displaying**

- Verify the Blade view file exists at the specified path
- Confirm the view receives `$item` and `$value` variables
- Check template is not cached: `php artisan view:clear`

**Export not working**

- Verify `export.enabled` is `true` in config
- Ensure `maatwebsite/excel` and `barryvdh/laravel-dompdf` are installed

**Styles not applying (Tailwind)**

- Confirm package views are in `tailwind.config.js` content array
- For Tailwind v4, check `@source` directive in `app.css`
- Run `npm run build` to recompile assets

---

## 💝 Support

If this package has helped your project, consider supporting its continued development:

[![Donate on Saweria](https://img.shields.io/badge/Donate-Saweria-orange)](https://saweria.co/developerawam)

---

## 🔒 Security

Please report security vulnerabilities to **info@developerawam.com** instead of using the public issue tracker.

---

## 👥 Credits

- [Developer Awam](https://github.com/developerawam) — Package Author
- [Restu](https://github.com/restu-lomboe) — Lead Developer

---

## 📄 License

Licensed under the MIT License — see [LICENSE.md](LICENSE.md) for details.

---

**Ready to build amazing DataTables?** [Get started now](#-quick-start) and transform your Laravel applications with beautiful, interactive tables!
