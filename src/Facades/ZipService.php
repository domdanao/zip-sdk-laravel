<?php

namespace Domdanao\ZipSdkLaravel\Facades;

use Illuminate\Support\Facades\Facade;

class ZipService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'zip-service';
    }
}
