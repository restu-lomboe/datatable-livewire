# Changelog Improvement v3.0.0

Dokumentasi lengkap semua 10 improvement + integrasi `livewire/blaze`.

---

## #1 — Refactor: Hilangkan duplikasi logika filter

**File:** `src/Traits/WithFiltering.php`

**Masalah:** Logika filter yang identik ada di dua tempat:
- `DataTable::getQuery()` baris 180-210
- `WithExport::export()` baris 25-55

Jika ada bug di filter, harus diperbaiki di dua tempat sekaligus.

**Solusi:** Satu method `buildFilteredQuery(): Builder` di `WithFiltering` trait.
Keduanya `getQuery()` dan `export()` memanggil method ini.

```php
// Sebelum (duplikat di dua tempat):
foreach ($this->filterBy as $i => $column) {
    $value = trim($this->query[$i]) ?? null;
    if (!$value) continue;
    if (str_contains($column, '.')) { ... }
    else { $query->where($column, 'LIKE', "%{$value}%"); }
}

// Sesudah (satu tempat):
$query = $this->buildFilteredQuery(); // di WithFiltering
```

---

## #2 — Fitur Baru: WithSelection — Row Checkbox Selection

**File:** `src/Traits/WithSelection.php`

Aktifkan per-component:
```blade
<livewire:livewire-datatable :model="App\Models\User::class" :selectable="true" ... />
```

Gunakan event di parent component:
```php
#[On('datatable-selection-changed')]
public function onSelectionChanged(array $ids): void
{
    // $ids = array of selected primary keys
    $this->selectedUsers = $ids;
}
```

Method yang tersedia:
- `toggleSelect($id)` — toggle satu baris
- `toggleSelectAll()` — select/deselect semua baris di halaman ini
- `clearSelection()` — kosongkan semua pilihan
- `isSelected($id)` — cek apakah ID dipilih

---

## #3 — Bug Fix: Typo `showFiterButton` → `showFilterButton`

**File:** `src/Components/DataTable.php`

```php
// Sebelum:
public $showFiterButton = false;

// Sesudah:
public $showFilterButton = false;
/** @deprecated */ public $showFiterButton = false; // backward compat
```

Kedua property disync sehingga kode lama yang masih memakai `showFiterButton`
tidak rusak, tapi kode baru memakai nama yang benar.

---

## #4 — Performance: Cache `Schema::getColumnListing()`

**File:** `src/Traits/WithColumnSearch.php` → `getCachedColumnListing()`

**Masalah:** `filterByColumn` computed property memanggil `Schema::getColumnListing()`
yang query ke `information_schema` setiap kali komponen dirender.

**Solusi:** Cache hasil 5 menit (configurable):

```php
// config/livewire-datatable.php
'schema_cache_ttl' => 300, // detik. Set 0 untuk disable cache.
```

Cache key per-tabel, jadi tidak collision antar model.

---

## #5 — Fitur Baru: Per-Column Search

**File:** `src/Traits/WithColumnSearch.php`

Input search kecil di bawah setiap header kolom, sebagai alternatif/tambahan
dari global search.

Aktifkan:
```blade
<livewire:livewire-datatable :per-column-search="true" ... />
```

State tersimpan di `$columnSearch` array `['column' => 'term']`.
Search per-kolom digabungkan (AND) dengan filter aktif lainnya.

---

## #6 — Fitur Baru: Column Visibility Toggle

**File:** `src/Traits/WithColumnVisibility.php`

User bisa hide/show kolom via dropdown toggle. State persist ke session.
Minimal 1 kolom selalu visible (tidak bisa semua disembunyikan).

Aktifkan:
```blade
<livewire:livewire-datatable :column-visibility="true" ... />
```

Method:
- `toggleColumn('email')` — toggle kolom
- `showAllColumns()` — tampilkan semua
- `isColumnVisible('email')` — cek visibilitas
- `getVisibleColumns()` — dapat array kolom yang aktif

Blade memakai `$this->activeColumns` (computed) bukan `$columns` langsung.

---

## #7 — Smart Filter Operator (Cast-Aware)

**File:** `src/Traits/WithFiltering.php` → `resolveFilterOperator()` + `resolveFilterValue()`

Filter sekarang deteksi tipe cast dari model secara otomatis:
- `boolean`/`bool` → operator `=`, value di-cast ke `true/false`
- `integer`/`int` → operator `=`, value di-cast ke `int`
- `float`/`double` → operator `=`, value di-cast ke `float`
- Kolom lain → operator `LIKE`, value dibungkus `%value%`

