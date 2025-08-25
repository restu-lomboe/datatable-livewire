# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),  
and this project adheres to [Semantic Versioning](https://semver.org/).

---

## [Unreleased]

### 🚀 Planned for v1.2.0

- Column Filtering (dropdown, select, date range)
- Export to CSV/Excel/PDF
- Server-side Caching for heavy datasets
- Column Formatting (date, currency, custom display)

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
