<?php

namespace App\Http\Middleware;

use Closure;

class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (env('APP_ENV') === 'production' && !env('APP_REVERSE_PROXY') && $request->header('X-FORWARDED-PROTO') !== 'https') {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}
