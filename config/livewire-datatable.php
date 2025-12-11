<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Per Page Options
    |--------------------------------------------------------------------------
    |
    | This option controls the default pagination options that are shown
    | in the datatable. You can modify these to any values you want.
    | Use 'all' to represent "All" records.
    |
    */
    'per_page_options' => [10, 25, 50, 100, 'all'],

    /*
    |--------------------------------------------------------------------------
    | Export Options
    |--------------------------------------------------------------------------
    |
    | Configure the export functionality of the datatable here.
    |
    */
    'export' => [
        'enabled' => true,
        'types' => ['excel', 'pdf'], // supported types: 'excel', 'pdf'
        'orientation' => 'portrait', // portrait or landscape
        'paper_size' => 'a4',
        'dropdown' => [
            'position' => 'top', // top, bottom, both
            'trigger_class' => 'inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-sm hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700',
            'menu_class' => 'absolute left-0 z-10 mt-2 w-35 origin-top-right rounded-sm bg-white dark:bg-gray-800 shadow-lg ring-1 ring-gray-900/5 ring-opacity-5 focus:outline-none',
            'item_class' => 'block w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-left',
            'trigger_text' => 'Export',
            'excel_text' => 'Excel',
            'pdf_text' => 'PDF',
        ],
    ],

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
        'wrapper' => 'w-full border border-gray-200 rounded-sm dark:border-gray-700',
        'search_wrapper' => 'mb-4 flex flex-col sm:flex-row items-center justify-between gap-4',
        'controls_wrapper' => 'flex justify-between sm:flex-row items-center gap-4 w-full justify-between pt-3 px-3',

        // Per page select
        'per_page_wrapper' => 'flex items-center gap-2',
        'per_page_select' => 'w-20 py-2.5 px-4 block border border-gray-300 rounded-sm text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200',

        // Search input
        'search_input_wrapper' => 'w-full sm:w-auto relative',
        'search_input' => 'w-full sm:w-auto pl-10 rounded-sm border px-2 py-1.5 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 shadow-sm disabled:cursor-not-allowed disabled:opacity-50',
        'search_icon_wrapper' => 'absolute inset-y-0 left-0 flex items-center pl-3',
        'search_icon' => 'h-5 w-5 text-gray-400 dark:text-gray-500',
        'export_wrapper' => 'flex gap-2 items-center',
        'export_button' => 'px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800',
        'export_icon' => 'w-4 h-4 mr-2',

        // Table
        'table_wrapper' => 'overflow-x-auto border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow',
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
        // 'td_id' => 'font-mono text-gray-500 dark:text-gray-400', // Example: ID column styling
        // 'td_created_at' => 'text-xs', // Example: Date column styling
        // 'td_status' => 'text-center', // Example: Status column styling
        // 'td_email' => 'font-medium', // Example: Email column styling
        // 'td_actions' => 'text-right space-x-2', // Example: Actions column styling

        // Empty state
        'empty_wrapper' => 'px-6 py-8 text-center',
        'empty_content' => 'flex flex-col items-center justify-center',
        'empty_icon' => 'size-16 text-gray-400 dark:text-gray-500 mb-2',
        'empty_text' => 'text-gray-500 dark:text-gray-400 text-sm font-medium',

        // Pagination
        'pagination_wrapper' => 'p-4',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Pagination Options
    |--------------------------------------------------------------------------
    |
    | This option controls the default pagination links that are shown
    | in the datatable. You can modify these to any values you want.
    | default use paginate, you can change to simplePaginate
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
