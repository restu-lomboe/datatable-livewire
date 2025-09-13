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
7. [Customization](#-customization)
8. [Examples](#-examples)
9. [Support](#-support)

## ✨ Features

- ⚡ **Server-side Rendering** - Handle thousands of records efficiently
- 🔍 **Smart Search** - Live search with intelligent debouncing
- 📊 **Column Sorting** - Sort by any column, including relationships
- 📄 **Dynamic Pagination** - Customizable pagination with multiple options
- 🎨 **Theming System** - Fully customizable with Tailwind CSS
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

#### 3. Use in Your View

```blade
<livewire:livewire-datatable
    :model="$model"
    :scope="$scope"
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
        $this->redirect(route('users.edit', $user));
        $this->dispatch('reset-table'); // Refresh table to after edit

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

            $this->dispatch('reset-table'); // Refresh table to after deletion
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
- `$this->dispatch('reset-table')`: Refresh (auto refresh) table to after action done

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
    scope="active"
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
