<?php

namespace Developerawam\LivewireDatatable;

use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\ServiceProvider as PDFServiceProvider;
use Developerawam\LivewireDatatable\Components\DataTable;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Maatwebsite\Excel\ExcelServiceProvider;
use Maatwebsite\Excel\Facades\Excel;

class LivewireDatatableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'livewire-datatable');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/livewire-datatable'),
            ], 'livewire-datatable-views');

            $this->publishes([
                __DIR__.'/../config/livewire-datatable.php' => config_path('livewire-datatable.php'),
            ], 'livewire-datatable-config');

            // Backward compat: keep old config/config.php publishable if present
            if (file_exists(__DIR__.'/../config/config.php')) {
                $this->publishes([
                    __DIR__.'/../config/config.php' => config_path('livewire-datatable.php'),
                ], 'livewire-datatable-config-legacy');
            }
        }

        // Register Excel and PDF facades
        $this->app->alias('Excel', Excel::class);
        $this->app->alias('PDF', Pdf::class);

        // Register Livewire component
        Livewire::component('livewire-datatable', DataTable::class);
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Canonical config file
        $configPath = __DIR__.'/../config/livewire-datatable.php';

        // Fallback to legacy path for BC
        if (! file_exists($configPath) && file_exists(__DIR__.'/../config/config.php')) {
            $configPath = __DIR__.'/../config/config.php';
        }

        $this->mergeConfigFrom($configPath, 'livewire-datatable');

        // Facade accessor binding (keeps LivewireDatatable facade working)
        $this->app->singleton('livewire-datatable', function () {
            return new class
            {
                public function version(): string
                {
                    return '2.3.1';
                }
            };
        });

        // Register Excel Service Provider
        $this->app->register(ExcelServiceProvider::class);

        // Register DomPDF Service Provider
        $this->app->register(PDFServiceProvider::class);
    }
}
