# Template System Configuration

The datatable-livewire package now supports multiple templates for different CSS frameworks.

## Supported Templates

- **tailwind** (default) - Tailwind CSS based styling
- **bootstrap** - Bootstrap 5+ based styling

## Configuration

### Environment Variable

Set the template in your `.env` file:

```env
DATATABLE_TEMPLATE=tailwind
# or
DATATABLE_TEMPLATE=bootstrap
```

### Config File

Edit `config/livewire-datatable.php`:

```php
return [
    // ... other config options ...

    /**
     * Template System
     *
     * This option controls which template theme the datatable uses.
     * Supported themes: 'tailwind', 'bootstrap'
     */
    'template' => env('DATATABLE_TEMPLATE', 'tailwind'),

    /**
     * Bootstrap Template Config
     *
     * Configuration specific to Bootstrap template rendering.
     */
    'bootstrap' => [
        'version' => '5', // Bootstrap version
        'container_class' => 'container-fluid',
        'panel_class' => 'card',
        'button_size' => 'btn-sm', // btn-sm, btn-md (default), btn-lg
    ],
];
```

## Usage Example

### Using Tailwind Template

```php
// In your Livewire component or view
<livewire:datatable
    :model="Order::class"
    :columns="['id' => 'ID', 'name' => 'Order Name', 'total' => 'Total']"
/>
```

With `.env`:

```env
DATATABLE_TEMPLATE=tailwind
```

### Using Bootstrap Template

```php
// Same component code
<livewire:datatable
    :model="Order::class"
    :columns="['id' => 'ID', 'name' => 'Order Name', 'total' => 'Total']"
/>
```

With `.env`:

```env
DATATABLE_TEMPLATE=bootstrap
```

## Template Structure

Templates are organized as follows:

```
resources/views/templates/
├── tailwind/
│   └── datatable.blade.php      # Tailwind CSS template
└── bootstrap/
    └── datatable.blade.php      # Bootstrap 5+ template
```

## Creating Custom Templates

To create your own template:

1. Create a directory in `resources/views/templates/your-template/`
2. Create a `datatable.blade.php` file with your custom template
3. Update `config/livewire-datatable.php`:

```php
'template' => env('DATATABLE_TEMPLATE', 'your-template'),
```

4. Update the `render()` method in `DataTable.php` component to support your template:

```php
public function render()
{
    $template = config('livewire-datatable.template', 'tailwind');
    $viewName = match($template) {
        'bootstrap' => 'livewire-datatable::templates.bootstrap.datatable',
        'your-template' => 'livewire-datatable::templates.your-template.datatable',
        'tailwind' => 'livewire-datatable::templates.tailwind.datatable',
        default => 'livewire-datatable::templates.tailwind.datatable',
    };

    return view($viewName);
}
```

## Requirements

### For Tailwind Template

- Tailwind CSS ^3.0
- Alpine.js (included with Livewire)

### For Bootstrap Template

- Bootstrap 5 or higher
- Bootstrap Icons (optional, but recommended for better UX)

  ```html
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"
  />
  ```

## Switching Templates at Runtime

You can also switch templates dynamically:

```php
// In your application's config service provider or runtime
config(['livewire-datatable.template' => 'bootstrap']);
```

## Notes

- The component automatically loads the correct template based on the configuration
- All datatable functionality (filtering, sorting, pagination, export) works the same across all templates
- CSS classes are configurable in the Tailwind template via `config/livewire-datatable.php`
- Bootstrap template uses Bootstrap utility classes for consistency with Bootstrap 5+ frameworks
