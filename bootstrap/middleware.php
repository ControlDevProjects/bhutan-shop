<?php
// Add this to your bootstrap/app.php (Laravel 11) or Kernel.php (Laravel 10)
// 
// Laravel 11 - in bootstrap/app.php:
// ->withMiddleware(function (Middleware $middleware) {
//     $middleware->alias([
//         'auth.role' => \App\Http\Middleware\RoleMiddleware::class,
//     ]);
// })
//
// Laravel 10 - in app/Http/Kernel.php:
// protected $routeMiddleware = [
//     'auth.role' => \App\Http\Middleware\RoleMiddleware::class,
// ];
