<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
         'plan/paytm/*',
        'client/rajodiya/invoice/paytm/*',
        '*/invoice-pay-with-paymentwall/*',
        '*/invoice/iyzipay/*',
        '*/paytab-success/*',
        '*/aamarpay*'


    ];
}
