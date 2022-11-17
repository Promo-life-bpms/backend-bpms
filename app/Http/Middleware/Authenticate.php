<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if ($token = $request->cookie('cookie_token')) {
            $request->headers->set('Authorization', 'Bearer ' . $token,);
            $request->headers->get('Content-Type', 'application/json');
        }
        $this->authenticate($request, $guards);
        return $next($request);
    }
}
