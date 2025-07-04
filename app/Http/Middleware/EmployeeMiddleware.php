<?php
// php artisan make:middleware EmployeeMiddleware

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(403, 'Access denied. Authentication required.');
        }

        return $next($request);
    }
}