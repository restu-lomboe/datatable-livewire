# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),  
and this project adheres to [Semantic Versioning](https://semver.org/).

---

## [Unreleased]

### ✨ New Features

- **Custom Export with Column Selection**:
  - Interactive modal to choose which columns to export
  - Select All / Deselect All controls for quick selection
  - Columns grouped by their source table (main table + relations)
  - Smart column detection — excludes internal fields (`id`, `password`, `remember_token`, timestamps, soft deletes)
  - Per-export paper size (A4, Letter, Legal) and orientation (Portrait, Landscape)
  - Export to Excel or PDF with custom column selection
  - Dark mode support for the entire modal UI
  - Works across all template systems (Tailwind, Bootstrap)
  - Configurable theme classes for all modal elements

- **Per-Export Paper Size & Orientation**:
  - PDF exports now accept `$paperSize` and `$orientation` parameters
  - Falls back to config defaults when not specified
  - Dynamic PDF font sizing (`140 / columnCount`) and cell padding (`80 / columnCount`)
  - `word-break: break-word` prevents content overflow in PDF cells

- **Enhanced Export API**:
  - `export(string $type, ?array $selectedColumns, ?string $paperSize, ?string $orientation)` — supports passing selected columns, paper size, and orientation
  - `customExport()` action for Livewire modal integration
  - `#[Computed] exportColumns` property — lists all exportable columns with labels
  - `showCustomExportPanel()` / `closeCustomExport()` actions for modal management
  - `toggleCustomExportColumn()`, `selectAllExportColumns()`, `deselectAllExportColumns()` for column selection

### 🔧 Improvements

- Default export (Excel/PDF buttons) remains fully backward compatible
- Existing export respects filters and search as before
- DOMPDF options: `isHtml5ParserEnabled`, `isFontSubsettingEnabled`, `defaultFont => serif`, `dpi => 96` for better PDF rendering
- `@page { margin: 10mm; }` for consistent PDF margins

### 🎨 UI/UX

- Custom Export option added to export dropdown with visual separator
- Modal with checkboxes organized by table groups
- Livewire live model binding for instant UI updates
- Disabled submit button when no columns selected
- Smooth dark mode adaptation for all modal elements

### 📚 Documentation

- Added custom export feature documentation with usage examples
- Configuration reference for all custom export theme classes
- PDF export customization guide (paper size, orientation, dynamic sizing)

---

## [v1.4.0] - 2025-12-11

### ✨ New Features

- **Advanced Dynamic Filter System**:
  - Multiple column filtering with configurable UI
  - Filter by multiple conditions on same table
  - Real-time filter application with visual feedback
  - Filter state management (add, delete, reset)
  - Works seamlessly with sorting and pagination
  - Support for text input and dropdown filters
  - Enable/disable filters via configuration
  - Advanced filter panel with collapsible interface

- **Default Sort Configuration**:
  - Custom default sort field per table
  - Custom default sort direction (asc/desc)
  - Maintains default sort on page load
  - Flexible sort reset with filtering
  - Supports sorting by relationships

- **Fully Dynamic CSS Classes**:
  - All CSS classes moved to configuration file
  - Dynamic class binding for all UI elements
  - Data attributes for debugging and inspection
  - Complete theme customization without template changes
  - Icon styling configuration (SVG classes)
  - Layout wrapper configuration
  - Text styling configuration

- **Enhanced Export with Filtering**:
  - Export respects active filters and search
  - Export with applied sorting
  - Filtered exports with proper naming convention
  - Support for searched data export

### 🔧 Improvements

- Improved sorting behavior when filtering is active
- Better pagination handling with active filters
- Reset sort field when filtering data
- Dynamic class extraction via `getClass()` method
- All SVG icon classes configurable
- All layout wrapper classes configurable
- Per-page text styling configurable
- Column header text styling configurable

### 🏗 Architecture

- Implemented `filterData()` method for filtering logic
- Enhanced `getQuery()` computed property for filtered queries
- Improved sort reset mechanism
- Dynamic theme loading from configuration
- Trait-based approach maintained for extensibility

### 🎨 UI/UX

- Professional filter panel with icons
- Smooth animations and transitions
- Responsive filter controls
- Dark mode support for all new elements
- Better visual hierarchy in filter UI
- Collapsible filter sections
- Visual feedback for active filters

### 🔐 Security

- Filter validation with model scope
- Safe query parameter handling
- Protection against unauthorized filtering

### 📚 Documentation

- Added advanced filtering documentation
- Filter configuration examples
- Default sort configuration guide
- Theme customization guide
- Dynamic class configuration reference
- CSS class reference with data-class attributes

## [v1.3.4] - 2025-12-10

### 🔧 Improvements

- Fixed UI filter adjustments
- Enhanced filter configuration options

## [v1.3.0] - 2025-10-18

### ✨ New Features

- Added advanced value formatting system:
  - Simple formatters (date, datetime, currency, boolean, etc.)
  - Complex formatters with customizable options
  - Custom date format patterns support
  - Flexible number and currency formatting
  - Text manipulation (limit, words, markdown)
  - Support for both Model and API data sources

- Improved pagination system:
  - Support for both default and simple pagination
  - Total count display in simple pagination mode
  - Consistent behavior across data sources
  - Query parameter preservation in pagination links

- Enhanced API integration:
  - Support for "Show All" pagination in API data sources
  - Consistent API response formatting for all data modes
  - Smart handling of per_page=all or null for full dataset retrieval
  - Two-step process for efficient all-records fetching
  - Flexible response mapping
  - Customizable query parameters
  - Robust error handling
  - Support for nested API responses

### 🔧 Improvements

- Refactored formatting logic into WithFormatters trait
- Added support for custom formatter options
- Improved type declarations and PHP 8.2 compatibility
- Better error handling for API responses

### 🏗 Architecture

- Introduced WithFormatters trait for better code organization
- Improved separation of concerns in data handling
- Enhanced type safety across components
- Better abstraction for data sources

### 📚 Documentation

- Added comprehensive formatter documentation
- Improved API integration examples
- Added date format pattern examples
- Updated configuration examples

## [v1.3.0] - 2025-10-18

### ✨ New Features

- Added comprehensive export functionality:
  - Export to Excel and PDF formats
  - Support for exporting all data regardless of pagination
  - Configurable export buttons with dropdown interface
  - Custom filename generation with search context
  - Proper formatting in exported files
  - Exclude action columns from exports
  - Support for all data types and relationships

- Enhanced pagination system:
  - Added "Show All" records option
  - Dynamic handling of large datasets
  - Improved performance with optimized queries
  - Maintains search and sort functionality
  - Smooth transition between page sizes

### 🔧 Improvements

- Optimized data export for large datasets
- Better handling of formatters in exports
- Improved memory efficiency for large exports
- Enhanced search functionality with export integration
- Added export button position configuration
- support custom params for scopr

### 🏗 Architecture

- Introduced WithExport trait for export functionality
- Added DataTableExport class for Excel exports
- Improved PDF template system
- Better handling of data transformations

### 📚 Documentation

- Added export configuration documentation
- Updated pagination examples
- Added formatter integration examples
- New examples for customizing exports

## [Unreleased]

### 🚀 Planned for v1.4.0

- Column Filtering (dropdown, select, date range)
- Server-side Caching for heavy datasets
- Advanced Column Formatting
- Bulk Actions Support

### ⚡ Improvements

- Optimize queries on relationship-heavy tables
- Improve responsiveness for mobile
- Reduce Tailwind overhead in theme rendering

### 🛠 Developer Experience

- Add more unit tests for custom query & custom methods
- Better error messages on misconfigured `columns`/`model`
- Example playground project in repo

### 🔒 Security

- Optional CSRF protection for inline actions
- Config option to restrict searchable/sortable columns

---

## [v1.0.0] - 2025-08-10

### 🎉 Initial Release

- Live search with debouncing
- Column sorting (with relationship support)
- Dynamic pagination
- Fully customizable theming with TailwindCSS
- Dark mode support
- Responsive design
- Custom cell templates
- Event-driven architecture

### [v1.1.0] - 2025-08-24

- Serverside rendering
- Improvement performance with Computed Properties Livewire
- Dynamic pagination (add method simplePagination)
- make column no, and can sorting by number column
