<?php

namespace Developerawam\LivewireDatatable;

use Illuminate\Support\ServiceProvider;

class LivewireDatatableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
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

        \Livewire\Livewire::component('livewire-datatable', \Developerawam\LivewireDatatable\Components\DataTable::class);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'livewire-datatable');
    }
}
