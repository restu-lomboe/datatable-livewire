<?php

namespace Developerawam\LivewireDatatable;

use Developerawam\LivewireDatatable\Skeleton\SkeletonClass;
use Illuminate\Support\Facades\Facade;

/**
 * @see SkeletonClass
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
