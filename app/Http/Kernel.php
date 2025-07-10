// app/Http/Kernel.php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // Other middleware
        \App\Http\Middleware\TrustProxies::class, // Add TrustProxies here
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        // Middleware groups
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        // Other middleware
        'verify.secret' => \App\Http\Middleware\VerifySecretKey::class,
    ];
}

// app/Http/Kernel.php
//protected $routeMiddleware = [
    // Other middleware
//    'verify.secret' => \App\Http\Middleware\VerifySecretKey::class,
//];