Contoh: kolom `is_active` (cast boolean) akan filter `WHERE is_active = 1`
bukan `WHERE is_active LIKE '%1%'` yang hasilnya tidak akurat.

---

## #8 — Fitur Baru: WithSavedFilters — Preset Filter

**File:** `src/Traits/WithSavedFilters.php`

Simpan kombinasi filter + sort sebagai preset bernama. Dua driver tersedia.

**Driver: session** (default, tanpa setup tambahan)
```php
// config
'saved_filters' => ['enabled' => true, 'driver' => 'session'],
```

**Driver: database** (persist antar sesi, per-user)
```bash
php artisan vendor:publish --tag=livewire-datatable-migrations
php artisan migrate
```
```php
'saved_filters' => ['enabled' => true, 'driver' => 'database'],
```

Aktifkan per-component:
```blade
<livewire:livewire-datatable :saved-filters="true" ... />
```

Method:
- `saveFilterPreset()` — simpan filter saat ini
- `applyFilterPreset('nama')` — load preset
- `deleteFilterPreset('nama')` — hapus preset

---

## #9 — Fitur Baru: WithMultiSort — Multi-Column Sort

**File:** `src/Traits/WithMultiSort.php`

Sort berdasarkan beberapa kolom sekaligus.
Implementasi di Blade: kirim flag `ctrl` saat header di-klik.

```blade
{{-- Tailwind header cell --}}
<th wire:click="sortByMulti('{{ $col }}', $event.ctrlKey)">
    {{ $label }}
    @if($priority = $this->getSortPriority($col))
        <span class="badge">{{ $priority }}</span>
    @endif
</th>
```

- **Click biasa** → sort satu kolom (stack direset)
- **Ctrl+Click** → tambahkan ke stack (max 3 level)
- Badge angka menunjukkan prioritas (1 = primary, 2 = secondary, dst.)

```php
// Config
'multi_sort' => false, // default off
```

---

## Bonus — Integrasi `livewire/blaze`

**File:** `src/LivewireDatatableServiceProvider.php`

Blaze bekerja dengan mengkompilasi template menjadi fungsi PHP yang teroptimasi, mengurangi overhead rendering 91-97%.

### Install

```bash
composer require livewire/blaze:^1.0
php artisan view:clear
```

Blaze otomatis aktif saat terdeteksi. Tidak perlu konfigurasi tambahan.

### Strategi yang diterapkan

| View | Strategi | Alasan |
|------|----------|--------|
| `templates/tailwind/` | Standard compile | Dynamic: `wire:model`, loops, conditionals |
| `templates/bootstrap/` | Standard compile | Sama |
| `placeholders/templates/tailwind/` | `memo: true` | Skeleton sama, render berulang saat lazy polling |
| `placeholders/templates/bootstrap/` | `memo: true` | Sama |
| `exports/pdf.blade.php` | Dikecualikan | Berjalan via DomPDF, bukan Blade component stack |

### Disable Blaze (jika konflik)

```php
// config/livewire-datatable.php
'blaze' => ['enabled' => false],
```

### Extend di app kamu

Setelah publish views, optimasi views yang di-publish:
```php
// AppServiceProvider::boot()
use Livewire\Blaze\Blaze;

Blaze::optimize()
    ->in(resource_path('views/vendor/livewire-datatable/templates'), memo: true);
```

---

## Ringkasan File yang Berubah/Ditambah

```
src/
├── Components/
│   └── DataTable.php                    MODIFIED — semua 10 improvement
├── Traits/
│   ├── WithExport.php                   MODIFIED — #1 DRY (pakai buildFilteredQuery)
│   ├── WithFormatters.php               unchanged
│   ├── WithFiltering.php                NEW      — #1 centralized filter + #7 smart operator
│   ├── WithSelection.php                NEW      — #2 row checkboxes
│   ├── WithColumnVisibility.php         NEW      — #6 hide/show columns
│   ├── WithColumnSearch.php             NEW      — #4 schema cache + #5 per-col search
│   ├── WithSavedFilters.php             NEW      — #8 filter presets
│   └── WithMultiSort.php                NEW      — #9 multi-column sort
├── LivewireDatatableServiceProvider.php MODIFIED — Blaze integration
tests/
└── DataTableTest.php                    MODIFIED — #6 comprehensive coverage

database/
└── migrations/
    └── ..._create_datatable_filter_presets_table.php  NEW — #8 database driver
```
