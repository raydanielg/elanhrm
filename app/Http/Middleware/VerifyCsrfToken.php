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
        '*-webhook/*',
        '*_webhook/*',
        '*_webhook',
        '*-webhook',
        '/lead-form/leadStore',
        '/lead-form/ticket-store',
        '*/iclock/*',
        '/billing-verify-webhook/*',
        '*/payfast-notification/*'
    ];
}
