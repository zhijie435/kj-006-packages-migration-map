<?php

namespace Shearerline\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Shearerline\Exceptions\UnauthorizedException;

class ShearerlineMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $request->attributes->set('shearerline', true);

        if (config('shearerline.auth.enabled', false)) {
            $this->authenticate($request);
        }

        return $next($request);
    }

    protected function authenticate(Request $request): void
    {
        $guard = config('shearerline.auth.guard', null);

        if ($guard) {
            if (!auth()->guard($guard)->check()) {
                throw new UnauthorizedException('', '用户未登录');
            }
        } else {
            if (!auth()->check()) {
                throw new UnauthorizedException('', '用户未登录');
            }
        }
    }
}
