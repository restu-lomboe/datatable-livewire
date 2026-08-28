<?php

namespace Developerawam\LivewireDatatable;

use Developerawam\LivewireDatatable\Components\DataTable;
use Illuminate\Support\Facades\Facade;

/**
 * @see DataTable
 */
class LivewireDatatableFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'livewire-datatable';
    }
}
