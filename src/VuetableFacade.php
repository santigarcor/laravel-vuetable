<?php

namespace Vuetable\Facades;

use Illuminate\Support\Facades\Facade;

class VuetableFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'vuetable';
    }
}
