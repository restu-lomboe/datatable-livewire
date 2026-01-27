# Bootstrap Template Dynamic Classes

The Bootstrap template now supports fully configurable CSS classes through the `bootstrap_theme` configuration array in `config/config.php`. This allows you to customize every aspect of the Bootstrap table styling without modifying template files.

## Configuration Overview

All Bootstrap classes are organized by component in the `bootstrap_theme` array:

### Container & Layout Classes

- `container` - Main container wrapper
- `card` - Card wrapper for the main table
- `card_header` - Header section styling
- `card_body` - Body section styling
- `card_footer` - Footer section styling

### Filter Panel Classes

- `filter_panel` - Filter panel card wrapper
- `filter_header` - Filter header styling
- `filter_header_title` - Filter title styling
- `filter_close_button` - Close button styling
- `filter_content` - Filter content area
- `filter_items` - Filter items container
- `filter_item` - Individual filter item
- `filter_input_group` - Input group wrapper
- `filter_input` - Filter input field
- `filter_select` - Filter select dropdown
- `filter_delete_button` - Delete filter button
- `filter_actions` - Filter action buttons container
- `filter_add_button` - Add filter button
- `filter_reset_button` - Reset filter button
- `filter_apply_button` - Apply filter button

### Header Controls Classes

- `header_row` - Header row wrapper
- `search_col` - Search column sizing
- `controls_col` - Controls column sizing
- `search_input_group` - Search input group
- `search_input` - Search input field
- `search_icon` - Search icon styling

### Control Elements Classes

- `controls_flex` - Controls flex container
- `per_page_group` - Per page control group
- `per_page_select` - Per page select dropdown
- `filter_button` - Filter button styling
- `export_dropdown` - Export dropdown wrapper
- `export_button` - Export button styling
- `export_menu` - Export menu styling
- `export_item` - Export menu items

### Table Classes

- `table_responsive` - Responsive table wrapper
- `table` - Main table element classes
- `table_style` - Inline table styles
- `thead` - Table header styling
- `thead_row` - Header row styling
- `th` - Header cell styling
- `th_button` - Header button styling
- `th_button_style` - Header button inline styles
- `th_sort_icon` - Sort icon spacing
- `sort_icon_asc` - Ascending sort icon
- `sort_icon_desc` - Descending sort icon
- `sort_icon_neutral` - Neutral sort icon
- `sort_icon_color_active` - Active sort icon color/style
- `sort_icon_color_inactive` - Inactive sort icon color/style
- `sort_icon_inactive_class` - Inactive sort icon CSS classes

### Body Classes

- `tbody` - Table body element
- `tr` - Table row styling
- `td` - Table cell styling

### Empty State Classes

- `empty_wrapper` - Empty state wrapper
- `empty_content` - Empty state content
- `empty_icon` - Empty state icon
- `empty_icon_style` - Empty state icon inline styles

### Pagination Classes

- `pagination_wrapper` - Pagination wrapper
- `pagination_info` - Pagination info text
- `pagination_controls` - Pagination controls container

## Customization Examples

### Change table styling

```php
'table' => 'table table-striped table-bordered align-middle mb-0',
```

### Customize button colors

```php
'filter_add_button' => 'btn btn-sm btn-success',
'filter_reset_button' => 'btn btn-sm btn-outline-danger',
'filter_apply_button' => 'btn btn-sm btn-primary',
```

### Modify card appearance

```php
'card' => 'card border-1 shadow-lg rounded-2',
'card_header' => 'card-header bg-primary text-white py-4 px-5',
```

### Update responsive breakpoints

```php
'search_col' => 'col-md-8 col-lg-7',
'controls_col' => 'col-md-4 col-lg-5',
```

## How It Works

The Bootstrap template uses Blade's `@class()` directive to dynamically apply classes from the configuration:

```blade
<table class="@class([config('livewire-datatable.bootstrap_theme.table')])" style="{{ config('livewire-datatable.bootstrap_theme.table_style') }}">
```

This approach ensures:

- ✅ All customization is done through configuration
- ✅ No need to modify template files
- ✅ Easy to override in environment-specific configs
- ✅ Consistent with Tailwind template approach
- ✅ Full Bootstrap utility class support

## Default Classes

All classes use Bootstrap 5+ standard utilities by default:

- Color utilities: `bg-primary`, `text-secondary`, `text-danger`
- Spacing: `py-3`, `px-4`, `ms-2`, `gap-2`
- Display: `d-flex`, `d-grid`, `d-none`, `d-sm-inline`
- Borders: `border-bottom`, `border-opacity-25`
- Sizing: `form-select-sm`, `btn-sm`
- Effects: `shadow-sm`, `opacity-50`

Refer to [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/) for all available utilities.
