<?php

namespace Shearerline\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ShearerlineMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $request->attributes->set('shearerline', true);

        return $next($request);
    }
}
