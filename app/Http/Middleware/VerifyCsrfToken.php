<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        "http://localhost:3000/*"
    ];

    public function handle($request, Closure $next)
    {
        $request->headers->set('Access-Control-Allow-Origin', 'http://localhost:8080');

        $referer = $request->headers->get('referer');
        if ($referer && strpos($referer, 'http://localhost:3000') === 0) {
            // Disable CSRF token verification for requests coming from React
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
