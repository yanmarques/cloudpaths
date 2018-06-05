<?php

namespace Cloudpaths\Facades;

use Illuminate\Support\Facades\Facade;

class Cloudpaths extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Cloudpaths';
    }
}
