# Panduan Integrasi livewire/blaze

## Mengapa Blaze tidak dikonfigurasi otomatis?

`livewire/blaze` dirancang khusus untuk **anonymous Blade components** (`x-component` syntax), **bukan** untuk Livewire component views secara langsung.

View file Livewire (seperti `resources/views/livewire/my-table.blade.php`) **berbeda** dengan anonymous Blade component (`resources/views/components/button.blade.php`).

Jika `Blaze::optimize()->in()` diarahkan ke Livewire views, Blaze akan mengkompilasi file tersebut sebagai Blade component biasa — proses ini bisa menghilangkan atau merusak root `<div>`, menyebabkan error:

```
Livewire encountered a missing root tag when trying to render a component.
```

---

## Cara Penggunaan Blaze yang Benar

Blaze **tetap memberikan manfaat** bagi performa Livewire, tapi hanya jika diarahkan ke anonymous Blade components yang dipanggil **di dalam** Livewire views.

### Contoh struktur yang tepat

```
resources/views/
├── livewire/           ← ❌ JANGAN arahkan Blaze ke sini
│   └── users-table.blade.php
└── components/         ← ✅ Arahkan Blaze ke sini
    ├── button.blade.php
    ├── badge.blade.php
    └── table/
        ├── status-badge.blade.php
        └── action-buttons.blade.php
```

### Konfigurasi di AppServiceProvider

```php
// app/Providers/AppServiceProvider.php

use Livewire\Blaze\Blaze;

public function boot(): void
{
    if (class_exists(Blaze::class)) {
        Blaze::optimize()
            // ✅ Blade anonymous components — aman dioptimasi dengan Blaze
            ->in(resource_path('views/components'))

            // ✅ Komponen ikon yang dirender banyak kali — cocok untuk memo
            ->in(resource_path('views/components/icons'), memo: true);

        // ❌ JANGAN lakukan ini:
        // ->in(resource_path('views/livewire'))
        // ->in(resource_path('views/vendor/livewire-datatable'))
    }
}
```

### Memo vs Fold

| Strategi | Kapan dipakai | Risiko |
|---|---|---|
| Standard compile | Komponen dengan props dinamis | Rendah |
| `memo: true` | Komponen yang dirender berulang kali dengan input sama (ikon, badge statis) | Sedang — jangan pakai untuk komponen dengan global state |
| `fold: true` | Komponen yang 100% statis, tidak ada variabel runtime | Tinggi — baca dokumentasi Blaze sebelum pakai |

---

## Referensi

- [livewire/blaze GitHub](https://github.com/livewire/blaze)
- [Blaze Packagist](https://packagist.org/packages/livewire/blaze)
