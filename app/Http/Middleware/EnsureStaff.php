<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureStaff
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()?->isStaff()) {
            return redirect()->guest(route('admin.login'));
        }

        return $next($request);
    }
}
