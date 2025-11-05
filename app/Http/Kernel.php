<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Aliases para middlewares (Laravel 12)
     */
    protected $middlewareAliases = [
        'auth'       => \App\Http\Middleware\Authenticate::class,
        'guest'      => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'verified'   => \App\Http\Middleware\EnsureEmailIsVerified::class,
        'throttle'   => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'signed'     => \Illuminate\Routing\Middleware\ValidateSignature::class,
        // 👇 nuestro alias
        'api.key'    => \App\Http\Middleware\ApiKeyMiddleware::class,
    ];
}
