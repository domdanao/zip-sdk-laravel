<?php

namespace Domdanao\ZipSdkLaravel\Facades;

use Illuminate\Support\Facades\Facade;

class PaymentLinkService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'zip-payment-link-service';
    }
}
