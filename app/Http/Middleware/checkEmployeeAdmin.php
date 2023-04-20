<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkEmployeeAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->guard('admin')->check() || !auth()->guard('admin')->user()->isEmployee()){
            abort(404);
        }

        if (is_null(auth()->guard('admin')->user()->center_id)) {
            abort(404,'you are not assign to any center so you cannot manage this action');
        }

        return $next($request);
    }
}
