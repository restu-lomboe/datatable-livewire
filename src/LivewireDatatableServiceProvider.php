<?php

namespace Developerawam\LivewireDatatable;

use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\ExcelServiceProvider;
use Barryvdh\DomPDF\ServiceProvider as PDFServiceProvider;
use Livewire\Livewire;

/**
 * Blaze Integration — PENTING: Scope yang benar
 *
 * livewire/blaze dirancang untuk anonymous Blade COMPONENTS (x-component),
 * BUKAN untuk Livewire component views secara langsung.
 *
 * ❌ SALAH: ->in('resources/views/livewire/...')
 *    Blaze akan mengkompilasi view Livewire sebagai Blade component biasa,
 *    yang bisa menghilangkan root <div> → error "missing root tag".
 *
 * ✅ BENAR: ->in('resources/views/components/...')
 *    Blaze mengoptimasi anonymous Blade components yang digunakan DI DALAM
 *    view Livewire. Livewire tetap merender viewnya sendiri secara normal.
 *
 * Untuk package ini, Blaze TIDAK dikonfigurasi otomatis karena view
 * datatable adalah Livewire views, bukan anonymous Blade components.
 * User dapat mengoptimasi anonymous components milik mereka sendiri secara manual.
 */
class LivewireDatatableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'livewire-datatable');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/livewire-datatable'),
            ], 'livewire-datatable-views');

            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('livewire-datatable.php'),
            ], 'livewire-datatable-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'livewire-datatable-migrations');
        }

        $this->app->alias('Excel', \Maatwebsite\Excel\Facades\Excel::class);
        $this->app->alias('PDF', \Barryvdh\DomPDF\Facade\Pdf::class);

        Livewire::component(
            'livewire-datatable',
            \Developerawam\LivewireDatatable\Components\DataTable::class
        );

        // NOTE: Blaze auto-registration DIHAPUS.
        // Blaze tidak boleh dikonfigurasi pada Livewire view files.
        // Lihat docs/BLAZE.md untuk panduan penggunaan Blaze yang benar.
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'livewire-datatable');
        $this->app->register(ExcelServiceProvider::class);
        $this->app->register(PDFServiceProvider::class);
    }
}
