<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role = null)
    {
		if (User::isAdmin()) {
			return $next($request);
		}

		abort(404);
    }
}
