<?php

namespace Domdanao\ZipSdkLaravel\Facades;

use Illuminate\Support\Facades\Facade;

class ZipCheckoutService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'zip-checkout-service';
    }
}
