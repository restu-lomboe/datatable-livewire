# Laravel Livewire DataTable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/developerawam/livewire-datatable.svg?style=flat-square)](https://packagist.org/packages/developerawam/livewire-datatable)
[![Total Downloads](https://img.shields.io/packagist/dt/developerawam/livewire-datatable.svg?style=flat-square)](https://packagist.org/packages/developerawam/livewire-datatable)
[![Donate on Saweria](https://img.shields.io/badge/Donate-Saweria-orange)](https://saweria.co/developerawam)

A powerful, flexible DataTable component for Laravel Livewire applications with built-in sorting, searching, and pagination.

![Laravel Livewire DataTable](./datatable.png)

## 📚 Table of Contents

1. [Features](#-features)
2. [Requirements](#-requirements)
3. [Installation](#-installation)
4. [Quick Start](#-quick-start)
5. [Advanced Usage](#-advanced-usage)
6. [Customization](#-customization)
7. [Examples](#-examples)
8. [Support](#-support)

## ✨ Features

- 🔍 Live Search with debouncing
- 🔄 Column Sorting (with relationship support)
- 📄 Dynamic Pagination
- 🎨 Fully Customizable Theming
- 🌓 Dark Mode Support
- 📱 Responsive Design
- 🔗 Relationship Column Support
- ⚡ Real-time Updates
- 🎯 Custom Cell Templates
- 🛠 Event-driven Architecture

## 🔧 Requirements

- PHP ^8.2
- Laravel ^12.0
- Livewire ^3.0
- Tailwind CSS ^3.0

### Browser Support

- Chrome, Firefox, Safari, Edge (latest versions)

## 📦 Installation

1. Install via Composer:

```bash
composer require developerawam/livewire-datatable
```

2. Add to Tailwind content in `tailwind.config.js`:

```js
module.exports = {
  content: [
    "./vendor/developerawam/livewire-datatable/resources/views/**/*.blade.php",
  ],
};
```

3. Optional: Publish configuration:

```bash
php artisan vendor:publish --tag="livewire-datatable-config"
```

## 🚀 Quick Start

```php
use App\Models\User;
use Livewire\Component;

class UsersTable extends Component
{
    public function render()
    {
        return view('livewire.users-table', [
            'model' => User::class,
            'columns' => [
                'id' => '#',
                'name' => 'Name',
                'email' => 'Email',
                'created_at' => 'Created At'
            ],
            'searchable' => ['name', 'email']
        ]);
    }
}
```

2. Create the blade view:

```blade
 <livewire:livewire-datatable
    :model="$model"
    :columns="$columns"
    :searchable="$searchable" />
```

That's it! You now have a fully functional DataTable with search and sorting.

## 🔥 Advanced Usage

### 🔗 Relationship Columns

Add protected with in model to include relationship on the model::class

```php
protected $with = ['department', 'roles'];
```

Display and sort by relationship fields:

```php
'columns' => [
    'id' => '#',
    'name' => 'Name',
    'department.name' => 'Department',  // Relationship column
    'roles.name' => 'Role',            // Another relationship
]
```

### 🎯 Custom Cell Templates

1. Define custom column views:

```php
public function render()
{
    return view('livewire.users-table', [
        'columns' => [
            'id' => '#',
            'name' => 'Name',
            'actions' => 'Actions'
        ],
        'customColumns' => [
            'actions' => 'components.table.actions'
        ]
    ]);
}
```

2. Create the template (`resources/views/components/table/actions.blade.php`):

```blade
<div class="flex space-x-2">
    <button wire:click="$dispatch('user-edit', { id: {{ $item->id }} })"
            class="text-blue-600 hover:text-blue-800">
        <x-heroicon-s-pencil class="h-5 w-5" />
    </button>
</div>
```

3. Handle events in your component:

````php
use Livewire\Attributes\On;

class UsersTable extends Component
{
    #[On('user-edit')]
    public function editUser($id)
    {
        // Handle edit action
    }
}

You can render custom HTML elements (like buttons, links, or any other custom content) in table cells using custom views:

```php
// In your Livewire component
public function render()
{
    return view('livewire.users-table', [
        'model' => User::class,
        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'actions' => 'Actions'  // This column will use a custom view
        ],
        'customColumns' => [
            'actions' => 'components.table.action-buttons'  // Path to your custom view
        ]
    ]);
}
````

Create a blade view for your custom cell content (e.g., `resources/views/components/table/action-buttons.blade.php`):

````blade
// The view receives $item (the model instance) and $value (the column value)
Create a blade view for your custom cell content (e.g., `resources/views/components/table/action-buttons.blade.php`):

```blade
// The view receives $item (the model instance) and $value (the column value)
<div class="flex space-x-2">
    <button wire:click="$dispatch('user-edit', { id: {{ $item->id }} })" class="text-blue-600 hover:text-blue-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
        </svg>
    </button>
    <button wire:click="$dispatch('user-delete', { id: {{ $item->id }} })" class="text-red-600 hover:text-red-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
    </button>
</div>
````

Then in your Livewire component, add event listeners for the actions:

```php
use Livewire\Attributes\On;

class UsersTable extends Component
{
    #[On('user-edit')]
    public function editUser($id)
    {
        // $id contains the user ID
        // Add your edit logic here
    }

    #[On('user-delete')]
    public function deleteUser($id)
    {
        // Add your delete logic here
        $user = User::find($id);
        if ($user) {
            $user->delete();
            // Optionally dispatch another event
            $this->dispatch('user-deleted');
        }
    }

    public function render()
    {
        return view('livewire.users-table', [
            'model' => User::class,
            'columns' => [
                'id' => 'ID',
                'name' => 'Name',
                'email' => 'Email',
                'actions' => 'Actions'
            ],
            'searchable' => ['name', 'email'],
            'customColumns' => [
                'actions' => 'components.table.action-buttons'
            ],
            'unsortable' => ['actions'] // Make sure action column is not sortable
        ]);
    }
}
```

```blade
 <livewire:livewire-datatable
    :model="$model"
    :columns="$columns"
    :searchable="$searchable"
    :unsortable="$unsortable"
    :custom-columns="$customColumns"
    :theme="[
        'td_description' => 'w-[32rem] !whitespace-normal cell-description',
        'td_actions' => 'text-right',
    ]" />
```

The column-specific classes (`td_columnname`) are applied in addition to the default `td` classes, giving you fine-grained control while maintaining consistent base styling.

The custom view receives two variables:

- `$item`: The current model instance (e.g., User model)
- `$value`: The value of the current column (useful for formatting existing data)

Key points for action buttons:

1. Use `wire:click="$dispatch('event-name', { id: {{ $item->id }} })"` to trigger events
2. Events are caught using the `#[On('event-name')]` attribute in your component
3. Event handlers receive the data as an array with the ID
4. Remember to mark action columns as unsortable
5. You can dispatch additional events after actions complete

## Theming

The package comes with a beautiful default theme using Tailwind CSS, but you can fully customize it.

### Global Theme Configuration

In your config/livewire-datatable.php:

```php
return [
    'theme' => [
        // Wrapper
        'wrapper' => 'w-full',
        'table_wrapper' => 'overflow-x-auto rounded-lg shadow',
        'table' => 'min-w-full divide-y divide-gray-200',

        // Controls
        'search_wrapper' => 'mb-4 flex items-center',
        'search_input' => 'rounded-md border-gray-300 shadow-sm',

        // Headers
        'th' => 'px-6 py-3 bg-gray-50 text-left',
        'th_sort_button' => 'group inline-flex items-center',

        // Body
        'tbody' => 'divide-y divide-gray-200',
        'tr' => 'hover:bg-gray-50',
        'td' => 'px-6 py-4 whitespace-nowrap',

        // Empty State
        'empty_wrapper' => 'text-center py-10',
        'empty_text' => 'text-gray-500',

        // Pagination
        'pagination_wrapper' => 'mt-4',
    ],
];
```

## 🎨 Customization

### 🌈 Theme Configuration

Customize the appearance in `config/livewire-datatable.php`:

```php
return [
    'theme' => [
        // Base styling
        'table' => 'min-w-full divide-y divide-gray-200',
        'tr' => 'hover:bg-gray-50',

        // Column-specific styling
        'td_id' => 'font-mono text-gray-500',
        'td_actions' => 'text-right space-x-2',
    ]
];
```

### 🌓 Dark Mode

The package automatically supports dark mode when your app uses Tailwind's dark mode:

```html
<html class="dark">
  <!-- Your content -->
</html>
```

## Dark Mode Support

The package automatically supports dark mode when your application uses Tailwind's dark mode. No additional configuration needed!

## 💪 Support

Found this package helpful? Consider supporting its development:

[![Donate on Saweria](https://img.shields.io/badge/Donate-Saweria-orange)](https://saweria.co/developerawam)

### 🔒 Security

Please report security issues to info@developerawam.com instead of using the issue tracker.

## 👥 Credits

- [Restu](https://github.com/restu-lomboe)
- [Developer Awam](https://github.com/developerawam)

## 📄 License

The MIT License (MIT). See [License File](LICENSE.md) for more information.
