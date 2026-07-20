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
                __DIR__.'/../config/config.php' => config_path('livewire-datatable.php'),
            ], 'livewire-datatable-config');
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
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'livewire-datatable');

        // Register Excel Service Provider
        $this->app->register(ExcelServiceProvider::class);

        // Register DomPDF Service Provider
        $this->app->register(PDFServiceProvider::class);
    }
}
