<?php

namespace Developerawam\LivewireDatatable;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Developerawam\LivewireDatatable\Skeleton\SkeletonClass
 */
class LivewireDatatableFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'livewire-datatable';
    }
}
