# Changelog

All notable changes to this project will be documented in this file.

---

## [v3.0.1] — 2026-05-05

### 🐛 Bug Fixes

- **State reset pada setiap Livewire re-render** — Semua fitur baru (WithSelection, WithColumnSearch, WithColumnVisibility, WithMultiSort, WithSavedFilters, WithExport) memiliki `boot*()` method yang menimpa nilai public property dari config setiap kali component dirender ulang. Ini menyebabkan fitur seperti checkbox selection, per-column search, column visibility, dan multi-sort hilang setelah setiap interaksi (sort, search, pagination). **Fix:** Semua `boot*()` method dihapus. Feature flags (`$selectable`, `$perColumnSearch`, `$columnVisibility`, `$multiSort`, `$savedFilters`) kini hanya diset **sekali** di `mount()`, dan Livewire mempertahankan nilainya secara otomatis via state hydration antar request.

---

## [v3.0.0] — 2026-05-05

### ✨ New Traits / Features

- **WithFiltering** — Centralized filter logic (#1 DRY refactor + #7 smart cast-aware operator)
- **WithSelection** — Row checkbox selection with select-all and `datatable-selection-changed` event (#2)
- **WithColumnSearch** — Schema cache via `Cache::remember` + per-column search inputs (#4 + #5)
- **WithColumnVisibility** — Dynamic show/hide columns, state persisted to session (#6)
- **WithSavedFilters** — Save/load named filter presets via session or database driver (#8)
- **WithMultiSort** — Multi-column sort via Ctrl+Click, up to 3 levels with priority badges (#9)

### 🔧 Bug Fixes

- **#3** — Typo `$showFiterButton` → `$showFilterButton` fixed; old property kept as deprecated alias for backward compat
- **#4** — `Schema::getColumnListing()` now cached (default 5 min) instead of hitting DB on every render
- **#1** — `WithExport::export()` no longer duplicates filter logic; uses `buildFilteredQuery()` from `WithFiltering`
- **#7** — Filter operator now cast-aware: boolean/int columns use `=` instead of `LIKE`

### 🚀 Performance

- **Blaze integration** — `LivewireDatatableServiceProvider` auto-registers Blaze optimizations when `livewire/blaze` is installed
  - Main templates: standard compile (91-97% overhead reduction)
  - Placeholder views: `memo: true` (memoized for lazy-load polling)

### 📦 New Files

```
src/Traits/WithFiltering.php
src/Traits/WithSelection.php
src/Traits/WithColumnSearch.php
src/Traits/WithColumnVisibility.php
src/Traits/WithSavedFilters.php
src/Traits/WithMultiSort.php
database/migrations/..._create_datatable_filter_presets_table.php
docs/IMPROVEMENTS.md
```

### 🔄 Modified Files

```
src/Components/DataTable.php
src/Traits/WithExport.php
src/LivewireDatatableServiceProvider.php
resources/views/templates/tailwind/datatable.blade.php
resources/views/templates/bootstrap/datatable.blade.php
composer.json
config/config.php
tests/DataTableTest.php
```

---

## [v2.1.0] — 2024-12-11

### ✨ New Features
- Advanced Dynamic Filter System
- Default Sort Configuration
- Fully Dynamic CSS Classes
- Enhanced Export with Filtering
