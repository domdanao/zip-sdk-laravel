<?php

namespace Domdanao\ZipSdkLaravel\Facades;

use Illuminate\Support\Facades\Facade;

class PaymentRequestService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'zip-payment-request-service';
    }
}
