<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Per Page Options
    |--------------------------------------------------------------------------
    |
    | This option controls the default pagination options that are shown
    | in the datatable. You can modify these to any values you want.
    |
    */
    'per_page_options' => [10, 25, 50, 100],

    /*
    |--------------------------------------------------------------------------
    | CSS Classes
    |--------------------------------------------------------------------------
    |
    | Here you can modify the CSS classes for various elements of the datatable.
    | This allows for complete customization of the datatable's appearance.
    |
    */
    'theme' => [
        'wrapper' => 'w-full',
        'search_wrapper' => 'mb-4 flex flex-col sm:flex-row items-center justify-between gap-4',
        'controls_wrapper' => 'flex justify-between sm:flex-row items-center gap-4 w-full justify-between',

        // Per page select
        'per_page_wrapper' => 'w-full sm:w-36',
        'per_page_select' => 'w-full sm:w-auto rounded-lg border p-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 shadow-sm',

        // Search input
        'search_input_wrapper' => 'w-full sm:w-auto relative',
        'search_input' => 'w-full sm:w-auto pl-10 rounded-lg border p-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 shadow-sm',

        // Table
        'table_wrapper' => 'overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 shadow',
        'table' => 'min-w-full divide-y divide-gray-200 dark:divide-gray-700',

        // Table head
        'thead' => '',
        'thead_row' => '',
        'th' => 'px-6 py-3 bg-gray-50 dark:bg-gray-700/50 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider',
        'th_sort_button' => 'group inline-flex items-center gap-x-2 hover:text-gray-700 dark:hover:text-gray-200',
        'th_sort_icon_wrapper' => 'inline-flex rounded p-1 transition',
        'th_sort_icon_active' => 'size-4 text-blue-500',
        'th_sort_icon_inactive' => 'size-4 text-gray-400 dark:text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-200',

        // Table body
        'tbody' => 'divide-y divide-gray-200 dark:divide-gray-700',
        'tr' => 'hover:bg-gray-50 dark:hover:bg-gray-700/25 transition',
        'td' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200',

        // Column-specific cell styling
        'td_id' => 'font-mono text-gray-500 dark:text-gray-400',  // Example: ID column styling
        'td_created_at' => 'text-xs',  // Example: Date column styling
        'td_status' => 'text-center',  // Example: Status column styling
        'td_email' => 'font-medium',   // Example: Email column styling
        'td_actions' => 'text-right space-x-2',  // Example: Actions column styling

        // Empty state
        'empty_wrapper' => 'px-6 py-8 text-center',
        'empty_content' => 'flex flex-col items-center justify-center',
        'empty_icon' => 'size-16 text-gray-400 dark:text-gray-500 mb-2',
        'empty_text' => 'text-gray-500 dark:text-gray-400 text-sm font-medium',

        // Pagination
        'pagination_wrapper' => 'mt-4',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Pagination Options
    |--------------------------------------------------------------------------
    |
    | This option controls the default pagination links that are shown
    | in the datatable. You can modify these to any values you want.
    | default use paginate, you can change to simplePagination
    |
    */
    'default_pagination' => 'paginate',

    /*
    |--------------------------------------------------------------------------
    | Default Sort Direction
    |--------------------------------------------------------------------------
    |
    | This option controls the default sort direction for all columns
    | in the datatable.
    |
    */
    'default_sort_direction' => 'asc',

    /*
    |--------------------------------------------------------------------------
    | Debounce Time (ms)
    |--------------------------------------------------------------------------
    |
    | This option controls the debounce time for the search input in milliseconds.
    |
    */
    'search_debounce' => 300,
];
